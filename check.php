<?php

/**
 * check.php — Backend Scraper, Parser, and Input Validator
 * Accepts POST parameters `day` and `period`, validates input,
 * scrapes all 85 rooms in parallel, and returns a JSON response.
 */

require_once 'rooms.php';

// ---------------------------------------------------------------------------
// Input Validation
// ---------------------------------------------------------------------------

/**
 * Validates that $day and $period are within the defined whitelists.
 *
 * @param string $day    Must be one of: Monday, Tuesday, Wednesday, Thursday, Friday, Saturday
 * @param string $period Must be one of: I, II, III, IV, V, VI, VII, VIII, IX, X
 * @return bool          true only if both values are in their respective whitelists
 */
function validateInput(string $day, string $period): bool
{
    $validDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    $validPeriods = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X'];

    return in_array($day, $validDays, true) && in_array($period, $validPeriods, true);
}

// ---------------------------------------------------------------------------
// Index Mapping
// ---------------------------------------------------------------------------

/**
 * Maps a valid day name to its 1-based index.
 * Precondition: $day has been validated by validateInput().
 *
 * @param string $day  One of: Monday, Tuesday, Wednesday, Thursday, Friday, Saturday
 * @return int         1 (Monday) through 6 (Saturday)
 */
function mapDayToIndex(string $day): int
{
    $map = [
        'Monday'    => 1,
        'Tuesday'   => 2,
        'Wednesday' => 3,
        'Thursday'  => 4,
        'Friday'    => 5,
        'Saturday'  => 6,
    ];

    return $map[$day];
}

/**
 * Maps a valid period name to its 1-based index.
 * Precondition: $period has been validated by validateInput().
 *
 * @param string $period  One of: I, II, III, IV, V, VI, VII, VIII, IX, X
 * @return int            1 (I) through 10 (X)
 */
function mapPeriodToIndex(string $period): int
{
    $map = [
        'I'    => 1,
        'II'   => 2,
        'III'  => 3,
        'IV'   => 4,
        'V'    => 5,
        'VI'   => 6,
        'VII'  => 7,
        'VIII' => 8,
        'IX'   => 9,
        'X'    => 10,
    ];

    return $map[$period];
}

// ---------------------------------------------------------------------------
// Cache Key
// ---------------------------------------------------------------------------

/**
 * Returns the session cache key for a given day and period.
 *
 * @param string $day    e.g. "Monday"
 * @param string $period e.g. "III"
 * @return string        e.g. "avail_Monday_III"
 */
function getCacheKey(string $day, string $period): string
{
    return 'avail_' . $day . '_' . $period;
}

// ---------------------------------------------------------------------------
// HTML Parser
// ---------------------------------------------------------------------------

/**
 * Parses a room's timetable HTML to determine occupancy for a given day and period.
 *
 * @param string $html        Raw HTML from mygbu.in room timetable page
 * @param int    $dayIndex    Day index 1–6 (Monday=1, Saturday=6)
 * @param int    $periodIndex Period index 1–10 (I=1, X=10)
 * @return string|array       'free' if the room is free, or an associative array
 *                            ['subject' => ..., 'section' => ..., 'teacher' => ...]
 *                            if occupied. Never throws.
 */
