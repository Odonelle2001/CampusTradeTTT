<?php

class ContactPageController
{
    private mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    public function submitTicket(): void
    {
        // Validation
        $name    = trim($_POST['name'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if ($name === '' || $email === '' || $message === '') {
            $_SESSION['ticket_error'] = "All fields are required.";
            header("Location: ContactPage.php");
            exit;
        }

        // Insert into tickets table
        $stmt = $this->db->prepare(
            "INSERT INTO tickets (name, email, message) VALUES (?, ?, ?)"
        );
        if (!$stmt) {
            $_SESSION['ticket_error'] = "Server error. Please try again later.";
            header("Location: ContactPage.php");
            exit;
        }

        $stmt->bind_param('sss', $name, $email, $message);
        $stmt->execute();
        $stmt->close();

        $_SESSION['ticket_success'] = "Your message has been submitted! We’ll get back to you soon.";
        header("Location: ContactPage.php");
        exit;
    }
}
