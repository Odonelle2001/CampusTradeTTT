<?php
session_start();

// show errors while you're still developing
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$msg = '';
$error = '';
$debugResetLink = ''; // We show the reset link on screen for the demo

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    // Basic validation
    if ($email === '') {
        $error = 'Please enter your email.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (!preg_match('/@go\.minnstate\.edu$/i', $email)) {
        // Enforce school email domain
        $error = 'Please use your go.minnstate.edu school email.';
    } else {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $db = require __DIR__ . '/Database.php';

        // Look up user by email
        $sql = "SELECT id, email FROM accounts WHERE email = ? LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result  = $stmt->get_result();
        $account = $result->fetch_assoc();
        $stmt->close();

        // Generic message (so we don't leak which emails exist)
        $genericMsg = 'If an account exists for that email, a reset link has been generated.';

        if (!$account) {
            // Do NOT reveal invalid emails
            $msg = $genericMsg;
        } else {
            $userId    = (int)$account['id'];
            $userEmail = $account['email'];

            // Generate reset token
            $token     = bin2hex(random_bytes(32));          // 64-char token
            $tokenHash = hash('sha256', $token);
            $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 hour from now (not enforced here, but stored)

            // 3) Save token + expiry in DB
            $updateSql = "
                UPDATE accounts
                   SET reset_token_hash   = ?,
                       reset_expires_at   = ?,
                       must_change_password = 0
                 WHERE id = ?
            ";
            $stmt = $db->prepare($updateSql);
            $stmt->bind_param('ssi', $tokenHash, $expiresAt, $userId);
            $stmt->execute();
            $stmt->close();

            // 4) Build reset link 
            $baseUrl   = 'http://localhost/CampusTradeTTT';
            $resetLink = $baseUrl . '/ResetPassword.php?token=' . urlencode($token);

        
            // Instead, we show the link on screen so the instructor can click it.
            $msg = $genericMsg;
            $debugResetLink = $resetLink;
        }
    }
}

include('header.php');
?>

<main>
  <div class="login-wrapper">
    <div class="login-box">

      <div class="login-logo-side">
        <img src="Images/CampusTradeLogo.png" alt="CampusTrade Logo">
      </div>

      <div class="login-form-side">
        <h2 class="TitleLogin">Forgot your password?</h2>

        <?php if ($error): ?>
          <div class="note error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php if ($msg): ?>
          <div class="note success"><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php if ($debugResetLink): ?>
          <div class="note orange">
            <strong>Password Reset Link</strong><br>
            <a href="<?= htmlspecialchars($debugResetLink, ENT_QUOTES, 'UTF-8') ?>">
              <?= htmlspecialchars($debugResetLink, ENT_QUOTES, 'UTF-8') ?>
            </a>
            <br>
          </div>
        <?php endif; ?>

        <form method="post" action="ForgotPassword.php" class="login-form">
          <div class="form-group">
            <label for="email">Enter your email</label>
            <input
              type="email"
              id="email"
              name="email"
              placeholder="StarID@go.minnstate.edu"
              required
            >
          </div>

          <button type="submit" class="button">Reset Link</button>
        </form>

        <div class="login-links">
          <p><a href="LoginPage.php">Back to Login</a></p>
          <p><a href="SignUpPage.php">Create an Account</a></p>
        </div>

      </div>

    </div>
  </div>
</main>

<?php include('footer.php'); ?>
