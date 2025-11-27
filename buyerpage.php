<?php
session_start();
include('header.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: LoginPage.php');
    exit;
}

$db = require __DIR__ . '/Database.php';

$buyerId = (int) $_SESSION['user_id'];

$itemsPerPage = 12;

// Read ?page= from URL, default = 1
$page = isset($_GET['page']) && ctype_digit($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}

/* ---- Count total active books ---- */
$totalBooks = 0;
$totalPages = 1;

$countSql = "SELECT COUNT(*) AS total FROM booklistings WHERE status = 'Active'";
$countResult = $db->query($countSql);
if ($countResult) {
    $row = $countResult->fetch_assoc();
    $totalBooks = (int) ($row['total'] ?? 0);
    $countResult->free();
}

if ($totalBooks > 0) {
    $totalPages = (int) ceil($totalBooks / $itemsPerPage);
} else {
    $totalPages = 1;
}

// If user asks for page bigger than last page, clamp it
if ($page > $totalPages) {
    $page = $totalPages;
}

$offset = ($page - 1) * $itemsPerPage;

/* ---- 1) Load profile for the logged-in user ---- */
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
$stmt->bind_param("i", $buyerId);
$stmt->execute();
$res     = $stmt->get_result();
$profile = $res->fetch_assoc() ?: [];
$stmt->close();

$vImgSrc    = !empty($profile['profile_image']) ? $profile['profile_image'] : 'Images/ProfileIcon.png';
$vFullName  = trim(($profile['first_name'] ?? '') . ' ' . ($profile['last_name'] ?? ''));
$vAcad      = $profile['acad_role']   ?? '';
$vSchool    = $profile['school_name'] ?? '';
$vMajor     = $profile['major']       ?? '';
$vCityState = $profile['city_state']  ?? '';
$vEmail     = $profile['email']       ?? '';
$vPay       = $profile['preferred_pay'] ?? '';

/* ---- 2) Load books (paginated) ---- */
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
    LIMIT ? OFFSET ?
";

$stmt = $db->prepare($sql);
$stmt->bind_param('ii', $itemsPerPage, $offset);
$stmt->execute();
$result = $stmt->get_result();

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
    $result->free();
}

$stmt->close();


/* ---- 3) Build department list ---- */
$depts = [];
foreach ($books as $b) {
    $d = trim($b['course_id'] ?? '');
    if ($d !== '' && !in_array($d, $depts, true)) {
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
      
      <a href="FeedPage.php" class="btn feed">Campus Feed</a>
      
      <a href="logout.php" class="btn logout">LogOut</a>
    </div>

    <div class="content-grid">
      <!-- LEFT: Profile -->
      <section class="profile-card">
        <h2>Your Profile</h2>
        <div class="profile-inner">
          <div class="avatar-uploader">
            <label class="avatar">
              <img src="<?= htmlspecialchars($vImgSrc) ?>" alt="Profile picture">
              <span class="avatar-text">Click to upload</span>
            </label>
          </div>

          <ul class="profile-info">
            <li><strong>Name:</strong> <?= htmlspecialchars($vFullName) ?></li>
            <li><strong>Status:</strong> <?= htmlspecialchars($vAcad) ?></li>
            <li><strong>School:</strong> <?= htmlspecialchars($vSchool) ?></li>
            <li><strong>Major:</strong> <?= htmlspecialchars($vMajor) ?></li>
            <li><strong>Location:</strong> <?= htmlspecialchars($vCityState) ?></li>
            <li><strong>Email:</strong> <?= htmlspecialchars($vEmail) ?></li>
            <li><strong>Preferred Payment:</strong> <?= htmlspecialchars($vPay ?: 'Not set') ?></li>
          </ul>

          <button
            type="button"
            class="btn update-profile-btn"
            onclick="window.location.href='Seller_Controller.php';"
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
            <?php foreach ($depts as $dept): ?>
              <option value="<?= htmlspecialchars($dept) ?>">
                <?= htmlspecialchars($dept) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="books-grid-wrapper">
          <!-- use same grid style as home page -->
          <div id="booksGrid" class="books-grid home-book-list">
            <?php if (empty($books)): ?>
              <p style="grid-column:1 / -1; text-align:center; color:#555;">
                No books posted yet.
              </p>
            <?php else: ?>
              <?php foreach ($books as $b): ?>
                <?php
                  $id        = (int) ($b['id'] ?? 0);
                  $title     = $b['title'] ?? '';
                  $course    = $b['course_id'] ?? '';
                  $price     = isset($b['price']) ? (float)$b['price'] : 0;
                  $sellerName= trim(($b['first_name'] ?? '') . ' ' . ($b['last_name'] ?? ''));
                ?>
                <a
                  class="book-card home-book-card"
                  href="BuyButtonPage.php?id=<?= $id ?>"
                  data-id="<?= $id ?>"
                  data-title="<?= htmlspecialchars(strtolower($title)) ?>"
                  data-author="<?= htmlspecialchars(strtolower($sellerName)) ?>"
                  data-dept="<?= htmlspecialchars($course) ?>"
                >
                  <div class="home-book-cover-wrapper">
                    <?php if (!empty($b['image_path'])): ?>
                      <img
                        src="<?= htmlspecialchars($b['image_path']) ?>"
                        alt="Book cover for <?= htmlspecialchars($title) ?>"
                        class="home-book-cover"
                      >
                    <?php else: ?>
                      <div class="home-book-cover home-placeholder">📚</div>
                    <?php endif; ?>
                  </div>

                  <div class="home-book-info">
                    <div class="home-book-title" title="<?= htmlspecialchars($title) ?>">
                      <?= htmlspecialchars($title) ?>
                    </div>

                    <?php if ($course !== ''): ?>
                      <div class="home-book-course">
                        <?= htmlspecialchars($course) ?>
                      </div>
                    <?php endif; ?>

                    <div class="home-book-price">
                      $<?= number_format($price, 2) ?>
                    </div>
                  </div>
                </a>
              <?php endforeach; ?>
            <?php endif; ?>
          </div><!-- /#booksGrid -->

                  <!-- Pagination -->
        <?php if ($totalBooks > 0 && $totalPages > 1): ?>
          <nav class="pagination-wrapper">
            <?php if ($page > 1): ?>
              <a class="page-link prev" href="?page=<?= $page - 1 ?>">&laquo; Prev</a>
            <?php endif; ?>

            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
              <a
                class="page-link <?= $p === $page ? 'active' : '' ?>"
                href="?page=<?= $p ?>"
              >
                <?= $p ?>
              </a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
              <a class="page-link next" href="?page=<?= $page + 1 ?>">Next &raquo;</a>
            <?php endif; ?>
          </nav>
        <?php endif; ?>

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
      const title   = card.dataset.title  || '';
      const author  = card.dataset.author || '';
      const cardDept= card.dataset.dept   || '';

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
  // cards are <a href="BuyButtonPage.php?id=..."> now, so no extra click handler needed
});
</script>

<?php include('footer.php'); ?>
