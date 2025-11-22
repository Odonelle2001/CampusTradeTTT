<?php
session_start();
// connect to DB
$db = require __DIR__ . '/Database.php';

require __DIR__ . '/UserModel.php';

$userModel = new UserModel($db);


if(isset($_POST['search_book'])){

  $Search = $_POST['entered_book'];

  try{
    $book_search = $userModel-> ReturnBook($Search);

    $_SESSION['searchResult'] = $book_search;
    $_SESSION['searchError'] = null;

  }catch(InvalidArgumentException $e){
    $_SESSION['searchError'] = $e ->getMessage();
    $_SESSION['searchResult'] = null;

  }catch(Exception $e){
    $_SESSION['searchError'] = "Something went wrong.Please try again.";
    $_SESSION['searchResult'] = null;
  }

  //Redirect to Homepage
  header("Location: HomePage.php");
  exit;
}
?>
