# Student Grading System

## Project structure

- `config/db.php` - MySQL connection settings.
- `includes/auth.php` - Session authentication guard for grading pages.
- `includes/csrf.php` - CSRF token helpers used by delete and logout forms.
- `templates/` - Shared page header and footer.
- `docs/` - CSS, JavaScript, and project guide assets.
- Root PHP files - Login, logout, and student result pages/actions.

## Local setup

1. Place the project in your local PHP web server directory.
2. Create the `student_data` database and its `results` table.
3. Configure the database connection in `config/db.php`.
4. Open `login.php` or `index.php` through the local web server.
