<?php

class UserModel {
  private mysqli $db;

  // Constructor: accept a mysqli instance
  public function __construct(mysqli $db) {
    $this ->db = $db;
  }

 
  public function CreateAccount(array $data): int {
    // Normalize & validate
    $email  = trim($data['email'] ?? '');
    $pass   = (string)($data['password'] ?? '');
    $first  = trim($data['first_name'] ?? '');
    $last   = trim($data['last_name'] ?? '');
    $school = trim($data['school_name'] ?? '');
    $major  = trim($data['major'] ?? '');
    $city = trim($data['city'] ?? '');

    // Match ENUM casing exactly
    $acad   = (($data['acad_role'] ?? '') === 'Alumni') ? 'Alumni' : 'Student';
    //Verify whether the email is valid, and a minnstate.edu email.
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      throw new InvalidArgumentException('Bad email');
    }

    $domain= "go.minnstate.edu";
    if(!(str_ends_with($email, $domain))){
        throw new InvalidArgumentException('Must be a minnstate.edu email');
    }
    //Ensure that thepassword isn't too long
    if (strlen($pass) < 6) {
      throw new InvalidArgumentException('Password too short');
    }

    //Check is user already exists
    $stmt = $this->db->prepare("SELECT 1 FROM Accounts WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $exists = $stmt->get_result()->fetch_row();

    if ($exists) {
        throw new InvalidArgumentException('Email already exists. Please log in.');
    }

    
    //Hash password before storing
    $hash = password_hash($pass, PASSWORD_DEFAULT);

    $sql_user = "INSERT INTO Accounts
            (email, password, first_name, last_name, school_name, major, acad_role,city_state)
            VALUES (?,?,?,?,?,?,?,?)";

    $stmt = $this->db->prepare($sql_user);
    $stmt->bind_param(
      "ssssssss",
      $email, $hash, $first, $last, $school, $major, $acad, $city
    );
    $stmt->execute();

    return $stmt->insert_id; // >0 on success
  }


//This will verify is the email and passowrd are valid by checking the database before login the user into the website.   
public function VerifyUser(string $email, string $password): array {
    $Email = trim($email);
    $pass  = trim($password);

    $sql_verify = "
        SELECT 
            id,
            email,
            first_name,
            last_name,
            password,
            must_change_password
        FROM accounts
        WHERE email = ?
        LIMIT 1
    ";
    // be consistent with your table name casing (likely 'accounts')
    $sql_verify = "SELECT id, email, first_name, password, acad_role
            FROM accounts
            WHERE email = ?
            LIMIT 1";

    $stmt = $this->db->prepare($sql_verify);
    if (!$stmt) {
        throw new RuntimeException('Failed to prepare statement: ' . $this->db->error);
    }
    $stmt->bind_param("s", $Email);
    $stmt->execute();

    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$user) {
        throw new InvalidArgumentException('Invalid email');
    }

    if (!password_verify($pass, $user['password'])) {
        throw new InvalidArgumentException('Invalid password');
    }

    // return whole row, including must_change_password
    return $user;
}



public function ChangePassword(int $userId, string $newPassword): void {
    // Trim and validate
    $newPassword = trim($newPassword);

    if (strlen($newPassword) < 8) {
        throw new InvalidArgumentException('Password must be at least 8 characters.');
    }

    // Hash new password
    $hash = password_hash($newPassword, PASSWORD_DEFAULT);

    // Prepare SQL update
    $sql = "
        UPDATE accounts
        SET password = ?, must_change_password = 0
        WHERE id = ?
        LIMIT 1
    ";

    $stmt = $this->db->prepare($sql);
    if (!$stmt) {
        throw new RuntimeException('Database error (ChangePassword prepare): ' . $this->db->error);
    }

    if (!$stmt->bind_param('si', $hash, $userId)) {
        throw new RuntimeException('Database error (ChangePassword bind): ' . $stmt->error);
    }

    if (!$stmt->execute()) {
        throw new RuntimeException('Database error (ChangePassword execute): ' . $stmt->error);
    }

    $stmt->close();
}



public function ProfileExtraction(): array {
    if (empty($_SESSION['user_id'])) {
        throw new RuntimeException('Not logged in');
    }
    $id = (int) $_SESSION['user_id'];

    // ---- accounts ----
    $sql = "SELECT first_name, last_name, acad_role, school_name, major, city_state, email
            FROM accounts
            WHERE id = ?
            LIMIT 1";

    $stmt = $this->db->prepare($sql);
    if (!$stmt) throw new RuntimeException("Prep failed: {$this->db->error}");
    $stmt->bind_param("i", $id);             // <- note the ->, not =
    $stmt->execute();
    $acc = $stmt->get_result()->fetch_assoc() ?: [];
    $stmt->close();

    // ---- userprofile (use user_id FK; change to your actual column name) ----
    $pm = $this->PaymentMeth($id);           // returns ['profile_image','preferred_pay'] or []

    // Provide all keys consistently so view never breaks
    return array_merge([
        'first_name'    => null,
        'last_name'     => null,
        'acad_role'     => null,
        'school_name'   => null,
        'major'         => null,
        'city_state'    => null,
        'email'         => null,
        'profile_image' => null,
        'preferred_pay' => null,
    ], $acc, $pm);
}

