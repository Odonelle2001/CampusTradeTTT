<?php
session_start();
if (empty($_SESSION['acad_role']) || $_SESSION['acad_role'] !== 'Admin') {
    header("Location: /CampusTradeTTT/HomePage.php");
    exit;
}

require_once '../Database.php';
require_once 'EditController.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_profile'])) {

    $controller = new EditController($db);

    try {
        $changed = $controller->updateAccountFromPost($_POST);

        $_SESSION['admin_message'] = $changed
            ? 'User profile updated.'
            : 'No changes were made.';

        header('Location: AdminDash.php');
        exit;
    } catch (Throwable $e) {
        $_SESSION['admin_error'] = 'Update failed: ' . $e->getMessage();
        header('Location: AdminDash.php');
        exit;
    }
}

if (!isset($_GET['id'])) {
    die('No user selected.');
}

$accountId = (int) $_GET['id'];

$sql = "
    SELECT 
        id,
        email,
        first_name,
        last_name,
        school_name,
        major,
        acad_role,
        city_state
    FROM accounts
    WHERE id = ?
";

$stmt = $db->prepare($sql);
if (!$stmt) {
    die('DB error (prepare select): ' . $db->error);
}

$stmt->bind_param('i', $accountId);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $vFirst     = $row['first_name']   ?? '';
    $vLast      = $row['last_name']    ?? '';
    $vAcad      = $row['acad_role']    ?? '';
    $vSchool    = $row['school_name']  ?? '';
    $vMajor     = $row['major']        ?? '';
    $vCityState = $row['city_state']   ?? '';
    $vEmail     = $row['email']        ?? '';
    $vId        = $row['id'];
} else {
    die('User not found.');
}

$stmt->close();

// defaults
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
<link rel= "stylesheet" href="AdminDash.css"> 

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link rel="stylesheet" href="../CSS/BasicSetUp.css">         <!-- Global styles -->
    <link rel="stylesheet" href="../CSS/HeaderNavBar.css">       <!-- Header, nav, layout -->
    <link rel="stylesheet" href="../CSS/ReusableComponents.css"> <!-- Buttons, cards, modals -->
    <link rel="stylesheet" href="../CSS/LoginForm.css">
    <link rel="stylesheet" href="../CSS/BuyButtonPage.css">
    <link rel="stylesheet" href="../css/BuyerPage.css">

</head>
<body>
<div class="profile-panel">
    <h2>Your Profile</h2>

<form id="profileForm" method="POST" action="AdminEditUser.php">
  
  <div class="profile-fields">
    <label for="">First Name:</label>
    <input type="text" name="first_name" value="<?= htmlspecialchars($vFirst) ?>">
    <label for="">Last Name:</label>
    <input type="text" name="last_name"  value="<?= htmlspecialchars($vLast) ?>">

    <label for="">Status:</label>
    <select name="acad_role">
      <option value="Student" <?= ($vAcad === 'Student') ? 'selected' : '' ?>>Student</option>
      <option value="Alumni"  <?= ($vAcad === 'Alumni')  ? 'selected' : '' ?>>Alumni</option>
      <option value="Admin"  <?= ($vAcad === 'Admin')  ? 'selected' : '' ?>>Admin</option>
    </select>

    <label for="">School</label>
    <input type="text" name="school_name" value="<?= htmlspecialchars($vSchool) ?>">

    <label for="">Major:</label>
    <input type="text" name="major"       value="<?= htmlspecialchars($vMajor) ?>">

    <label for="">City:</label>
    <input type="text" name="city_state"  value="<?= htmlspecialchars($vCityState) ?>">

   <label for="">Prefferred Payment:</label>
    <select name="preferred_pay">
      <option value="Cash"      <?= ($vPay === 'Cash')      ? 'selected' : '' ?>>Cash</option>
      <option value="Venmo"     <?= ($vPay === 'Venmo')     ? 'selected' : '' ?>>Venmo</option>
      <option value="Zelle"     <?= ($vPay === 'Zelle')     ? 'selected' : '' ?>>Zelle</option>
      <option value="PayPal"    <?= ($vPay === 'PayPal')    ? 'selected' : '' ?>>PayPal</option>
      <option value="Cash App"  <?= ($vPay === 'Cash App')  ? 'selected' : '' ?>>Cash App</option>
      <option value="Other"     <?= ($vPay === 'Other')     ? 'selected' : '' ?>>Other</option>
    </select>

    <input type="hidden" name="account_id" value="<?= (int)$vId ?>">
    <input type="hidden" name="edit_profile" value="1">

    <button class="button" name="save_fields" type="submit">Save Changes</button>
    <a href="AdminDash.php" class="Button">Cancel</a>
  </div>

</form>


    
</body>
</html>