 <?php
// Seller_Controller.php
session_start();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$db = require __DIR__ . '/Database.php';

// Make sure user is logged in
if (empty($_SESSION['user_id'])) {
    header('Location: LoginPage.php');
    exit;
}

$sellerId = (int)$_SESSION['user_id'];

/* ==========================
   1) LOAD PROFILE DATA
   ========================== */

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
$res     = $stmt->get_result();
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

/* ==========================
   2) HANDLE FORM POSTS
   ========================== */

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

        // price column is UNSIGNED INT
        $price = (int) round((float) $priceInput);
        if ($price < 0) $price = 0;

        $bookState = ($condition === 'Used') ? 'Used' : 'New';
        $status    = 'Active';

        // Optional image upload for book
        $imagePath = null;
        if (!empty($_FILES['bookImage']['name']) && $_FILES['bookImage']['error'] === UPLOAD_ERR_OK) {

            // Filesystem folder
            $uploadDir = __DIR__ . '/Uploads/Books/';
            // Path to store in DB / use in <img src="">
            $webPrefix = 'Uploads/Books/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $ext      = pathinfo($_FILES['bookImage']['name'], PATHINFO_EXTENSION);
            $fileName = 'book_' . $sellerId . '_' . time() . '.' . $ext;
            $fullPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['bookImage']['tmp_name'], $fullPath)) {
                $imagePath = $webPrefix . $fileName;
            }
        }

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
        if (!empty($_FILES['profileImage']['name']) && $_FILES['profileImage']['error'] === UPLOAD_ERR_OK) {

            $uploadDir = __DIR__ . '/Uploads/avatars/';
            $webPrefix = 'Uploads/avatars/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $ext      = pathinfo($_FILES['profileImage']['name'], PATHINFO_EXTENSION);
            $fileName = 'avatar_' . $sellerId . '_' . time() . '.' . $ext;
            $fullPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['profileImage']['tmp_name'], $fullPath)) {
                $newImagePath = $webPrefix . $fileName;
            }
        }

        if ($newImagePath !== null) {
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
}

/* ==========================
   3) LOAD SELLER'S BOOKS
   ========================== */
$postedBooks = [];
$sql = "SELECT id, title FROM booklistings WHERE seller_id = ? ORDER BY created_at DESC";
$stmt = $db->prepare($sql);
$stmt->bind_param("i", $sellerId);
$stmt->execute();
$res = $stmt->get_result();
$postedBooks = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();

/* ==========================
   4) SHOW SELLER PAGE VIEW
   ========================== */
include 'sellerpage.php';