public function PaymentMeth(int $id): array {
    // CHANGE user_id to match your FK column name
    $sql = "SELECT profile_image, preferred_pay FROM userprofile
            WHERE user_id = ?
            LIMIT 1";
    $stmt = $this->db->prepare($sql);
    if (!$stmt) throw new RuntimeException("Prep failed: {$this->db->error}");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc() ?: [];
    $stmt->close();
    return $row;
}



    //Function saves all the Book information into the database
public function PostBooks(array $books){

      $book_image = $books['book_image'];
      $title = $books['title'];
      $ISBN = $books['isbn'];
      $price = $books['price'];
      $book_status = $books['book_status'];
      $course_dept = $books['course_dept'];
      $contacts = $books['contacts'];

      //Ensure that the data is valid and clean

      $sql_postbook = "INSERT INTO booklisting(image_path, title, isbn,price, book_state, course_id) VALUES(?, ?, ?, ?, ?, ?)";
      $stmt = $this->db->prepare($sql_postbook);
      $stmt -> bind_param("sssiss", $book_image,$title,$ISBN,$price,$book_status,$course_dept, $contacts);
      $stmt -> execute();

      return $stmt->insert_id; // >0 on success
    }
          // Fetch one book row by id 
public function GetBookId(int $id, int $sellerId) {
    $sql = "SELECT * FROM booklistings WHERE id = ? AND seller_id = ? LIMIT 1";

    $stmt = $this->db->prepare($sql);
    if (!$stmt) {
        throw new RuntimeException('Database error (prepare GetBookId): ' . $this->db->error);
    }

    $stmt->bind_param("ii", $id, $sellerId);
    $stmt->execute();

    $result = $stmt->get_result();
    return $result->fetch_assoc();  // assoc array or null
}

public function UpdateBook(array $Book_info, int $sellerId) {
    $id         = (int)$Book_info['id'];
    $title      = $Book_info['titleAuthor'];            
    $isbn       = $Book_info['isbn'];                 
    $priceInt   = (int) round((float)$Book_info['price']); 
    $bookState  = ($Book_info['condition'] === 'Used') ? 'Used' : 'New';  
    $courseDept = $Book_info['courseDept'];            
    $contact    = $Book_info['contact'];              
    $imagePath  = $Book_info['image_path'] ?? '';      // NEW

    $sql_update = "
        UPDATE booklistings
        SET title = ?, isbn = ?, price = ?, book_state = ?, course_id = ?, contact_info = ?, image_path = ?
        WHERE id = ? AND seller_id = ?
        LIMIT 1
    ";

    $stmt = $this->db->prepare($sql_update);
    if (!$stmt) {
        throw new RuntimeException('Database error (prepare UpdateBook): ' . $this->db->error);
    }

    // s = string, i = integer
    if (!$stmt->bind_param(
        "ssissssii",
        $title,
        $isbn,
        $priceInt,
        $bookState,
        $courseDept,
        $contact,
        $imagePath,
        $id,
        $sellerId
    )) {
        throw new RuntimeException('Database error (bind_param UpdateBook): ' . $stmt->error);
    }

    if (!$stmt->execute()) {
        throw new RuntimeException('Database error (execute UpdateBook): ' . $stmt->error);
    }

    // true if row actually changed
    return $stmt->affected_rows > 0;
    }

    public function ReturnBook($book_search){

      $Search = trim($book_search);

      if($Search === ''){
        throw new InvalidArgumentException("Please enter a title or ISBN number.");
      }
      $sql_Search= "SELECT * FROM booklistings 
                    WHERE LOWER(title) LIKE ? OR isbn LIKE ?
                    ORDER BY created_at DESC";

      $stmt = $this -> db ->prepare($sql_Search);

      if(!$stmt){
        throw new RuntimeException('Database error: '. $this->db->error);
      }

      $like = "%" . $Search . "%";
      $stmt -> bind_param("ss", $like, $like);
      $stmt -> execute();

      $returned_book = $stmt -> get_result();
      $book = $returned_book -> fetch_assoc();

      if(!$book){
        throw new InvalidArgumentException("Book not available");
      }
      return $book;
    }