function parseRoomHtml(string $html, int $dayIndex, int $periodIndex)
{
    // Empty or null input — return safe default
    if ($html === '' || $html === null) {
        return ['subject' => 'N/A', 'section' => 'N/A', 'teacher' => 'N/A'];
    }

    // Match the row for the target day
    $dayPattern = '/<tr[^>]*class=[\'"]lesson_' . $dayIndex . '[\'"][^>]*>(.*?)<\/tr>/si';
    if (!preg_match($dayPattern, $html, $dayMatch)) {
        return 'free';
    }

    $dayRowHtml = $dayMatch[1];

    // Match the target period cell
    $cellPattern = '/<td[^>]*class=[\'"]lesson_cell day_' . $periodIndex . '[\'"][^>]*>(.*?)<\/td>/si';
    if (!preg_match($cellPattern, $dayRowHtml, $cellMatch)) {
        return 'free';
    }

    // Strip tags keeping <br> and <a>, then trim
    $cellHtml = strip_tags($cellMatch[1], '<br><a>');
    $cellText = trim($cellHtml);

    if ($cellText === '') {
        return 'free';
    }

    // Try to match the occupied pattern: SUBJECT(SECTION)<br>SUBJECT(<a>FACULTY</a>)
    $occupiedPattern = '/([A-Z0-9]+)\(([^)]+)\)\s*<br\s*\/?>\s*[A-Z0-9]+\(<a[^>]*>([^<]+)<\/a>\)/i';
    if (preg_match($occupiedPattern, $cellText, $occMatch)) {
        return [
            'subject' => $occMatch[1],
            'section' => $occMatch[2],
            'teacher' => $occMatch[3],
        ];
    }

    // Cell has content but didn't match expected pattern
    return ['subject' => 'Unknown', 'section' => '—', 'teacher' => 'Unknown'];
}

// ---------------------------------------------------------------------------
// Parallel Scraper
// ---------------------------------------------------------------------------

/**
 * Scrapes all rooms concurrently using curl_multi and returns free/occupied lists.
 *
 * @param array $rooms        Associative array [id => name] from getRooms()
 * @param int   $dayIndex     Day index 1–6 (Monday=1, Saturday=6)
 * @param int   $periodIndex  Period index 1–10 (I=1, X=10)
 * @return array              ['free' => [...], 'occupied' => [...]]
 *                            Optionally includes 'all_failed' => true if every request failed.
 */
function scrapeAllRooms(array $rooms, int $dayIndex, int $periodIndex): array
{
    $mh      = curl_multi_init();
    $handles = [];

    // Initialise one handle per room — URLs built exclusively from hardcoded IDs
    foreach ($rooms as $roomId => $roomName) {
        $url = 'https://mygbu.in/schd/rindex.php?id=' . (int) $roomId;
        $ch  = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_multi_add_handle($mh, $ch);
        $handles[$roomId] = $ch;
    }

    // Execute all handles concurrently — no busy-waiting
    $running = 0;
    do {
        $status = curl_multi_exec($mh, $running);
        if ($running > 0) {
            curl_multi_select($mh);
        }
    } while ($running > 0);

    $free      = [];
    $occupied  = [];
    $failCount = 0;
    $htmlCache = []; // room_id => raw html, for next_free.php reuse

    foreach ($handles as $roomId => $ch) {
        $html     = curl_multi_getcontent($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_errno($ch);

        // Store raw HTML for session cache (used by next_free.php)
        $htmlCache[$roomId] = (string)($html ?? '');

        // Treat failed/empty/non-200 responses as free rooms
        $failed = ($curlErr !== 0 || $httpCode !== 200 || $html === '' || $html === null);
        if ($failed) {
            $failCount++;
        }

        $roomName = $rooms[$roomId];
        $school   = getSchoolForRoom($roomName);
        $url      = 'https://mygbu.in/schd/rindex.php?id=' . (int) $roomId;

        if ($failed) {
            $free[] = [
                'id'     => (int) $roomId,
                'name'   => htmlspecialchars($roomName, ENT_QUOTES, 'UTF-8'),
                'school' => htmlspecialchars($school, ENT_QUOTES, 'UTF-8'),
                'url'    => htmlspecialchars($url, ENT_QUOTES, 'UTF-8'),
            ];
        } else {
            $parsed = parseRoomHtml((string) $html, $dayIndex, $periodIndex);

            if (is_array($parsed)) {
                // Occupied room
                $occupied[] = [
                    'id'      => (int) $roomId,
                    'name'    => htmlspecialchars($roomName, ENT_QUOTES, 'UTF-8'),
                    'school'  => htmlspecialchars($school, ENT_QUOTES, 'UTF-8'),
                    'url'     => htmlspecialchars($url, ENT_QUOTES, 'UTF-8'),
                    'subject' => htmlspecialchars((string) $parsed['subject'], ENT_QUOTES, 'UTF-8'),
                    'section' => htmlspecialchars((string) $parsed['section'], ENT_QUOTES, 'UTF-8'),
                    'teacher' => htmlspecialchars((string) $parsed['teacher'], ENT_QUOTES, 'UTF-8'),
                ];
            } else {
                // 'free' string
                $free[] = [
                    'id'     => (int) $roomId,
                    'name'   => htmlspecialchars($roomName, ENT_QUOTES, 'UTF-8'),
                    'school' => htmlspecialchars($school, ENT_QUOTES, 'UTF-8'),
                    'url'    => htmlspecialchars($url, ENT_QUOTES, 'UTF-8'),
                ];
            }
        }

        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);
    }

    curl_multi_close($mh);

    $result = ['free' => $free, 'occupied' => $occupied, 'html_cache' => $htmlCache];

    // Warn if every single request failed
    if ($failCount === count($rooms)) {
        $result['all_failed'] = true;
    }

    return $result;
}

