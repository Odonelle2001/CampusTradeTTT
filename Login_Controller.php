<?php
/*
    session_start();
    $db = require __DIR__ . '/Database.php'; 
    require __DIR__ . '/UserModel.php';

    $userModel = new UserModel($db);

    //write your code here

    if($_SERVER['REQUEST_METHOD']=== 'POST'){

        if(empty($_POST['email']) || empty($_POST['password'])){
            $msg = "Email and password fields can't be empty";
            $_SESSION['flash_errors'] = [$msg];
            $_SESSION['old'] = $_POST;
            header('Location: LoginPage.php');
        }

        try{

            $User_email = $_POST['email'];    
            $User_password = $_POST['password'];

            $user = $userModel->VerifyUser($User_email,$User_password);

            header('Location: buyerpage.php');
            exit;

        }catch(InvalidArgumentException $e){
            $_SESSION['flash_errors'] = [$e ->getmessage()];
            $_SESSION['old'] = $_POST;

            header('Location: LoginPage.php');
            exit;
        }catch(RuntimeException $e){
            $_SESSION['flash_errors'] = ['Server error'];
            header('Location: LoginPage.php');
        }

    }
*/

session_start();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$db = require __DIR__ . '/Database.php';
require __DIR__ . '/UserModel.php';
$userModel = new UserModel($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['email']) || empty($_POST['password'])) {
        $_SESSION['flash_errors'] = ["Email and password fields can't be empty"];
        $_SESSION['old'] = $_POST;
        header('Location: LoginPage.php'); exit;
    }

    try {
        $user = $userModel->VerifyUser($_POST['email'], $_POST['password']);

        //Start a session for the User
        session_regenerate_id(true);
        $_SESSION['user_id']   = (int)$user['id'];
        $_SESSION['email']     = $user['email'];       
        $_SESSION['firstName'] = $user['first_name'] ?? '';
        
        // If user is using a temporary reset password, force them to change it
        if (!empty($user['must_change_password']) && (int)$user['must_change_password'] === 1) {
            $_SESSION['force_pw_change'] = true;
            header('Location: ChangePassword.php');
            exit;
}

        // Redirect to controller that builds the view
        header('Location: buyerpage.php');
        exit;

    } catch (InvalidArgumentException $e) {
        $_SESSION['flash_errors'] = [$e->getMessage()];
        $_SESSION['old'] = $_POST;
        header('Location: LoginPage.php'); 
        exit;

    } catch (RuntimeException $e) {
        $_SESSION['flash_errors'] = ['Server error'];
        header('Location: LoginPage.php'); 
        exit;
    }

} else {
    header('Location: LoginPage.php'); exit;
}


?>