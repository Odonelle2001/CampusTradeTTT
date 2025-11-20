<?php
  include("header.php");
session_start();
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

  <!-- Main Container -->
<div class="signup-wrapper">
  <div class="logo-side">
    <img src="Images/CampusTradeLogo.png" alt="CampusTrade Logo">
  </div>

  <div class="signup-container">
  <h2>Create an account</h2>

  <!--Error Handling-->
<?php if (!empty($errors)): ?>
  <div class="alert error" role="alert" aria-live="polite">
    <ul class="alert-list">
      <?php foreach ($errors as $msg): ?>
        <li><?= htmlspecialchars($msg) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

  <form action="SignUp_Controller.php" method="POST">
  <label for="firstName">First Name</label>
  <input id="firstName" name="firstName" type="text" required>

  <label for="lastName">Last Name</label>
  <input id="lastName" name="lastName" type="text" required>

  <label for="email">Minnstate Email</label>
  <input id="email" name="email" type="email" placeholder="Startid@go.minnstate.edu" required>

  <label for="school">School Name</label>
  <input id="school" name="school" type="text" required>

  <label for="major">Major</label>
  <input id="major" name="major" type="text" required>
    
  <label for="State/city"> state/city</label>
  <input type="text" name="location" placeholder="State/City">
    
  <label for="role">Role</label>
  <select id="role" name="role" required>
    <option value="" disabled selected>Student or Alumni</option>
    <option value="student">Student</option>
    <option value="alumni">Alumni</option>
  </select>

  <label for="password">Password</label>
  <input id="password" name="password" type="password" placeholder="6 characters minimum" required>

  <label for="confirmPassword">Confirm Password</label>
  <input id="confirmPassword" name="confirmPassword" type="password" required>

  <button type="submit" name="submit">Sign Up</button>
</form>
<div class="login-redirect">
  Already have an account? <a href="LoginPage.php">Log in here</a>
</div>

  
  <div class="note orange">Only For Minnstate schools.</div>
</div>

  <?php
    include("footer.php");
  ?>

</body>
</html>
