<?php
session_start();
require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/csrf.php';

$idValue = $_GET['id'] ?? '';
$id = filter_var($idValue, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if ($id === false) {
    http_response_code(400);
    exit('A valid student result ID is required.');
}

$csrfToken = csrf_token();

$lookup = $conn->prepare(
    'SELECT student_name, matric_no, subject_name FROM results WHERE id = ?'
);

if ($lookup === false) {
    error_log('Unable to prepare delete confirmation lookup: ' . $conn->error);
    http_response_code(500);
    exit('Unable to load the student result.');
}

$lookup->bind_param('i', $id);
if (!$lookup->execute()) {
    error_log('Unable to execute delete confirmation lookup: ' . $lookup->error);
    http_response_code(500);
    exit('Unable to load the student result.');
}

$record = $lookup->get_result()->fetch_assoc();
if ($record === null) {
    http_response_code(404);
    exit('Student result not found.');
}

$errorMessage = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedToken = $_POST['csrf_token'] ?? '';

    if (!csrf_token_is_valid($submittedToken)) {
        http_response_code(400);
        $errorMessage = 'The confirmation expired or is invalid. Refresh this page and try again.';
    } else {
        $delete = $conn->prepare('DELETE FROM results WHERE id = ?');

        if ($delete === false) {
            error_log('Unable to prepare student result deletion: ' . $conn->error);
            http_response_code(500);
            $errorMessage = 'Unable to delete the student result. Please try again.';
        } else {
            $delete->bind_param('i', $id);

            if (!$delete->execute()) {
                error_log('Unable to delete student result: ' . $delete->error);
                http_response_code(500);
                $errorMessage = 'Unable to delete the student result. Please try again.';
            } elseif ($delete->affected_rows !== 1) {
                http_response_code(404);
                $errorMessage = 'This student result no longer exists.';
            } else {
                unset($_SESSION['csrf_token']);
                header('Location: index.php');
                exit;
            }
        }
    }
}

$escape = static fn(string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
$pageTitle = 'Delete Student Result';
$pageDescription = 'Review this result before deleting it.';
$pageBackUrl = 'index.php';
$pageBackLabel = '← Back to results';
include __DIR__ . '/templates/header.php';
?>

        <?php if ($errorMessage !== null): ?>
            <div class="form-errors" role="alert">
                <p><?= $escape($errorMessage) ?></p>
            </div>
        <?php endif; ?>

        <section class="results-card delete-confirmation" aria-label="Confirm student result deletion">
            <h2>Are you sure you want to delete this result?</h2>
            <dl class="delete-details">
                <div>
                    <dt>Student</dt>
                    <dd><?= $escape((string) $record['student_name']) ?></dd>
                </div>
                <div>
                    <dt>Matriculation number</dt>
                    <dd><?= $escape((string) $record['matric_no']) ?></dd>
                </div>
                <div>
                    <dt>Subject</dt>
                    <dd><?= $escape((string) $record['subject_name']) ?></dd>
                </div>
            </dl>
            <p class="delete-warning">This action cannot be undone.</p>

            <form class="form-actions" method="post" action="delete.php?id=<?= $id ?>">
                <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
                <a class="button button--secondary" href="index.php">Cancel</a>
                <button class="button button--danger" type="submit">Delete Result</button>
            </form>
        </section>
<?php include __DIR__ . '/templates/footer.php'; ?>