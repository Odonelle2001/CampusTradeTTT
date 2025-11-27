<?php
session_start();

$vImgSrc     = $vImgSrc     ?? 'Images/ProfileIcon.png';
$vFirst      = $vFirst      ?? ''; 
$vLast       = $vLast       ?? '';  
$vAcad       = $vAcad       ?? '';
$vSchool     = $vSchool     ?? '';
$vMajor      = $vMajor      ?? '';
$vCityState  = $vCityState  ?? '';
$vEmail      = $vEmail      ?? '';
$vPay        = $vPay        ?? '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link rel="stylesheet" href="CSS/BasicSetUp.css">         <!-- Global styles -->
    <link rel="stylesheet" href="CSS/HeaderNavBar.css">       <!-- Header, nav, layout -->
    <link rel="stylesheet" href="CSS/LoginForm.css">
    <link rel="stylesheet" href="CSS/BuyButtonPage.css">
    <link rel="stylesheet" href="css/BuyerPage.css">
    <link rel="stylesheet" href="CSS/ReusableComponents.css"> <!-- Buttons, cards, modals -->
    <link rel="stylesheet" href="CSS/ProfileUpdate.css">

</head>
<body>
<div class="profile-page-wrapper">
    <div class="profile-panel">
      <h2>Your Profile</h2>

      <form id="profileForm" method="POST" action="Seller_Controller.php">
        <div class="profile-fields">
          <label for="">First Name:</label>
          <input type="text" name="first_name" value="<?= htmlspecialchars($vFirst) ?>">

          <label for="">Last Name:</label>
          <input type="text" name="last_name"  value="<?= htmlspecialchars($vLast) ?>">

          <label for="">Status:</label>
          <select name="acad_role">
            <option value="Student" <?= ($vAcad === 'Student') ? 'selected' : '' ?>>Student</option>
            <option value="Alumni"  <?= ($vAcad === 'Alumni')  ? 'selected' : '' ?>>Alumni</option>
          </select>

          <label for="">School</label>
          <input type="text" name="school_name" value="<?= htmlspecialchars($vSchool) ?>">

          <label for="">Major:</label>
          <input type="text" name="major" value="<?= htmlspecialchars($vMajor) ?>">

          <label for="">City:</label>
          <input type="text" name="city_state" value="<?= htmlspecialchars($vCityState) ?>">

          <label for="">Preferred Payment:</label>
          <select name="preferred_pay">
            <option value="Cash"      <?= ($vPay === 'Cash')      ? 'selected' : '' ?>>Cash</option>
            <option value="Venmo"     <?= ($vPay === 'Venmo')     ? 'selected' : '' ?>>Venmo</option>
            <option value="Zelle"     <?= ($vPay === 'Zelle')     ? 'selected' : '' ?>>Zelle</option>
            <option value="PayPal"    <?= ($vPay === 'PayPal')    ? 'selected' : '' ?>>PayPal</option>
            <option value="Cash App"  <?= ($vPay === 'Cash App')  ? 'selected' : '' ?>>Cash App</option>
            <option value="Other"     <?= ($vPay === 'Other')     ? 'selected' : '' ?>>Other</option>
          </select>

          <input type="hidden" name="edit_profile" value="1">

          <button class="button" name="save_fields" type="submit">Save Changes</button>
        </div>
      </form>
    </div>
  </div>

</body>
</html>