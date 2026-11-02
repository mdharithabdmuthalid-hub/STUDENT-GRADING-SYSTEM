<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/config/db.php';

$errors = [];
$values = [
    'student_name' => '',
    'matric_no' => '',
    'subject_name' => '',
    'subject_code' => '',
    'test1' => '',
    'test2' => '',
    'final_exam' => '',
    'grade' => '',
];
$totalMarks = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($values as $field => $default) {
        $postedValue = $_POST[$field] ?? '';
        if (!is_string($postedValue)) {
            $errors[] = 'Please provide valid values for all fields.';
            continue;
        }

        $values[$field] = trim($postedValue);
    }

    foreach (['student_name', 'matric_no', 'subject_name', 'subject_code', 'grade'] as $field) {
        if ($values[$field] === '') {
            $errors[] = 'Please fill in all fields.';
            break;
        }
    }

    $scoreLimits = [
        'test1' => 20,
        'test2' => 30,
        'final_exam' => 50,
    ];
    $scores = [];

    foreach ($scoreLimits as $field => $maximum) {
        if ($values[$field] === '' || !is_numeric($values[$field])) {
            $errors[] = 'Enter a valid score for each assessment.';
            continue;
        }

        $score = (float) $values[$field];
        if (!is_finite($score) || $score < 0 || $score > $maximum) {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . " must be between 0 and {$maximum}.";
            continue;
        }

        $scores[$field] = $score;
    }

    if (count($scores) === count($scoreLimits)) {
        $totalMarks = array_sum($scores);
    }

    if ($errors === []) {
        $stmt = $conn->prepare(
            'INSERT INTO results (student_name, matric_no, subject_name, subject_code, test1, test2, final_exam, total_marks, grade)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        if ($stmt === false) {
            die('Unable to prepare the student result: ' . htmlspecialchars($conn->error, ENT_QUOTES, 'UTF-8'));
        }

        $stmt->bind_param(
            'ssssdddds',
            $values['student_name'],
            $values['matric_no'],
            $values['subject_name'],
            $values['subject_code'],
            $scores['test1'],
            $scores['test2'],
            $scores['final_exam'],
            $totalMarks,
            $values['grade']
        );

        if (!$stmt->execute()) {
            die('Unable to save the student result: ' . htmlspecialchars($stmt->error, ENT_QUOTES, 'UTF-8'));
        }

        header('Location: index.php');
        exit;
    }
}

$escape = static fn(string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
$pageTitle = 'Add Student Result';
$pageDescription = 'Enter the student and assessment details below.';
$pageBackUrl = 'index.php';
$pageBackLabel = '← Back to results';
include __DIR__ . '/templates/header.php';
?>

        <?php if ($errors !== []): ?>
            <div class="form-errors" role="alert">
                <p>Please check the form:</p>
                <ul>
                    <?php foreach (array_unique($errors) as $error): ?>
                        <li><?= $escape($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <section class="results-card add-result-card" aria-label="New student result">
            <form class="result-form" method="post" action="add.php">
                <div class="form-section">
                    <h2>Student details</h2>
                    <div class="form-grid">
                        <div class="form-field">
                            <label for="student_name">Student name</label>
                            <input type="text" id="student_name" name="student_name"
                                value="<?= $escape($values['student_name']) ?>" required>
                        </div>
                        <div class="form-field">
                            <label for="matric_no">Matriculation number</label>
                            <input type="text" id="matric_no" name="matric_no"
                                value="<?= $escape($values['matric_no']) ?>" required>
                        </div>
                        <div class="form-field">
                            <label for="subject_name">Subject</label>
                            <input type="text" id="subject_name" name="subject_name"
                                value="<?= $escape($values['subject_name']) ?>" required>
                        </div>
                        <div class="form-field">
                            <label for="subject_code">Subject code</label>
                            <input type="text" id="subject_code" name="subject_code"
                                value="<?= $escape($values['subject_code']) ?>" required>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Assessment scores</h2>
                    <div class="form-grid">
                        <div class="form-field">
                            <label for="test1">Test 1 <span class="field-hint">out of 20</span></label>
                            <input type="number" id="test1" name="test1" min="0" max="20" step="0.01"
                                value="<?= $escape($values['test1']) ?>" required>
                        </div>
                        <div class="form-field">
                            <label for="test2">Test 2 <span class="field-hint">out of 30</span></label>
                            <input type="number" id="test2" name="test2" min="0" max="30" step="0.01"
                                value="<?= $escape($values['test2']) ?>" required>
                        </div>
                        <div class="form-field">
                            <label for="final_exam">Final exam <span class="field-hint">out of 50</span></label>
                            <input type="number" id="final_exam" name="final_exam" min="0" max="50" step="0.01"
                                value="<?= $escape($values['final_exam']) ?>" required>
                        </div>
                        <div class="form-field">
                            <label for="grade">Grade</label>
                            <input type="text" id="grade" name="grade" placeholder="For example, A or B+"
                                value="<?= $escape($values['grade']) ?>" required>
                        </div>
                    </div>
                    <p class="total-preview">Total marks: <strong id="total-preview"><?= $totalMarks === null ? '—' : number_format($totalMarks, 2) ?></strong> / 100</p>
                </div>

                <div class="form-actions">
                    <a class="button button--secondary" href="index.php">Cancel</a>
                    <button class="button" type="submit">Save Student Result</button>
                </div>
            </form>
        </section>
<?php include __DIR__ . '/templates/footer.php'; ?>