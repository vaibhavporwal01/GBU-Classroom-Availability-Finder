<?php
/**
 * next_free.php — Returns all free periods for a given room on a given day.
 * Reads raw HTML from session cache (populated by check.php) to avoid re-fetching.
 * GET ?room_id=203&day=Monday
 */
require_once 'rooms.php';
require_once 'check.php'; // needed for parseRoomHtml()

header('Content-Type: application/json');

$validDays = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
$periods   = ['I','II','III','IV','V','VI','VII','VIII','IX','X'];

$roomId = (int)($_GET['room_id'] ?? 0);
$day    = trim($_GET['day'] ?? '');
$rooms  = getRooms();

if (!isset($rooms[$roomId]) || !in_array($day, $validDays, true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid room or day']);
    exit;
}

$dayIndex = array_search($day, $validDays) + 1;

// Try session cache first
@session_start(['cookie_httponly' => true, 'cookie_samesite' => 'Strict']);
$html = $_SESSION['room_html'][$roomId] ?? null;

// Fall back to a fresh fetch if not cached
if ($html === null || $html === '') {
    $url = 'https://mygbu.in/schd/rindex.php?id=' . $roomId;
    $ch  = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $html = (string)(curl_exec($ch) ?: '');
    curl_close($ch);
}

$freePeriods = [];
foreach ($periods as $i => $period) {
    $result = parseRoomHtml($html, $dayIndex, $i + 1);
    if ($result === 'free') {
        $freePeriods[] = $period;
    }
}

echo json_encode([
    'success'      => true,
    'room'         => $rooms[$roomId],
    'day'          => $day,
    'free_periods' => $freePeriods,
]);
exit;
