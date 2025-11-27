<?php

// Seller_Controller.php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$db = require __DIR__ . '/Database.php';
require __DIR__ . '/UserModel.php';

// ---- Upload root ----
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $UPLOAD_ROOT = "C:/xampp/htdocs/CampusTradeTTT/Uploads/";
} else {
    $UPLOAD_ROOT = __DIR__ . "/Uploads/";
}

$userModel = new UserModel($db);

// =========================
// Require login
// =========================
if (empty($_SESSION['user_id'])) {
    header("Location: LoginPage.php");
    exit;
}

$userId   = (int)$_SESSION['user_id'];
$sellerId = $userId; // same thing in your app

// =========================
// POST HANDLERS
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* =======================================
       1) PROFILE IMAGE UPLOAD (auto circle)
       ======================================= */
    if (!empty($_FILES['profileImage']['name'])) {

        $file  = $_FILES['profileImage'];
        $error = $file['error'];

        if ($error === UPLOAD_ERR_OK && $file['size'] > 0) {

            $uploadDir = $UPLOAD_ROOT . "Profiles/";
            $webPrefix = "Uploads/Profiles/";

            if (!is_dir($uploadDir)) {
                die('Upload folder NOT found for profile image: ' . $uploadDir);

            }

            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
                $_SESSION['error'] = "Invalid image type.";
                header("Location: Seller_Controller.php?profile=img_failed");
                exit;
            }

            $fileName = 'avatar_' . $userId . '_' . time() . '_' . mt_rand(1000,9999) . '.' . $ext;
            $fullPath = $uploadDir . $fileName;

            if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
                $_SESSION['error'] = "Upload failed.";
                header("Location: Seller_Controller.php?profile=img_failed");
                exit;
            }

            $imagePath = $webPrefix . $fileName;
            $userModel->UpdateProfileImage($imagePath, $userId);

            header("Location: Seller_Controller.php?profile=img_updated");
            exit;
        }

        $_SESSION['error'] = "Upload error code: " . $error;
        header("Location: Seller_Controller.php?profile=img_failed");
        exit;
    }

    /* =======================================
       2) SAVE PROFILE FIELDS (Profile_Update.php)
       ======================================= */
    if (isset($_POST['edit_profile']) && isset($_POST['save_fields'])) {

        $Profile_data = [
            'first'  => trim($_POST['first_name'] ?? ''),
            'last'   => trim($_POST['last_name'] ?? ''),
            'acad'   => trim($_POST['acad_role'] ?? 'Student'),
            'school' => trim($_POST['school_name'] ?? ''),
            'major'  => trim($_POST['major'] ?? ''),
            'citySt' => trim($_POST['city_state'] ?? ''),
        ];

        try {
            $ok = $userModel->UpdateProfile($Profile_data, $userId);

            header("Location: Seller_Controller.php?profile=" . ($ok ? "updated" : "nochange"));
            exit;

        } catch (RuntimeException $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: Seller_Controller.php?view=profile_update&profile=failed");
            exit;
        }
    }

    /* =======================================
       3) POST A BOOK
       ======================================= */
    if (isset($_POST['post_book'])) {

        $titleAuthor = trim($_POST['titleAuthor'] ?? '');
        $isbn        = trim($_POST['isbn'] ?? '');
        $priceInput  = trim($_POST['price'] ?? '0');
        $condition   = $_POST['condition'] ?? 'New';
        $courseDept  = trim($_POST['courseDept'] ?? '');
        $contact     = trim($_POST['contact'] ?? '');

        if ($titleAuthor === '') {
            header('Location: Seller_Controller.php?error=missing_title');
            exit;
        }

        $price = (int) round((float)$priceInput);
        if ($price < 0) $price = 0;

        $bookState = ($condition === 'Used') ? 'Used' : 'New';
        $status    = 'Active';

        // book image upload
        $imagePath = null;
        if (!empty($_FILES['bookImage']['name'])) {
            $file  = $_FILES['bookImage'];
            $error = $file['error'];

            if ($error === UPLOAD_ERR_OK && $file['size'] > 0) {

                // 🔹 Absolute path to Books folder (must match your real path)
                $uploadDir = 'C:/Xampp/htdocs/CampusTradeTTT/Uploads/Books/';

                if (!is_dir($uploadDir)) {
                    die('Upload folder NOT found for books: ' . $uploadDir);

                }

                // Path stored in DB / used in <img src="...">
                $webPrefix = 'Uploads/Books/';

                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $ext = 'jpg';
                }

                $fileName = 'book_' . $sellerId . '_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
                $fullPath = $uploadDir . $fileName;

                if (move_uploaded_file($file['tmp_name'], $fullPath)) {
                    $imagePath = $webPrefix . $fileName;
                }
            }
        }

        $sql = "
            INSERT INTO booklistings
            (seller_id, title, isbn, image_path, price, book_state, status, course_id, contact_info)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt = $db->prepare($sql);
        $stmt->bind_param(
            "isssissss",
            $sellerId,
            $titleAuthor,
            $isbn,
            $imagePath,
            $price,
            $bookState,
            $status,
            $courseDept,
            $contact
        );
        $stmt->execute();
        $stmt->close();

        header('Location: Seller_Controller.php?posted=1');
        exit;
    }

   /* ---- B) UPDATE PROFILE IMAGE ---- */
