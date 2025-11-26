<?php
session_start();

// Must be logged in
if (empty($_SESSION['user_id'])) {
    header('Location: LoginPage.php');
    exit;
}

$mustChange = !empty($_SESSION['force_pw_change']); // from login

// Database + Model
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$db = require __DIR__ . '/Database.php';

require __DIR__ . '/UserModel.php';
$userModel = new UserModel($db);

$userId = (int)$_SESSION['user_id'];

$error = '';
$msg = '';

// -------------------------
// Handle Form Submission
// -------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $newPass = trim($_POST['new_password'] ?? '');
    $confirm = trim($_POST['confirm_password'] ?? '');

    if ($newPass === '' || $confirm === '') {
        $error = 'Please fill in both password fields.';
    } elseif ($newPass !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (strlen($newPass) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } else {

        // Update password through the model
        $userModel->ChangePassword($userId, $newPass);

        // Remove forced password flag
        unset($_SESSION['force_pw_change']);

        // Redirect after success
        header('Location: HomePage.php');
        exit;
    }
}

include 'header.php';
?>

<main>
  <div class="login-wrapper">
    <div class="login-box">
      <div class="login-form-side">

        <h2 class="TitleLogin">
          <?= $mustChange ? 'Change your temporary password' : 'Change Password' ?>
        </h2>

        <?php if ($error): ?>
          <div class="note error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" action="ChangePassword.php" class="login-form">

          <div class="form-group">
            <label for="new_password">New password</label>
            <input type="password" id="new_password" name="new_password" required>
          </div>

          <div class="form-group">
            <label for="confirm_password">Confirm new password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
          </div>

          <button type="submit" class="login-btn">
            Update Password
          </button>
        </form>

      </div>
    </div>
  </div>
</main>

<?php include 'footer.php'; ?>
