<?php
session_start();
require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/includes/csrf.php';

$loginSucceeded = isset($_SESSION['login_success']);
unset($_SESSION['login_success']);
$logoutToken = csrf_token();

require_once __DIR__ . '/config/db.php';
$stmt = $conn->prepare("SELECT * FROM results");
$stmt->execute();
$result = $stmt->get_result();
$pageTitle = 'Student Grading System';
$pageDescription = 'Review student results and grades.';
include __DIR__ . '/templates/header.php';
?>

<?php if ($loginSucceeded): ?>
    <p class="login-success" id="login-success" role="status">Login successful!</p>
<?php endif; ?>

<section class="results-card" aria-label="Student results">
    <div class="card-toolbar">
        <div class="toolbar-actions">
            <a class="button" href="add.php">＋ Add Student Result</a>
            <a class="button button--secondary" href="index.php">Show All</a>
            <form class="logout-form" action="logout.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($logoutToken, ENT_QUOTES, 'UTF-8') ?>">
                <button class="button button--secondary" type="submit">Log out</button>
            </form>
        </div>
        <form class="search-form" role="search" onsubmit="return false">
            <input class="search-input" type="search" id="result-search"
                placeholder="Search name, ID, subject, or code"
                aria-label="Search student results">
        </form>
    </div>

    <div class="table-scroll">
        <table>
            <thead>
                <tr>
                    <th>Number</th>
                    <th>Student Name</th>
                    <th>Matriculation Number</th>
                    <th>Subject</th>
                    <th>Subject Code</th>
                    <th>Test 1 (20)</th>
                    <th>Test 2 (30)</th>
                    <th>Final (50)</th>
                    <th>Total (100)</th>
                    <th>Grade</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="results-body">
                <?php
                $no = 1;
                while ($row = $result->fetch_assoc()):
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td class="student-name"><?= htmlspecialchars($row['student_name']) ?></td>
                        <td><?= htmlspecialchars($row['matric_no']) ?></td>
                        <td><?= htmlspecialchars($row['subject_name']) ?></td>
                        <td><?= htmlspecialchars($row['subject_code']) ?></td>
                        <td><?= number_format($row['test1'], 2) ?></td>
                        <td><?= number_format($row['test2'], 2) ?></td>
                        <td><?= number_format($row['final_exam'], 2) ?></td>
                        <td><?= number_format($row['total_marks'], 2) ?></td>
                        <td><span class="grade-badge"><?= htmlspecialchars($row['grade']) ?></span></td>
                        <td>
                            <div class="row-actions">
                                <a href="edit.php?id=<?= (int) $row['id'] ?>">Edit</a>
                                <a href="delete.php?id=<?= (int) $row['id'] ?>">Delete</a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <p class="empty-state" id="empty-state" hidden>No matching student results found.</p>
    </div>
    <footer class="card-footer" id="result-count">Student results</footer>
</section>
<?php include __DIR__ . '/templates/footer.php'; ?>