if (isset($_POST['edit_profile'])) {

    $newImagePath = null;

    if (!empty($_FILES['profileImage']['name'])) {
        $file  = $_FILES['profileImage'];
        $error = $file['error'];

        if ($error === UPLOAD_ERR_OK && $file['size'] > 0) {

            // 🔹 ABSOLUTE PATH on disk – must match your real folder
            $uploadDir = 'C:/Xampp/htdocs/CampusTradeTTT/Uploads/Profiles/';
            $webPrefix = 'Uploads/Profiles/';   // what we store in DB / use in <img src>

            if (!is_dir($uploadDir)) {
                die('Upload folder NOT found for profiles: ' . $uploadDir);
            }

            // extension
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
                $ext = 'jpg';
            }

            $fileName = 'avatar_' . $sellerId . '_' . time() . '_' . mt_rand(1000,9999) . '.' . $ext;
            $fullPath = $uploadDir . $fileName;

            if (move_uploaded_file($file['tmp_name'], $fullPath)) {
                // this goes in DB
                $newImagePath = $webPrefix . $fileName;
            } else {
                die('move_uploaded_file failed for profile image. Tried: ' . $fullPath);
            }
        } elseif ($error !== UPLOAD_ERR_NO_FILE) {
            die('Upload error for profileImage. Error code: ' . $error);
        }
    }

    if ($newImagePath !== null) {
        // if row exists -> update, otherwise insert
        $sql = "
            INSERT INTO userprofile (user_id, profile_image, preferred_pay)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE profile_image = VALUES(profile_image)
        ";

        $prefPay = $vPay ?: 'Cash';

        $stmt = $db->prepare($sql);
        $stmt->bind_param("iss", $sellerId, $newImagePath, $prefPay);
        $stmt->execute();
        $stmt->close();

        // refresh current page variables
        $vImgSrc = $newImagePath;
    }

    header('Location: Seller_Controller.php?profile=updated');
    exit;
}


    /* ---- C) DELETE BOOK ---- */
    if (isset($_POST['delete_book'])) {
        $bookIdToDelete = isset($_POST['postedBook']) ? (int) $_POST['postedBook'] : 0;

        if ($bookIdToDelete > 0) {
            $sql = "DELETE FROM booklistings WHERE id = ? AND seller_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("ii", $bookIdToDelete, $sellerId);
            $stmt->execute();
            $stmt->close();
        }

        header('Location: Seller_Controller.php?deleted=1');
        exit;
    }

    /* =======================================
       5) EDIT BOOK (open Edit_book.php)
       ======================================= */
    if (isset($_POST['edit_book'])) {
        $booktoEdit = isset($_POST['postedBook']) ? (int) $_POST['postedBook'] : 0;

        if ($booktoEdit <= 0) {
            $_SESSION['error'] = "Please select a book to edit.";
            header("Location: Seller_Controller.php");
            exit;
        }

        $book = $userModel->GetBookId($booktoEdit, $sellerId);

        if (!$book) {
            $_SESSION['error'] = "Book not found.";
            header("Location: Seller_Controller.php");
            exit;
        }

        include 'Edit_book.php';
        exit;
    }

    /* =======================================
       6) UPDATE BOOK
       ======================================= */
    if (isset($_POST['update_book'])) {

        $Book_info = [
            'id'          => (int)($_POST['book_id'] ?? 0),
            'titleAuthor' => trim($_POST['titleAuthor'] ?? ''),
            'isbn'        => trim($_POST['isbn'] ?? ''),
            'price'       => trim($_POST['price'] ?? '0'),
            'condition'   => $_POST['condition'] ?? 'New',
            'courseDept'  => trim($_POST['courseDept'] ?? ''),
            'contact'     => trim($_POST['contact'] ?? ''),
        ];

        if ($Book_info['id'] <= 0) {
            $_SESSION['error'] = "Invalid book.";
            header("Location: Seller_Controller.php");
            exit;
        }

        $existingImage = trim($_POST['existing_image'] ?? '');
        $newImagePath  = $existingImage;

        if (!empty($_FILES['bookImage']['name'])) {
            $file  = $_FILES['bookImage'];
            $error = $file['error'];

            if ($error === UPLOAD_ERR_OK && $file['size'] > 0) {

                $uploadDir = $UPLOAD_ROOT . "Books/";
                if (!is_dir($uploadDir)) {
                    die('Upload folder NOT found for books (update): ' . $uploadDir);
                }

                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
                    $ext = 'jpg';
                }

                $fileName = 'book_' . $sellerId . '_' . time() . '_' . mt_rand(1000,9999) . '.' . $ext;
                $fullPath = $uploadDir . $fileName;

                if (move_uploaded_file($file['tmp_name'], $fullPath)) {
                    $newImagePath = 'Uploads/Books/' . $fileName;
                }
            }
        }

        $Book_info['image_path'] = $newImagePath;

        try {
            $userModel->UpdateBook($Book_info, $sellerId);
            header("Location: Seller_Controller.php");
            exit;

        } catch (Exception $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
            header("Location: Seller_Controller.php");
            exit;
        }
    }
}

