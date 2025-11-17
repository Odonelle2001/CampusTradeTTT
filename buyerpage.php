<?php
session_start();
include('header.php');

// connect to DB (mysqli)
$db = require __DIR__ . '/Database.php';

/*
 * 1) Load all active book listings with seller info
 *    We use JOIN to get seller name from accounts.
 */
$books = [];

$sql = "
    SELECT 
        b.id,
        b.title,
        b.price,
        b.book_state,
        b.course_id,
        b.image_path,
        a.first_name,
        a.last_name
    FROM booklistings b
    JOIN accounts a ON b.seller_id = a.id
    WHERE b.status = 'Active'
    ORDER BY b.created_at DESC
";

$result = $db->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
}

/*
 * 2) Build unique department list from course_id (ex: CS101, MATH205)
 */
$depts = [];
foreach ($books as $b) {
    $d = trim($b['course_id'] ?? '');
    if ($d !== '' && !in_array($d, $depts)) {
        $depts[] = $d;
    }
}
sort($depts);
?>
<link rel="stylesheet" href="CSS/BuyerPage.css">

<main class="buyer-page">
  <div class="container-card">
    <!-- top actions inside the cream box -->
    <div class="top-actions">
      <a href="SellerPage.php" class="btn switch">Switch to Seller</a>
      <a href="logout.php" class="btn logout">LogOut</a>
    </div>

    <div class="content-grid">
      <!-- LEFT: Profile -->
      <section class="profile-card">
        <h2>Your Profile</h2>
        <div class="profile-inner">

          <div class="avatar-uploader">
            <label class="avatar">
              <img src="images/avatar-placeholder.png" alt="avatar">
              <span class="avatar-text">Click to upload</span>
            </label>
          </div>

          <ul class="profile-info">
            <li><strong>Name:</strong></li>
            <li><strong>Status:</strong></li>
            <li><strong>School:</strong></li>
            <li><strong>Major:</strong></li>
            <li><strong>Location:</strong></li>
            <li><strong>Email:</strong></li>
            <li><strong>Preferred Payment:</strong></li>
          </ul>

          <!-- Later we can build this page -->
          <button
            type="button"
            class="btn update-profile-btn"
            onclick="window.location.href='EditProfile.php';"
          >
            Update Profile
          </button>
        </div>
      </section>

      <!-- RIGHT: Search + Filter + Library grid -->
      <section class="library-card">
        <div class="library-top">
          <div class="search-wrap">
            <input id="bookSearch" type="text" placeholder="Search by title or seller...">
            <button id="searchBtn" class="icon-btn" aria-label="search">🔍</button>
          </div>

          <select id="filterDept">
            <option value="">All Courses</option>
            <?php foreach($depts as $dept): ?>
              <option value="<?php echo htmlspecialchars($dept); ?>">
                <?php echo htmlspecialchars($dept); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="books-grid-wrapper">
          <div id="booksGrid" class="books-grid">
            <?php if (empty($books)): ?>
              <p style="grid-column:1 / -1; text-align:center; color:#555;">
                No books posted yet.
              </p>
            <?php else: ?>
              <?php foreach($books as $b): ?>
                <div class="book-card"
                     data-id="<?php echo htmlspecialchars($b['id']); ?>"
                     data-title="<?php echo htmlspecialchars(strtolower($b['title'] ?? '')); ?>"
                     data-author="<?php echo htmlspecialchars(strtolower(($b['first_name'] ?? '').' '.($b['last_name'] ?? ''))); ?>"
                     data-dept="<?php echo htmlspecialchars($b['course_id'] ?? ''); ?>">

                  <div class="book-img">
  <?php if (!empty($b['image_path'])): ?>
    <img
      src="<?php echo htmlspecialchars($b['image_path']); ?>"
      alt="Book cover for <?php echo htmlspecialchars($b['title']); ?>">
  <?php else: ?>
    <!-- fallback icon when no image -->
    <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
         xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
      <rect x="3" y="4" width="14" height="16" rx="1.5" fill="#F18305"/>
      <rect x="6" y="7" width="8" height="2" fill="#fff" opacity="0.35"/>
    </svg>
  <?php endif; ?>
</div>


                  <div class="book-title">
                    <?php echo htmlspecialchars($b['title'] ?? ''); ?>
                  </div>
                  <div class="book-author">
                    <?php echo htmlspecialchars($b['course_id'] ?? ''); ?>
                  </div>
                  <div class="book-price">
                    $<?php echo htmlspecialchars($b['price'] ?? '0'); ?>
                  </div>
                </div><!-- /.book-card -->
              <?php endforeach; ?>
            <?php endif; ?>
          </div><!-- /.books-grid -->
        </div><!-- /.books-grid-wrapper -->
      </section><!-- /.library-card -->
    </div><!-- /.content-grid -->
  </div><!-- /.container-card -->
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const searchInput = document.getElementById('bookSearch');
  const deptSelect  = document.getElementById('filterDept');
  const cards       = Array.from(document.querySelectorAll('.book-card'));

  function applyFilters() {
    const q    = (searchInput.value || '').toLowerCase();
    const dept = deptSelect.value;

    cards.forEach(card => {
      const title = card.dataset.title || '';
      const author = card.dataset.author || '';
      const cardDept = card.dataset.dept || '';

      const matchesSearch = !q || title.includes(q) || author.includes(q);
      const matchesDept   = !dept || cardDept === dept;

      card.style.display = (matchesSearch && matchesDept) ? '' : 'none';
    });
  }

  if (searchInput) {
    searchInput.addEventListener('input', applyFilters);
  }
  if (deptSelect) {
    deptSelect.addEventListener('change', applyFilters);
  }

  // make book cards clickable -> BuyButtonPage.php?id=...
  cards.forEach(card => {
    card.addEventListener('click', () => {
      const id = card.dataset.id;
      if (!id) return;
      window.location.href = 'BuyButtonPage.php?id=' + encodeURIComponent(id);
    });
  });
});
</script>
<?php include('footer.php'); ?>
