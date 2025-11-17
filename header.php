<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>CampusTrade CSS Test</title>
  <link rel="stylesheet" href="CSS/BasicSetUp.css">         <!-- Global styles -->
  <link rel="stylesheet" href="CSS/HeaderNavBar.css">       <!-- Header, nav, layout -->
  <link rel="stylesheet" href="CSS/ReusableComponents.css"> <!-- Buttons, cards, modals -->
  <link rel="stylesheet" href="CSS/LoginForm.css">
  <link rel="stylesheet" href="CSS/BuyButtonPage.css">
  <link rel="stylesheet" href="css/BuyerPage.css">

</head>
<body>

  <!-- Header and Navigation -->
  <header>
    <div class="logo"> <img src="Images/CampusTradeLogo.png" alt="CampusTrade Logo" width="120">
  </div>
    <nav>
      <a href="BuyButtonPage.php" style="font-size:12px;">ToBuyBook</a>
      <a href="HomePage.php">Home</a>
      <a href="AboutPage.php">About</a>
      <a href="SignUpPage.php">Sign Up</a>
      <a href="LoginPage.php">Login</a>
      <a href="ContactPage.php">Contact</a>
       <?php if (isset($_SESSION['user_id'])): ?>
    <a href="logout.php">Logout</a>
  <?php endif; ?>
  </nav>
  </header>
