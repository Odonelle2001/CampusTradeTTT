<?php
// Seller_Controller.php
session_start();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$db = require __DIR__ . '/Database.php';

require __DIR__ . '/UserModel.php';

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    // Windows (XAMPP)
    $UPLOAD_ROOT = "C:/xampp/htdocs/CampusTradeTTT/Uploads/";
} else {
    // macOS (XAMPP/MAMP)
    // __DIR__ is "/Applications/XAMPP/xamppfiles/htdocs/CampusTradeTTT"
    $UPLOAD_ROOT = __DIR__ . "/Uploads/";
}


$userModel = new UserModel($db);

// =========================
//  Require login
// =========================
if (!isset($_SESSION['user_id'])) {
    header("Location: LoginPage.php");
    exit;
}

$sellerId = (int) $_SESSION['user_id'];

/* ============================================
   1) LOAD PROFILE DATA (accounts + userprofile)
   ============================================ */

$profileSql = "
    SELECT 
        a.first_name,
        a.last_name,
        a.school_name,
        a.major,
        a.acad_role,
        a.city_state,
        a.email,
        u.profile_image,
        u.preferred_pay
    FROM accounts a
    LEFT JOIN userprofile u ON u.user_id = a.id
    WHERE a.id = ?
";

$stmt = $db->prepare($profileSql);
$stmt->bind_param("i", $sellerId);
$stmt->execute();
$res = $stmt->get_result();
$profile = $res->fetch_assoc() ?: [];
$stmt->close();

$vImgSrc    = !empty($profile['profile_image']) ? $profile['profile_image'] : 'Images/ProfileIcon.png';
$vFirstName = trim(($profile['first_name'] ?? '') . ' ' . ($profile['last_name'] ?? ''));
$vAcad      = $profile['acad_role']   ?? '';
$vSchool    = $profile['school_name'] ?? '';
$vMajor     = $profile['major']       ?? '';
$vCityState = $profile['city_state']  ?? '';
$vEmail     = $profile['email']       ?? '';
$vPay       = $profile['preferred_pay'] ?? '';

/* ============================================
   2) HANDLE POST REQUESTS
   ============================================ */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* ---- A) POST A BOOK ---- */
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

        // price is UNSIGNED INT in DB
        $price = (int) round((float)$priceInput);
        if ($price < 0) $price = 0;

        $bookState = ($condition === 'Used') ? 'Used' : 'New';
        $status    = 'Active';

        // ---------- BOOK IMAGE UPLOAD ----------
        $imagePath = null;

        if (!empty($_FILES['bookImage']['name'])) {

            $file  = $_FILES['bookImage'];
            $error = $file['error'];

            if ($error === UPLOAD_ERR_OK && $file['size'] > 0) {

                // 🔹 Absolute path to Books folder (must match your real path)
                $uploadDir = $UPLOAD_ROOT . "Books/";
                $webPrefix = "Uploads/Books/";


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
                    $imagePath = $webPrefix . $fileName; // relative path for src + DB
                } else {
                    die('move_uploaded_file failed for book image. Tried: ' . $fullPath);
                }
            } elseif ($error !== UPLOAD_ERR_NO_FILE) {
                die('Upload error for bookImage. Error code: ' . $error);
            }
        }

        // ---------- INSERT BOOK ----------
        $sql = "
            INSERT INTO booklistings
                (seller_id, title, isbn, image_path, price, book_state, status, course_id, contact_info)
            VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?)
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
            $uploadDir = $UPLOAD_ROOT . "Profiles/";
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
        //Edit the book
if (isset($_POST['edit_book'])) {
    $booktoEdit = isset($_POST['postedBook']) ? (int) $_POST['postedBook'] : 0;

    if ($booktoEdit <= 0) {
        $_SESSION['error'] = "Please select a book to edit.";
        header("Location: Seller_Controller.php");
        exit;
    }

    // Fetch the full book row (filter by seller for safety)
    $book = $userModel->GetBookId($booktoEdit, $sellerId);

    if (!$book) {
        $_SESSION['error'] = "Book not found.";
        header("Location: Seller_Controller.php");
        exit;
    }

    include 'Edit_book.php';  // $book is now available in that file
    exit;
}


if (isset($_POST['update_book'])) {

    $Book_info = [
        'id'          => isset($_POST['book_id']) ? (int) $_POST['book_id'] : 0,
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

    try {
        $Edit_Book = $userModel->UpdateBook($Book_info, $sellerId);

        if ($Edit_Book) {
            $_SESSION['success'] = "Book updated successfully.";
        } else {
            $_SESSION['error'] = "Failed to update book (no rows changed).";
        }

        header("Location: Seller_Controller.php");
        exit;

    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: Seller_Controller.php");
        exit;
    }
}

}

/* ============================================
   3) LOAD SELLER'S BOOK LIST (for dropdown)
   ============================================ */

$postedBooks = [];
$sql = "SELECT id, title FROM booklistings WHERE seller_id = ? ORDER BY created_at DESC";
$stmt = $db->prepare($sql);
$stmt->bind_param("i", $sellerId);
$stmt->execute();
$res = $stmt->get_result();
$postedBooks = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();

/* ============================================
   4) RENDER SELLER PAGE
   ============================================ */
include 'sellerpage.php';


