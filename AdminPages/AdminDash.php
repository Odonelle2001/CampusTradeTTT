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

// Post methods for Deletion!
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? null;

    if ($postAction === 'deleteBook') {
        $bookId = (int)($_POST['book_id'] ?? 0);

        if ($bookId > 0) {
            $controller->deleteBook($bookId);
        }

        // Refreshes Page
        $action = 'booklistings';

    } elseif ($postAction === 'deleteUser') {
        $userId = (int)($_POST['user_id'] ?? 0);

        if ($userId > 0) {
            $controller->deleteUser($userId);
        }

        $action = 'users';
        
    } elseif ($postAction === 'deleteTicket') {
    $ticketId = (int)($_POST['ticket_id'] ?? 0);

    if ($ticketId > 0) {
        $controller->deleteTicket($ticketId);
    }

    
    $action = 'tickets';
}

} else {
        //GET action for tables
        $action = $_GET['action'] ?? null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>

<link rel= "stylesheet" href="AdminDash.css"> 
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>

</head>

<body>

<header>

    <div class="NavHead">
        <img src="/CampusTradeTTT/Images/CampusTradeLogo.png" alt="Logo">
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
        $('.display').DataTable({
                  scrollX: true
    });
    });
</script>


</body>
</html>