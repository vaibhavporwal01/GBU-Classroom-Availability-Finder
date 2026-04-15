<?php

/**
 * Unit tests for parseRoomHtml()
 * Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6
 *
 * Run: php tests/test_parse_room_html.php
 * Exits with code 1 if any test fails.
 */

require_once __DIR__ . '/../check.php';

$passed = 0;
$failed = 0;

function assert_test(string $name, bool $condition): void
{
    global $passed, $failed;
    if ($condition) {
        echo "PASS: $name\n";
        $passed++;
    } else {
        echo "FAIL: $name\n";
        $failed++;
    }
}

// ---------------------------------------------------------------------------
// Test 1: Empty string → returns array with 'N/A' values (no exception)
// Requirements: 2.3
// ---------------------------------------------------------------------------
$result = parseRoomHtml('', 1, 1);
assert_test(
    'Empty string returns N/A array',
    is_array($result)
    && $result['subject'] === 'N/A'
    && $result['section'] === 'N/A'
    && $result['teacher'] === 'N/A'
);

// ---------------------------------------------------------------------------
// Test 2: Valid free cell (empty <td>) → returns 'free'
// Requirements: 2.2
// ---------------------------------------------------------------------------
$freeHtml = "<table><tr class='lesson_1'><td class='lesson_cell day_1'></td></tr></table>";
$result = parseRoomHtml($freeHtml, 1, 1);
assert_test(
    'Empty lesson cell returns free',
    $result === 'free'
);

// ---------------------------------------------------------------------------
// Test 3: Valid occupied cell with full pattern → correct {subject, section, teacher}
// Requirements: 2.1, 2.6
// ---------------------------------------------------------------------------
$occupiedHtml = "<table>
  <tr class='lesson_1'>
    <td class='lesson_cell day_1'>CS301(A)<br>CS301(<a href='#'>Dr. Sharma</a>)</td>
  </tr>
</table>";
$result = parseRoomHtml($occupiedHtml, 1, 1);
assert_test(
    'Occupied cell returns correct subject',
    is_array($result) && $result['subject'] === 'CS301'
);
assert_test(
    'Occupied cell returns correct section',
    is_array($result) && $result['section'] === 'A'
);
assert_test(
    'Occupied cell returns correct teacher',
    is_array($result) && $result['teacher'] === 'Dr. Sharma'
);

// ---------------------------------------------------------------------------
// Test 4: Malformed HTML → returns 'free' (no exception)
// Requirements: 2.4
// ---------------------------------------------------------------------------
$malformedHtml = '<tr><td><<<broken>>>';
$result = parseRoomHtml($malformedHtml, 1, 1);
assert_test(
    'Malformed HTML returns free without exception',
    $result === 'free'
);

// ---------------------------------------------------------------------------
// Test 5: Cell with content but no faculty link → teacher === 'Unknown'
// Requirements: 2.5
// ---------------------------------------------------------------------------
$noLinkHtml = "<table>
  <tr class='lesson_1'>
    <td class='lesson_cell day_1'>CS301(A)<br>CS301(Dr. Sharma)</td>
  </tr>
</table>";
$result = parseRoomHtml($noLinkHtml, 1, 1);
assert_test(
    'Cell without faculty link returns Unknown teacher',
    is_array($result) && $result['teacher'] === 'Unknown'
);

// ---------------------------------------------------------------------------
// Test 6: HTML with no matching day row → returns 'free'
// Requirements: 2.2
// ---------------------------------------------------------------------------
$wrongDayHtml = "<table>
  <tr class='lesson_3'>
    <td class='lesson_cell day_1'>CS301(A)<br>CS301(<a href='#'>Dr. Sharma</a>)</td>
  </tr>
</table>";
$result = parseRoomHtml($wrongDayHtml, 1, 1);
assert_test(
    'No matching day row returns free',
    $result === 'free'
);

// ---------------------------------------------------------------------------
// Summary
// ---------------------------------------------------------------------------
echo "\n";
echo "Results: $passed passed, $failed failed\n";

if ($failed > 0) {
    exit(1);
}
exit(0);