// =========================
// GET VIEW: Profile Update page
// =========================
if (isset($_GET['view']) && $_GET['view'] === "profile_update") {

    $profile = $userModel->ProfileExtraction();

    $vFirst     = $profile['first_name'] ?? '';
    $vLast      = $profile['last_name'] ?? '';
    $vAcad      = $profile['acad_role'] ?? '';
    $vSchool    = $profile['school_name'] ?? '';
    $vMajor     = $profile['major'] ?? '';
    $vCityState = $profile['city_state'] ?? '';
    $vEmail     = $profile['email'] ?? '';
    $vPay       = $profile['preferred_pay'] ?? 'Cash';
    $vImgSrc    = $profile['profile_image'] ?: "Images/ProfileIcon.png";

    require "Profile_Update.php";
    exit;
}

// =========================
// DEFAULT LOAD: SellerPage
// =========================
$profile = $userModel->ProfileExtraction();

$vFirst     = $profile['first_name'] ?? '';
$vLast      = $profile['last_name'] ?? '';
$vAcad      = $profile['acad_role'] ?? '';
$vSchool    = $profile['school_name'] ?? '';
$vMajor     = $profile['major'] ?? '';
$vCityState = $profile['city_state'] ?? '';
$vEmail     = $profile['email'] ?? '';
$vPay       = $profile['preferred_pay'] ?? 'Cash';
$vImgSrc    = $profile['profile_image'] ?: "Images/ProfileIcon.png";

// books for dropdown
$sql = "SELECT id, title FROM booklistings WHERE seller_id = ? ORDER BY created_at DESC";
$stmt = $db->prepare($sql);
$stmt->bind_param("i", $sellerId);
$stmt->execute();
$postedBooks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

require "SellerPage.php";
exit;