/*
    public function UpdateProfile(array $profile, int $sellerId){

      $first   = trim($profile['first']   ?? '');
      $last    = trim($profile['last']    ?? '');
      $acad    = trim($profile['acad']    ?? 'Student');
      $school  = trim($profile['school']  ?? '');
      $major   = trim($profile['major']   ?? '');
      $citySt  = trim($profile['citySt']  ?? '');
      $payment = trim($profile['payment'] ?? 'Cash');

      $sql_updateacc = "UPDATE accounts SET first_name = ?, last_name = ?, acad_role = ?, school_name = ?, major = ?, city_state = ?
                        WHERE id = ?
                        LIMIT 1";
      $stmt = $this ->db->prepare($sql_updateacc);
      if(!$stmt){
        throw new RuntimeException("Database error: " . $this->db->error);
      }

      $updating = $stmt->bind_param("ssssssi", $first, $last, $acad, $school, $major, $citySt, $sellerId);

      if(!$updating){
        throw new RuntimeException("Database error with updating Profile: " . $this ->db->error);
      }
      if(!$stmt ->execute()){
        throw new RuntimeException("Database error execute UpdateProfile accounts): " . $stmt->db->error);
      }

      $accChanged = $stmt->affected_rows > 0;

      $stmt ->close();

      $payment_changed = UpdatePayment($sellerId, $payment);

      return ($accChanged || $payment_changed);

    }
    public function UpdatePayment(int $sellerId, string $payment){

      $sql_update_prof = "INSERT INTO userprofile (user_id, preferred_pay)
                          VALUES (?, ?)
                          ON DUPLICATE KEY UPDATE preferred_pay = VALUES(preferred_pay)
        ";

        $stmtProf = $this->db->prepare($sql_update_prof);
        if (!$stmtProf) {
            throw new RuntimeException("Database error (prepare UpdateProfile userprofile): " . $this->db->error);
        }

        if (!$stmtProf->bind_param("is", $sellerId, $payment)) {
            throw new RuntimeException("Database error (bind_param UpdateProfile userprofile): " . $stmtProf->error);
        }

        if (!$stmtProf->execute()) {
            throw new RuntimeException("Database error (execute UpdateProfile userprofile): " . $stmtProf->error);
        }

        $profChanged = $stmtProf->affected_rows > 0;
        $stmtProf->close();
        return $profChanged;

    }*/
 public function UpdateProfile(array $profile, int $userId): bool {

    $first   = trim($profile['first']   ?? '');
    $last    = trim($profile['last']    ?? '');
    $acad    = trim($profile['acad']    ?? 'Student');
    $school  = trim($profile['school']  ?? '');
    $major   = trim($profile['major']   ?? '');
    $citySt  = trim($profile['citySt']  ?? '');

    $sql_updateacc = "
        UPDATE accounts 
        SET first_name=?, last_name=?, acad_role=?, school_name=?, major=?, city_state=?
        WHERE id=? LIMIT 1
    ";

    $stmt = $this->db->prepare($sql_updateacc);
    if(!$stmt){
        throw new RuntimeException("prepare UpdateProfile accounts: " . $this->db->error);
    }

    if(!$stmt->bind_param("ssssssi", $first, $last, $acad, $school, $major, $citySt, $userId)){
        throw new RuntimeException("bind UpdateProfile accounts: " . $stmt->error);
    }

    if(!$stmt->execute()){
        throw new RuntimeException("execute UpdateProfile accounts: " . $stmt->error);
    }

    $accChanged = $stmt->affected_rows > 0;
    $stmt->close();

    return $accChanged;
}

public function UpdateProfileAndPayment(array $profile, int $sellerId): bool {

    $accChanged = $this->UpdateProfile($profile, $sellerId);

    $payment = trim($profile['payment'] ?? 'Cash');
    $payChanged = $this->UpdatePayment($sellerId, $payment);

    return ($accChanged || $payChanged);
}

public function UpdateProfileImage(string $imagePath, int $sellerId): bool {
    // If the row may not exist yet, do an UPSERT (insert or update)
    $sql_Profile_img = "
        INSERT INTO userprofile (user_id, profile_image)
        VALUES (?, ?)
        ON DUPLICATE KEY UPDATE profile_image = VALUES(profile_image)
    ";

    $stmt_img = $this->db->prepare($sql_Profile_img);
    if (!$stmt_img) {
        throw new RuntimeException("Database error (prepare UpdateProfileImage): " . $this->db->error);
    }

    if (!$stmt_img->bind_param("is", $sellerId, $imagePath)) {
        throw new RuntimeException("Database error (bind_param UpdateProfileImage): " . $stmt_img->error);
    }

    if (!$stmt_img->execute()) {
        throw new RuntimeException("Database error (execute UpdateProfileImage): " . $stmt_img->error);
    }

    $img_change = $stmt_img->affected_rows > 0;
    $stmt_img->close();

    return $img_change;
}




}

  


?>