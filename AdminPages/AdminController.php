<?php
if (empty($_SESSION['acad_role']) || $_SESSION['acad_role'] !== 'Admin') {
    header("Location: /CampusTradeTTT/HomePage.php");
    exit;
}

require_once 'AdminModel.php';

class AdminController {

    private AdminModel $model;
    
    public function __construct(AdminModel $model){
        $this->model = $model;
    }

    public function showUsers(): void{
        $accounts = $this->model->getAccounts();
        
        echo "<h2>Accounts</h2>";

        if (empty($accounts)) {
        echo "<p>No Accounts found.</p>";
        return;
    }
    // Creating the table with headers
        echo '<table id="accountTable" class="display" style="width:100%">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>ID</th>';
        echo '<th>Email</th>';
        echo '<th>Password</th>';
        echo '<th>first_name</th>';
        echo '<th>last_name</th>';
        echo '<th>School Name</th>';
        echo '<th>Major</th>';
        echo '<th>Acad_role</th>';
        echo '<th>city_state</th>';
        echo '<th>Created At</th>';
        echo '<th>Actions</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

    //Going through each account and filling in information
        foreach ($accounts as $account) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($account['id']) . '</td>';
            echo '<td>' . htmlspecialchars($account['email']) . '</td>';
            echo '<td>' . htmlspecialchars($account['password']) . '</td>';
            echo '<td>' . htmlspecialchars($account['first_name']) . '</td>';
            echo '<td>' . htmlspecialchars($account['last_name']) . '</td>';
            echo '<td>' . htmlspecialchars($account['school_name']) . '</td>';
            echo '<td>' . htmlspecialchars($account['major']) . '</td>';
            echo '<td>' . htmlspecialchars($account['acad_role']) . '</td>';
            echo '<td>' . htmlspecialchars($account['city_state']) . '</td>';
            echo '<td>' . htmlspecialchars($account['created_at']) . '</td>';
            echo '<td>';
            echo '<form method="post" style="display:inline;" onsubmit="return confirm(\'Are you sure you want to delete this Account?\');">';
            echo '<input type="hidden" name="action" value="deleteUser">';
            echo '<input type="hidden" name="user_id" value="' . (int)$account['id'] . '">';
            echo '<button type="submit" class="Button">Delete</button>';
            echo '</form>';
            echo '<a href="AdminEditUser.php?id=' . (int)$account['id'] . '" class="Button">Edit</a>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    }
    public function deleteUser(int $id): void{
        $this->model->deleteUserById($id);
    }

    public function showBookListings(): void{
        $books = $this->model->getBookListings();
        
        echo "<h2>Book Listings</h2>";

        if (empty($books)) {
        echo "<p>No book listings found.</p>";
        return;
    }
    // Creating the table with headers
        echo '<table id="bookTable" class="display" style="width:100%">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>ID</th>';
        echo '<th>Seller ID</th>';
        echo '<th>Title</th>';
        echo '<th>ISBN</th>';
        echo '<th>Price</th>';
        echo '<th>Book State</th>';
        echo '<th>Status</th>';
        echo '<th>Course ID</th>';
        echo '<th>Contact Info</th>';
        echo '<th>Created At</th>';
        echo '<th>Actions</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

    //Going through each listing and filling in information
        foreach ($books as $book) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($book['id']) . '</td>';
            echo '<td>' . htmlspecialchars($book['seller_id']) . '</td>';
            echo '<td>' . htmlspecialchars($book['title']) . '</td>';
            echo '<td>' . htmlspecialchars($book['isbn']) . '</td>';
            echo '<td>' . htmlspecialchars($book['price']) . '</td>';
            echo '<td>' . htmlspecialchars($book['book_state']) . '</td>';
            echo '<td>' . htmlspecialchars($book['status']) . '</td>';
            echo '<td>' . htmlspecialchars($book['course_id']) . '</td>';
            echo '<td>' . htmlspecialchars($book['contact_info']) . '</td>';
            echo '<td>' . htmlspecialchars($book['created_at']) . '</td>';
            echo '<td>';
            echo '<form method="post" style="display:inline;" onsubmit="return confirm(\'Are you sure you want to delete this listing?\');">';
            echo '<input type="hidden" name="action" value="deleteBook">';
            echo '<input type="hidden" name="book_id" value="' . (int)$book['id'] . '">';
            echo '<button type="submit" class="Button">Delete</button>';
            echo '</form>';
            echo '<a href="AdminEditBook.php?id=' . (int)$book['id'] . '" class="Button">Edit</a>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    }
    public function deleteBook(int $id): void{
        $this->model->deleteBookById($id);
    }

    public function showTickets(){
        $tickets = $this->model->getTickets();
        
        echo "<h2>Tickets</h2>";

        if (empty($tickets)) {
        echo "<p>No Tickets found.</p>";
        return;
    }
    // Creating the table with headers
        echo '<table id="ticketTable" class="display" style="width:100%">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>ID</th>';
        echo '<th>Name</th>';
        echo '<th>Email</th>';
        echo '<th>Message</th>';
        echo '<th>Created At</th>';
        echo '<th>Actions</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

    //Going through each listing and filling in information
        foreach ($tickets as $ticket) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($ticket['id']) . '</td>';
            echo '<td>' . htmlspecialchars($ticket['name']) . '</td>';
            echo '<td>' . htmlspecialchars($ticket['email']) . '</td>';
            echo '<td>' . htmlspecialchars($ticket['message']) . '</td>';
            echo '<td>' . htmlspecialchars($ticket['created_at']) . '</td>';
            echo '<td>';
            echo '<form method="post" style="display:inline;" onsubmit="return confirm(\'Are you sure you want to delete this ticket?\');">';
            echo '<input type="hidden" name="action" value="deleteTicket">';
            echo '<input type="hidden" name="ticket_id" value="' . (int)$ticket['id'] . '">';
            echo '<button type="submit" class="Button">Delete</button>';
            echo '</form>';
            echo '<a href="mailto:' . htmlspecialchars($ticket['email']) . '" class="Button">Email</a>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    }
    public function deleteTicket(int $id): void{
        $this->model->deleteTicketById($id);
    }


}

?>
