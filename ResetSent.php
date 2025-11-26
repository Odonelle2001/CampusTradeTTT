<?php
session_start();
include('header.php');
?>
<main>
  <div class="login-wrapper">
    <div class="login-box">
      <h2>Password Reset Email Sent</h2>
      <p>
        If an account exists for that email, we’ve sent a message with a link
        to reset your password.
      </p>
      <p>
        Please check your inbox and spam folder and click the link in the email.
      </p>

      <a href="LoginPage.php" class="login-btn" style="margin-top:20px;">
        Back to Login
      </a>
    </div>
  </div>
</main>
<?php include('footer.php'); ?>
