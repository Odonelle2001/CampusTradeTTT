<?php
session_start();
if (empty($_SESSION['acad_role']) || $_SESSION['acad_role'] !== 'Admin') {
    header("Location: /CampusTradeTTT/HomePage.php");
    exit;
}

$db = require '../Database.php';
require_once 'EditController.php';

$edit = new EditController($db);

$error = null;

// Get book ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    echo "Invalid book ID.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $edit->updateBookFromPost($_POST, $_FILES);
        header('Location: AdminDash.php?action=booklistings');
        exit;
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}


$book = $edit->getBookById($id);
if (!$book) {
    echo "Book not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
    <link rel="stylesheet" href="../CSS/BasicSetUp.css">     
    <link rel="stylesheet" href="../CSS/HeaderNavBar.css">       
    <link rel="stylesheet" href="../CSS/ReusableComponents.css"> 
    <link rel="stylesheet" href="../CSS/LoginForm.css">
    <link rel="stylesheet" href="../CSS/BuyButtonPage.css">
    <link rel="stylesheet" href="../CSS/BuyerPage.css">
    <link rel="stylesheet" href="../CSS/ProfileUpdate.css">
    <link rel= "stylesheet" href="AdminDash.css"> 
</head>
<body>
  <div class="edit-book-wrapper">
    <div class="form-panel edit-book-panel">
      <h2>Edit Book</h2>

      <?php if (!empty($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>

      <form method="post" action="AdminEditBook.php?id=<?= (int)$book['id'] ?>" enctype="multipart/form-data">
        <input type="hidden" name="book_id" value="<?= htmlspecialchars($book['id']) ?>">
        <input type="hidden" name="existing_image" value="<?= htmlspecialchars($book['image_path'] ?? '') ?>">

        <div class="book-upload">
          <input id="bookUpload" name="bookImage" type="file" accept="image/*" hidden>
          <label for="bookUpload" class="book-circle" aria-label="Upload book image">
            <?php if (!empty($book['image_path'])): ?>
              <img id="bookPreview"
                   src="<?= htmlspecialchars($book['image_path']) ?>"
                   alt="Book image preview">
            <?php else: ?>
              <span class="book-plus">+</span>
              <span class="book-hint">Book Image</span>
              <img id="bookPreview" alt="Book image preview" hidden>
            <?php endif; ?>
          </label>
        </div>

        <input type="text" name="titleAuthor" placeholder="Book Title / Author"
               required value="<?= htmlspecialchars($book['title']) ?>">

        <input type="text" name="isbn" placeholder="ISBN"
               value="<?= htmlspecialchars($book['isbn']) ?>">

        <input type="number" step="0.01" name="price" placeholder="Price"
               value="<?= htmlspecialchars($book['price']) ?>">

        <?php $currentState = $book['book_state'] ?? 'New'; ?>
        <select name="condition">
          <option value="New"  <?= $currentState === 'New'  ? 'selected' : '' ?>>New</option>
          <option value="Used" <?= $currentState === 'Used' ? 'selected' : '' ?>>Used</option>
        </select>

        <input type="text" name="courseDept" placeholder="Course Dept."
               value="<?= htmlspecialchars($book['course_id']) ?>">

        <input type="email" name="contact" placeholder="Contact Info"
               value="<?= htmlspecialchars($book['contact_info']) ?>">

        <div class="button-group">
          <button class="button" id="btn" type="submit" name="update_book" value="1">Save Changes</button>
          <a href="AdminDash.php" class="Button">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
