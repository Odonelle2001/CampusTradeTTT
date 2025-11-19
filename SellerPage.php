<?php
// sellerpage.php
// This file is meant to be included by Seller_Controller.php
if (!isset($vFirstName) && basename(strtolower($_SERVER['SCRIPT_NAME'])) === 'sellerpage.php') {
    header('Location: Seller_Controller.php');
    exit;
}

$vImgSrc     = $vImgSrc     ?? 'Images/ProfileIcon.png';
$vFirstName  = $vFirstName  ?? '';
$vAcad       = $vAcad       ?? '';
$vSchool     = $vSchool     ?? '';
$vMajor      = $vMajor      ?? '';
$vCityState  = $vCityState  ?? '';
$vEmail      = $vEmail      ?? '';
$vPay        = $vPay        ?? '';
$postedBooks = $postedBooks ?? [];

include('header.php');
?>

<main>
  <div class="container">
    <div class="seller-page">

      <!-- Top Actions -->
      <div class="top-actions">
        <button class="button" type="button" onclick="window.location.href='buyerpage.php'">Switch to Buyer</button>
        <form method="post" action="logout.php" style="display:inline;">
          <button class="button logout" type="submit">LogOut</button>
        </form>
      </div> 

      <!-- LEFT: Profile Panel -->
      <div class="profile-panel">
        <h2>Your Profile</h2>

        <!-- Profile Image Upload -->
        <form id="profileForm" method="POST" enctype="multipart/form-data" action="Seller_Controller.php">
  <div class="avatar-uploader">
    <input id="avatarInput" name="profileImage" type="file" accept="image/*" hidden>
    <label for="avatarInput" class="avatar" aria-label="Upload profile picture">
      <img id="avatarPreview" src="<?= htmlspecialchars($vImgSrc) ?>" alt="Profile picture">
      <span class="avatar-icon">+</span>
    </label>
    <small>
      Step 1: Click the circle to choose a photo.  
      Step 2: It will be saved automatically.
    </small>
  </div>

  <p>Name: <?= htmlspecialchars($vFirstName) ?></p>
  <p>Status: <?= htmlspecialchars($vAcad) ?></p>
  <p>School: <?= htmlspecialchars($vSchool) ?></p>
  <p>Major: <?= htmlspecialchars($vMajor) ?></p>
  <p>Location: <?= htmlspecialchars($vCityState) ?></p>
  <p>Email: <?= htmlspecialchars($vEmail) ?></p>
  <p>Preferred Payment: <?= htmlspecialchars($vPay) ?></p>

  <!-- hidden flag so the controller knows this is a profile update -->
  <input type="hidden" name="edit_profile" value="1">

  <button class="button" type="submit">Update Profile</button>
</form>


        <!-- Posted Books -->
        <h3>Books Posted</h3>
        <form method="post" action="Seller_Controller.php">
          <select name="postedBook">
            <option value="">Select Book</option>
            <?php foreach ($postedBooks as $b): ?>
              <option value="<?= htmlspecialchars($b['id']) ?>">
                <?= htmlspecialchars($b['title']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <button class="button delete" type="submit" name="delete_book" value="1">Delete Book</button>
          <button class="button delete" type="submit" name="edit_book" value="1">Edit Book</button>

        </form>
      </div>

      <!-- RIGHT: Post a Book -->
      <div class="form-panel">
        <h2>Post a Book</h2>

    <form method="post" enctype="multipart/form-data" action="Seller_Controller.php">
  <div class="book-upload">
    
    <input id="bookUpload" name="bookImage" type="file" accept="image/*" hidden>
    <label for="bookUpload" class="book-circle" aria-label="Upload book image">
      <span class="book-plus">+</span>
      <span class="book-hint">Book Image</span>
      <img id="bookPreview" alt="Book image preview" hidden>
    </label>
  </div>

  <input type="text" name="titleAuthor" placeholder="Book Title / Author" required>
  <input type="text" name="isbn" placeholder="ISBN">
  <input type="number" step="0.01" name="price" placeholder="Price">
  <select name="condition">
    <option value="New">New</option>
    <option value="Used">Used</option>
  </select>
  <input type="text" name="courseDept" placeholder="Course Dept.">
  <input type="email" name="contact" placeholder="Contact Info">

  <div class="button-group">
    <button class="button" type="submit" name="post_book" value="1">Post Book</button>


  </div>
</form>


    </div>
  </div>
</main>

<?php include('footer.php'); ?>

<!-- ========= IMAGE PREVIEW SCRIPT ========= -->
<script>
document.addEventListener('DOMContentLoaded', () => {
  // Profile avatar preview
  const avatarInput = document.getElementById('avatarInput');
  const avatarPreview = document.getElementById('avatarPreview');

  if (avatarInput && avatarPreview) {
    avatarInput.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = (ev) => {
        avatarPreview.src = ev.target.result;
      };
      reader.readAsDataURL(file);
    });
  }

  // Book image preview
  const bookInput = document.getElementById('bookUpload');
  const bookPreview = document.getElementById('bookPreview');

  if (bookInput && bookPreview) {
    bookInput.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = (ev) => {
        bookPreview.src = ev.target.result;
        bookPreview.hidden = false;
      };
      reader.readAsDataURL(file);
    });
  }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  // Profile avatar preview
  const avatarInput = document.getElementById('avatarInput');
  const avatarPreview = document.getElementById('avatarPreview');

  if (avatarInput && avatarPreview) {
    avatarInput.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = (ev) => {
        avatarPreview.src = ev.target.result;
      };
      reader.readAsDataURL(file);
    });
  }

  // Book image preview
  const bookInput   = document.getElementById('bookUpload');
  const bookPreview = document.getElementById('bookPreview');

  if (bookInput && bookPreview) {
    bookInput.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = (ev) => {
        bookPreview.src = ev.target.result;
        bookPreview.hidden = false;   // just show it under the +
      };
      reader.readAsDataURL(file);
    });
  }

    });

</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  // Profile avatar preview + auto-save
  const avatarInput   = document.getElementById('avatarInput');
  const avatarPreview = document.getElementById('avatarPreview');
  const profileForm   = document.getElementById('profileForm');

  if (avatarInput && avatarPreview && profileForm) {
    avatarInput.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (!file) return;

      // Show preview immediately
      const reader = new FileReader();
      reader.onload = (ev) => {
        avatarPreview.src = ev.target.result;
      };
      reader.readAsDataURL(file);

      // Auto-submit form so image is saved to DB
      setTimeout(() => {
        profileForm.submit();
      }, 300); // tiny delay so preview appears smoothly
    });
  }
});
</script>


<?php include('footer.php'); ?>

