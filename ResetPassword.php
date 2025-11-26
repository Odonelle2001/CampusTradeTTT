<?php
session_start();

// Show errors while you're still developing
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$error = '';
$msg   = '';
$showForm = true;

// Get token from URL or POST
$token = $_GET['token'] ?? ($_POST['token'] ?? '');
$token = trim($token ?? '');

if ($token === '') {
    $error    = 'Invalid or missing reset link.';
    $showForm = false;
} else {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $db = require __DIR__ . '/Database.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $newPassword     = (string)($_POST['password'] ?? '');
        $confirmPassword = (string)($_POST['confirm_password'] ?? '');

        // Basic validation
        if ($newPassword === '' || $confirmPassword === '') {
            $error = 'Please fill in both password fields.';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'Passwords do not match.';
        } elseif (strlen($newPassword) < 8) {
            $error = 'Password must be at least 8 characters.';
        }

        if ($error === '') {
            $tokenHash = hash('sha256', $token);

            // Find a user with this token
            $sql = "
                SELECT id
                  FROM accounts
                 WHERE reset_token_hash = ?
                 LIMIT 1
            ";
            $stmt = $db->prepare($sql);
            $stmt->bind_param('s', $tokenHash);
            $stmt->execute();
            $result = $stmt->get_result();
            $user   = $result->fetch_assoc();
            $stmt->close();

            if (!$user) {
                $error    = 'This reset link is invalid. Please request a new one.';
                $showForm = false;
            } else {
                $userId = (int)$user['id'];
                $hash   = password_hash($newPassword, PASSWORD_DEFAULT);

                // Update password and clear token fields
                $updateSql = "
                    UPDATE accounts
                       SET password        = ?,
                           reset_token_hash = NULL,
                           reset_expires_at = NULL
                     WHERE id = ?
                ";
                $stmt = $db->prepare($updateSql);
                $stmt->bind_param('si', $hash, $userId);
                $stmt->execute();
                $stmt->close();

                $msg      = 'Your password has been reset. You can now log in with your new password.';
                $showForm = false;
            }
        }
    } else {
        //  verify token before showing form
        $tokenHash = hash('sha256', $token);

        $sql = "
            SELECT id
              FROM accounts
             WHERE reset_token_hash = ?
             LIMIT 1
        ";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('s', $tokenHash);
        $stmt->execute();
        $result = $stmt->get_result();
        $user   = $result->fetch_assoc();
        $stmt->close();

        if (!$user) {
            $error    = 'This reset link is invalid. Please request a new one.';
            $showForm = false;
        }
    }
}

include 'header.php';
?>
<main>
  <div class="login-wrapper">
    <div class="login-box">
      <div class="login-logo-side">
        <img src="Images/CampusTradeLogo.png" alt="CampusTrade Logo">
      </div>

      <div class="login-form-side">
        <h2 class="TitleLogin">Choose a New Password</h2>

        <?php if ($error): ?>
          <div class="note error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php if ($msg): ?>
          <div class="note success"><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php if ($showForm): ?>
          <form method="post" action="ResetPassword.php" class="login-form">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-group">
              <label for="password">New Password</label>
              <input
                type="password"
                id="password"
                name="password"
                placeholder="At least 8 characters"
                required
              >
            </div>

            <div class="form-group">
              <label for="confirm_password">Confirm New Password</label>
              <input
                type="password"
                id="confirm_password"
                name="confirm_password"
                placeholder="At least 8 characters"
                required
              >
            </div>

            <button type="submit" class="button">Update Password</button>
          </form>
        <?php else: ?>
          <div style="margin-top:20px;">
            <a href="ForgotPassword.php">Request a new reset link</a> |
            <a href="LoginPage.php">Back to Login</a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</main>
<?php include 'footer.php'; ?>
