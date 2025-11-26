<?php

class AdminModel{
    private mysqli $db;

    public function __construct(mysqli $db){
        $this->db = $db;
    }

    public function getBookListings(): array{
        $data = [];
        $sql = "SELECT id, seller_id, title, isbn, image_path, price, book_state, status, course_id, contact_info, created_at
                FROM booklistings";
        $result = $this->db->query($sql);

        if ($result){
            while ($row = $result->fetch_assoc()){
                $data[] = $row;
            }
            $result->free();
        }

        return $data;
    }

    public function deleteBookById(int $id): bool{
        $sql = "DELETE FROM booklistings WHERE id = ?";
        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    
    }

       public function getAccounts(): array{
        $data = [];
        $sql = "SELECT id, email, password, first_name, last_name, school_name, major, acad_role, city_state, created_at
                FROM accounts";
        $result = $this->db->query($sql);

        if ($result){
            while ($row = $result->fetch_assoc()){
                $data[] = $row;
            }
            $result->free();
        }

        return $data;
    }

    public function deleteUserById(int $id): bool{
        $sql = "DELETE FROM accounts WHERE id = ?";
        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    public function getTickets(): array
{
    $sql = "SELECT id, name, email, message, created_at 
            FROM tickets
            ORDER BY created_at DESC";

    $result = $this->db->query($sql);

    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}






}



?>
