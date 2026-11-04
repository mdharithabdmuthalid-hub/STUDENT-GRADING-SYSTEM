<?php
/**
 * calculate.php
 * - Grade scale + calculation functions (used by add.php and edit.php)
 * - If opened directly in the browser: recalculates total marks and grade
 *   for every row in the database, then goes back to index.php
 */

// ---------------------------------------------------------------
// 1) GRADE SCALE (change the numbers here if needed)
// ---------------------------------------------------------------
const GRADE_SCALE = [
    90 => 'A+',
    80 => 'A',
    75 => 'A-',
    70 => 'B+',
    65 => 'B',
    60 => 'B-',
    50 => 'C',
    40 => 'D',
    30 => 'E',
    0  => 'F',
];

// ---------------------------------------------------------------
// 2) FUNCTIONS
// ---------------------------------------------------------------

/** Returns the letter grade for a total mark (0-100). */
function grade_from_total(float $total): string
{
    foreach (GRADE_SCALE as $minimum => $grade) {
        if ($total >= $minimum) {
            return $grade;
        }
    }

    return 'F';
}

/**
 * Test 1 (max 20) + Test 2 (max 30) + Final exam (max 50) = total out of 100.
 * Returns ['total_marks' => float, 'grade' => string].
 */
function calculate_result(float $test1, float $test2, float $finalExam): array
{
    $total = round($test1 + $test2 + $finalExam, 2);

    return [
        'total_marks' => $total,
        'grade'       => grade_from_total($total),
    ];
}

// If another file includes this one (add.php / edit.php), stop here.
// Only the functions above are needed in that case.
if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') !== __FILE__) {
    return;
}

// ---------------------------------------------------------------
// 3) OPENED DIRECTLY: recalculate all saved results
// ---------------------------------------------------------------

// Must be logged in
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (empty($_SESSION['authenticated'])) {
    header('Location: login.php');
    exit;
}

// Connect to the database (same settings as your project)
$conn = new mysqli('localhost', 'root', '', 'student_data');
if ($conn->connect_error) {
    http_response_code(500);
    exit('Connection failed: ' . htmlspecialchars($conn->connect_error, ENT_QUOTES, 'UTF-8'));
}
$conn->set_charset('utf8mb4');

// Read every result
$rows = $conn->query('SELECT id, test1, test2, final_exam FROM results');
if ($rows === false) {
    http_response_code(500);
    exit('Query error: ' . htmlspecialchars($conn->error, ENT_QUOTES, 'UTF-8'));
}

// Prepare the update
$update = $conn->prepare('UPDATE results SET total_marks = ?, grade = ? WHERE id = ?');
if ($update === false) {
    http_response_code(500);
    exit('Prepare error: ' . htmlspecialchars($conn->error, ENT_QUOTES, 'UTF-8'));
}

// Recalculate each row
while ($row = $rows->fetch_assoc()) {
    $calc = calculate_result(
        (float) $row['test1'],
        (float) $row['test2'],
        (float) $row['final_exam']
    );
    $id = (int) $row['id'];
    $update->bind_param('dsi', $calc['total_marks'], $calc['grade'], $id);
    $update->execute();
}

$update->close();
$conn->close();

header('Location: index.php');
exit;