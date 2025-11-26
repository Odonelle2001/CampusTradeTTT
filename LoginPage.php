<?php
session_start();
include('header.php');

// Pull flashes (set by your POST handler)
$errors = $_SESSION['flash_errors'] ?? [];
$success = $_SESSION['flash_success'] ?? null;
$old = $_SESSION['old'] ?? [];
unset($_SESSION['flash_errors'], $_SESSION['flash_success'], $_SESSION['old']);


//Checks if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: buyerpage.php");
    exit;
}

?>

<main>
  <div class="login-wrapper">
    <div class="login-box">
      
      <!-- Left side: Logo / Image -->
      <div class="login-logo-side">
        <img src="Images/CampusTradeLogo.png" alt="CampusTrade Logo">
      </div>

      <!-- Right side: Login form -->
      <div class="login-form-side">
        <h2 class="TitleLogin">Login to CampusTrade</h2>
        
      <!--Error Handling-->
        <?php if (!empty($errors)): ?>
        <div class="alerts" role="region" aria-label="Errors">
          <?php foreach ($errors as $msg): ?>
            <div class="alert alert--error" role="alert" aria-live="polite">
              <span class="alert__icon" aria-hidden="true">❌</span>
              <div class="alert__content"><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

        <form class="form_box" action="Login_Controller.php" method="POST">
          <label for="username">MinnState Email</label>
          <input id="username" name="email" type="email" placeholder="StartID@go.minnstate.edu" required>

          <label for="password">Password</label>
          <input id="password" name="password" type="password" placeholder="Enter your password" required>

          <button type="submit" class="button">Login</button>
        </form>

        <div class="login-links">
          <a href="ForgotPassword.php">Forgot Password?</a>
          <p>Don’t have an account? <a href="SignUpPage.php">Sign up here</a></p>
        </div>

        <div class="note orange">Only for MinnState schools.</div>
      </div>

    </div>
  </div>
</main>

<?php
include('footer.php');
?>

