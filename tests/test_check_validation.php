<?php

/**
 * tests/test_check_validation.php
 * Unit tests for validateInput() in check.php.
 *
 * Validates: Requirements 8.1, 8.2, 8.3, 8.4, 1.4
 *
 * Outputs PASS/FAIL for each test and exits with code 1 if any test fails.
 */

// Provide a fake SCRIPT_FILENAME so check.php does not execute its entry point.
$_SERVER['SCRIPT_FILENAME'] = __FILE__;

require_once __DIR__ . '/../check.php';

// ---------------------------------------------------------------------------
// Test runner helpers
// ---------------------------------------------------------------------------

$failures = 0;

function assert_true(bool $result, string $label): void
{
    global $failures;
    if ($result) {
        echo "PASS: {$label}\n";
    } else {
        echo "FAIL: {$label}\n";
        $failures++;
    }
}

// ---------------------------------------------------------------------------
// 1. All valid day+period combinations → true  (6 × 10 = 60 tests)
// ---------------------------------------------------------------------------

$validDays    = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
$validPeriods = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X'];

foreach ($validDays as $day) {
    foreach ($validPeriods as $period) {
        assert_true(
            validateInput($day, $period) === true,
            "validateInput('{$day}', '{$period}') === true"
        );
    }
}

// ---------------------------------------------------------------------------
// 2. Invalid inputs → false
// ---------------------------------------------------------------------------

// Empty string day
assert_true(
    validateInput('', 'I') === false,
    "validateInput('', 'I') === false  [empty day]"
);

// Empty string period
assert_true(
    validateInput('Monday', '') === false,
    "validateInput('Monday', '') === false  [empty period]"
);

// Whitespace-only day
assert_true(
    validateInput(' ', 'I') === false,
    "validateInput(' ', 'I') === false  [whitespace day]"
);

// Whitespace-only period
assert_true(
    validateInput('Monday', ' ') === false,
    "validateInput('Monday', ' ') === false  [whitespace period]"
);

// SQL injection in day
assert_true(
    validateInput("Monday'; DROP TABLE rooms;--", 'I') === false,
    "validateInput(SQL injection day) === false"
);

// SQL injection in period
assert_true(
    validateInput('Monday', "I'; DROP TABLE rooms;--") === false,
    "validateInput(SQL injection period) === false"
);

// Out-of-range day: Sunday
assert_true(
    validateInput('Sunday', 'I') === false,
    "validateInput('Sunday', 'I') === false  [Sunday not valid]"
);

// Out-of-range period: XI
assert_true(
    validateInput('Monday', 'XI') === false,
    "validateInput('Monday', 'XI') === false  [XI out of range]"
);

// Numeric string for day
assert_true(
    validateInput('1', 'I') === false,
    "validateInput('1', 'I') === false  [numeric day]"
);

// Numeric string for period
assert_true(
    validateInput('Monday', '1') === false,
    "validateInput('Monday', '1') === false  [numeric period]"
);

// ---------------------------------------------------------------------------
// Summary
// ---------------------------------------------------------------------------

$total = 60 + 10; // 60 valid combos + 10 invalid cases
echo "\n";
if ($failures === 0) {
    echo "All {$total} tests passed.\n";
    exit(0);
} else {
    echo "{$failures} of {$total} tests FAILED.\n";
    exit(1);
}
