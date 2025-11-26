
<?php
session_start();
if (empty($_SESSION['acad_role']) || $_SESSION['acad_role'] !== 'Admin') {
    header("Location: /CampusTradeTTT/HomePage.php");
    exit;
}

require '../Database.php';
require_once 'AdminModel.php';
require_once 'AdminController.php';

$model = new AdminModel($db);
$controller = new AdminController($model);

// Decide what action to run
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submissions (like Delete)
    $postAction = $_POST['action'] ?? null;

    if ($postAction === 'deleteBook') {
        $bookId = (int)($_POST['book_id'] ?? 0);

        if ($bookId > 0) {
            $controller->deleteBook($bookId);
        }

        // After deleting, show the Book Listings again
        $action = 'booklistings';

    } elseif ($postAction === 'deleteUser') {
        $userId = (int)($_POST['user_id'] ?? 0);

        if ($userId > 0) {
            $controller->deleteUser($userId);
        }

        // After deleting, show the Users table again
        $action = 'users';

    } else {
        // Fallback to GET action if needed
        $action = $_GET['action'] ?? null;
    }
} else {
    // Normal button clicks (Users, Book Listings, Tickets)
    $action = $_GET['action'] ?? null;
}
?>

<head>

<link rel= "stylesheet" href="AdminDash.css"> 
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>


</head>


<body>

    <header>

    <div class="NavHead">
        <img src="/CampusTradeTTT\Images\CampusTradeLogo.png" alt="Logo">
        <h1>Admin Dashboard</h1>
        <nav>
            <a href="../HomePage.php">Home</a>
        </nav>
    </div>

    </header>

    <div class="Buttons">
        <form method="get">
            <button type="submit" name="action" value="users">Users</button>
            <button type="submit" name="action" value="booklistings">Book Listings</button>
            <button type="submit" name="action" value="tickets">Tickets</button>
        </form>
    </div>

    <div class="Content">

    <?php
    if ($action === "users") {
        $controller->showUsers();
    }

    if ($action === "booklistings") {
        $controller->showBookListings();
    }

    if ($action === "tickets") {
        $controller->showTickets();
    }
    ?>

    </div>

<script>
    $(document).ready(function() {
        $('.display').DataTable();
                  scrollX: true
    });
</script>


</body>