// ---------------------------------------------------------------------------
// Session Cache + Availability Fetch
// ---------------------------------------------------------------------------

/**
 * Returns availability data for the given day and period, using the session
 * cache when available. Gracefully degrades if the session cannot be started.
 *
 * @param string $day    Validated day name (e.g. "Monday")
 * @param string $period Validated period name (e.g. "III")
 * @param array  $rooms  Full room map from getRooms()
 * @return array         Result array with keys: free, occupied, cached.
 *                       May also contain all_failed => true.
 */
function getOrFetchAvailability(string $day, string $period, array $rooms): array
{
    // Attempt to start the session with secure cookie options.
    // Suppress errors and degrade gracefully if it fails.
    $sessionStarted = false;
    try {
        $sessionStarted = @session_start([
            'cookie_httponly' => true,
            'cookie_samesite' => 'Strict',
        ]);
    } catch (\Throwable $e) {
        $sessionStarted = false;
    }

    $cacheKey = getCacheKey($day, $period);

    // Cache hit — return stored result with cached flag set to true
    if ($sessionStarted && isset($_SESSION[$cacheKey])) {
        $result           = $_SESSION[$cacheKey];
        $result['cached'] = true;
        return $result;
    }

    // Cache miss — scrape live data
    $dayIndex    = mapDayToIndex($day);
    $periodIndex = mapPeriodToIndex($period);
    $result      = scrapeAllRooms($rooms, $dayIndex, $periodIndex);
    $result['cached'] = false;

    // Store raw HTML cache in session separately (for next_free.php)
    if ($sessionStarted && isset($result['html_cache'])) {
        $_SESSION['room_html'] = $result['html_cache'];
    }
    unset($result['html_cache']);

    // Only cache if not all requests failed
    if ($sessionStarted && empty($result['all_failed'])) {
        $_SESSION[$cacheKey] = $result;
    }

    return $result;
}

// ---------------------------------------------------------------------------
// Entry Point
// ---------------------------------------------------------------------------

// Only run the entry point when this file is the main script (not when included by tests)
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    header('Content-Type: application/json');

    $day    = $_POST['day']    ?? '';
    $period = $_POST['period'] ?? '';

    if (!validateInput($day, $period)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid day or period']);
        exit;
    }

    $rooms  = getRooms();
    $result = getOrFetchAvailability($day, $period, $rooms);

    $response = [
        'success'  => true,
        'day'      => $day,
        'period'   => $period,
        'free'     => $result['free'],
        'occupied' => $result['occupied'],
        'cached'   => $result['cached'],
    ];

    if (!empty($result['all_failed'])) {
        $response['warning'] = 'All room requests failed';
    }

    echo json_encode($response);
    exit;
}
