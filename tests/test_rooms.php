<?php

/**
 * tests/test_rooms.php — Unit tests for getSchoolForRoom()
 * Uses plain PHP assertions. Outputs PASS/FAIL per test.
 * Exits with code 1 if any test fails.
 */

require_once __DIR__ . '/../rooms.php';

$failures = 0;

/**
 * Assert helper: prints PASS/FAIL and increments $failures on failure.
 */
function assertTest(string $description, bool $condition, &$failures): void
{
    if ($condition) {
        echo "PASS: {$description}\n";
    } else {
        echo "FAIL: {$description}\n";
        $failures++;
    }
}

// ── Specific room tests ────────────────────────────────────────────────────

assertTest(
    "IL202 → SOICT",
    getSchoolForRoom('IL202') === 'SOICT',
    $failures
);

assertTest(
    "BTLab1 → SOBT (not misclassified via BT prefix)",
    getSchoolForRoom('BTLab1') === 'SOBT',
    $failures
);

assertTest(
    "V225 → Common",
    getSchoolForRoom('V225') === 'Common',
    $failures
);

assertTest(
    "LH101 → Common",
    getSchoolForRoom('LH101') === 'Common',
    $failures
);

// ── All 85 rooms return a valid, non-empty school string ───────────────────

$validSchools = ['SOICT', 'SOE', 'SOBT', 'SOVS/AS', 'Common'];
$rooms        = getRooms();

assertTest(
    "getRooms() returns exactly 85 entries",
    count($rooms) === 85,
    $failures
);

foreach ($rooms as $id => $name) {
    $school = getSchoolForRoom($name);

    assertTest(
        "Room {$name} (id={$id}) returns non-null, non-empty school string",
        $school !== null && $school !== '',
        $failures
    );

    assertTest(
        "Room {$name} (id={$id}) school '{$school}' is in the defined set",
        in_array($school, $validSchools, true),
        $failures
    );
}

// ── Summary ────────────────────────────────────────────────────────────────

echo "\n";
if ($failures === 0) {
    echo "All tests passed.\n";
    exit(0);
} else {
    echo "{$failures} test(s) failed.\n";
    exit(1);
}
