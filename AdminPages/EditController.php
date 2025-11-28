<?php
if (empty($_SESSION['acad_role']) || $_SESSION['acad_role'] !== 'Admin') {
    header("Location: /CampusTradeTTT/HomePage.php");
    exit;
}

class EditController{
    private $db;

    public function __construct(mysqli $db){
        $this->db = $db;
    }

    /* Update an account row based on POST data from the edit form.*/
    public function updateAccountFromPost(array $post): bool{
        $accountId = isset($post['account_id']) ? (int)$post['account_id'] : 0;
        if ($accountId <= 0) {
            throw new InvalidArgumentException('Invalid account ID.');
        }

        // Map form fields -> DB fields
        $first  = trim($post['first_name']   ?? '');
        $last   = trim($post['last_name']    ?? '');
        $acad   = trim($post['acad_role']    ?? 'Student');
        $school = trim($post['school_name']  ?? '');
        $major  = trim($post['major']        ?? '');
        $city   = trim($post['city_state']   ?? '');

        $sql = "
            UPDATE accounts
            SET first_name = ?,
                last_name  = ?,
                acad_role  = ?,
                school_name = ?,
                major      = ?,
                city_state = ?
            WHERE id = ?
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
        throw new RuntimeException('DB error (prepare update): ' . $this->db->error);
        }

        $stmt->bind_param(
            'ssssssi',
            $first,
            $last,
            $acad,
            $school,
            $major,
            $city,
            $accountId
        );
        
        if (!$stmt->execute()) {
            $err = $stmt->error;
            $stmt->close();
            throw new RuntimeException('DB error (execute update): ' . $err);
        }

        $changed = $stmt->affected_rows > 0;

        $stmt->close();

        return $changed;

        }
    public function getBookById(int $bookId): ?array {
        if ($bookId <= 0) {
            throw new InvalidArgumentException('Invalid book ID.');
        }

        $sql = "
            SELECT id, seller_id, title, isbn, image_path, price, book_state,
                   status, course_id, contact_info, created_at
            FROM booklistings
            WHERE id = ?
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new RuntimeException('DB error (prepare getBookById): ' . $this->db->error);
        }

        if (!$stmt->bind_param('i', $bookId)) {
            throw new RuntimeException('DB error (bind getBookById): ' . $stmt->error);
        }

        if (!$stmt->execute()) {
            throw new RuntimeException('DB error (execute getBookById): ' . $stmt->error);
        }

        $result = $stmt->get_result();
        $book = $result->fetch_assoc();
        $stmt->close();

        return $book ?: null;
    }

    public function updateBookFromPost(array $post, array $files): bool {
        $bookId = isset($post['book_id']) ? (int)$post['book_id'] : 0;
        if ($bookId <= 0) {
            throw new InvalidArgumentException('Invalid book ID.');
        }

        $title      = trim($post['titleAuthor'] ?? '');
        $isbn       = trim($post['isbn'] ?? '');
        $price      = isset($post['price']) ? (float)$post['price'] : 0.0;
        $bookState  = trim($post['condition'] ?? 'New');
        $courseId   = trim($post['courseDept'] ?? '');
        $contact    = trim($post['contact'] ?? '');
        $imagePath  = trim($post['existing_image'] ?? '');

        if ($title === '') {
            throw new InvalidArgumentException('Title is required.');
        }

        if (!empty($files['bookImage']['name']) && $files['bookImage']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../uploads/';  // adjust if your path is different
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }

            $fileName   = basename($files['bookImage']['name']);
            $targetPath = $uploadDir . $fileName;

            if (!move_uploaded_file($files['bookImage']['tmp_name'], $targetPath)) {
                throw new RuntimeException('Failed to move uploaded file.');
            }
            $imagePath = 'uploads/' . $fileName;
        }

        $sql = "
            UPDATE booklistings
            SET title       = ?,
                isbn        = ?,
                image_path  = ?,
                price       = ?,
                book_state  = ?,
                course_id   = ?,
                contact_info= ?
            WHERE id = ?
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new RuntimeException('DB error (prepare updateBook): ' . $this->db->error);
        }

        if (!$stmt->bind_param(
            'sssdsssi',
            $title,
            $isbn,
            $imagePath,
            $price,
            $bookState,
            $courseId,
            $contact,
            $bookId
        )) {
            throw new RuntimeException('DB error (bind updateBook): ' . $stmt->error);
        }

        if (!$stmt->execute()) {
            throw new RuntimeException('DB error (execute updateBook): ' . $stmt->error);
        }

        $changed = $stmt->affected_rows > 0;
        $stmt->close();

        return $changed;
}

}

