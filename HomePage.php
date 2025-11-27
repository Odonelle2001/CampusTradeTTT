<?php
session_start();
include('header.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$searchResult = $_SESSION['searchResult'] ?? null;
$searchError  = $_SESSION['searchError'] ?? null;

// optional: clear after showing once
//unset($_SESSION['searchResult'], $_SESSION['searchError']);

// connect to DB
$db = require __DIR__ . '/Database.php';

// Load 12 most recent active books
$featuredBooks = [];

$sql = "
    SELECT 
        b.id,
        b.title,
        b.price,
        b.course_id,
        b.image_path
    FROM booklistings b
    WHERE b.status = 'Active'
    ORDER BY b.created_at DESC
    LIMIT 16
";
$result = $db->query($sql);

while ($row = $result->fetch_assoc()) {
    $featuredBooks[] = $row;
}
// decide where Browse All Books should go
$browseHref = isset($_SESSION['user_id'])
    ? 'buyerpage.php'
    : 'LoginPage.php';
?>

<link rel="stylesheet" href="CSS/HomePage.css">

<!-- Section with Search -->
<div class="hero-section">
  <div class="hero-content">
    <h1 class="hero-title">CampusTrade</h1>
    <p class="hero-subtitle">Buy and sell directly with Minnesota State students</p>

    <form action="Home_Controller.php" class="search-container" method = "POST">
      <span class="search-icon">🔍</span>
      <input type="text" name = "entered_book" placeholder="Search by title, author, ISBN, or course code..." required>
      <button class="search-btn" name = "search_book">Search</button>
    </form>

   <!-- Display the book -->
<div class="Display-container">

  <?php if (!empty($searchError)): ?>
    <p id="flash-error" class="flash-msg error">
      <?= htmlspecialchars($searchError) ?>
    </p>
  <?php endif; ?>

  <?php if (!empty($searchResult)): ?>
  <div class="Display-container">
    <a href="buyButtonpage.php?id=<?= urlencode($searchResult['id']) ?>" class="book-link">
      <div class="book-card flash-msg success" id="flash-success">
        <div class="book-img">
          <img
            src="<?= htmlspecialchars($searchResult['image_path'] ?? 'Images/placeholder-book.png') ?>"
            alt="<?= htmlspecialchars($searchResult['title']) ?>"
          >
        </div>

        <div class="book-title">
          <?= htmlspecialchars($searchResult['title']) ?>
        </div>

        <div class="book-price">
          $<?= htmlspecialchars($searchResult['price']) ?>
        </div>
      </div>
    </a>
  </div>
<?php endif; ?>

</div>


<div class="action-buttons">
      <button class="buy-sell-btn" onclick="window.location.href='buyerpage.php'">Buy</button>
      <button class="buy-sell-btn" onclick="window.location.href='SellerPage.php'">Sell</button>
</div>
  </div>
</div>

<!-- Main Content Section -->
<div class="content-section">
  <!-- Why Campus Trade -->
  <div class="info-box">
    <h2>Why Campus Trade?</h2>
    <p class="intro-text">A student-to-student marketplace built for the Minnesota State system.</p>

    <p>Textbooks are expensive, and buying new isn't always necessary. Campus Trade connects you with other MinnState students who are selling the exact books you need at prices that won't break the bank.</p>

    <div class="features-grid">
      <div class="feature-item">
        <h3>Save Money</h3>
        <p>Buy used textbooks at significantly reduced prices compared to campus bookstores.</p>
      </div>

      <div class="feature-item">
        <h3>Sell Fast</h3>
        <p>List your books quickly and connect with buyers in your campus community.</p>
      </div>

      <div class="feature-item">
        <h3>Stay Local</h3>
        <p>Meet on campus for safe, convenient exchanges. No shipping required.</p>
      </div>

      <div class="feature-item">
        <h3>Verified Students</h3>
        <p>Only MinnState students with verified email addresses can join.</p>
      </div>
    </div>

    <div class="cta-box">
      <p>Join students across Minnesota State colleges who are trading textbooks and saving money each semester.</p>
    </div>
  </div>

   <!-- Featured Books -->
  <div class="books-grid">
    <h3>Featured Books</h3>
    

    <div class="home-book-list">
      <?php foreach ($featuredBooks as $b): ?>
        <?php
          $bookId  = (int)$b['id'];
          $bookUrl = "BuyButtonPage.php?id={$bookId}";  // keep your login logic elsewhere
        ?>

        <a class="home-book-card" href="<?= $bookUrl ?>">
          <div class="home-book-cover-wrapper">
            <?php if (!empty($b['image_path'])): ?>
              <img
                src="<?= htmlspecialchars($b['image_path']) ?>"
                alt="Book cover for <?= htmlspecialchars($b['title']) ?>"
                class="home-book-cover"
              >
            <?php else: ?>
              <div class="home-book-cover home-placeholder">📚</div>
            <?php endif; ?>
          </div>

          <div class="home-book-info">
            <div class="home-book-title">
              <?= htmlspecialchars($b['title']) ?>
            </div>
            <div class="home-book-price">
              $<?= number_format((float)$b['price'], 2) ?>
            </div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>  <!-- end .home-book-list -->
  </div>    <!-- end .books-grid -->
</div>      <!-- end .content-section -->

<?php include('footer.php'); ?>


<?php include('footer.php'); ?>