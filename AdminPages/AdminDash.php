<?php
session_start();
include('header.php');

// connect to DB (mysqli)
$db = require __DIR__ . '/Database.php';

// Make sure user is logged in
if (empty($_SESSION['user_id'])) {
    header('Location: LoginPage.php');
    exit;
}

$userId = (int) $_SESSION['user_id'];


//Work in Progress

?>

