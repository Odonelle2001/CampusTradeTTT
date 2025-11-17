<?php

class UserModel {
  private mysqli $db;

  // Constructor: accept a PDO instance
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

    // be consistent with your table name casing (likely 'accounts')
    $sql_verify = "SELECT id, email, first_name, password
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

    return $user; // return the row so controller can set session fields
}


    public function ChangePassword(string $password){
    
    
    }


    /*
    public function ProfileExtraction(): array{
    $User_profile = [];

    if (empty($_SESSION['user_id'])) {
      throw new RuntimeException('Not logged in');
    }
    $id = (int) $_SESSION['user_id'];

      $sql_Profile = "SELECT * FROM accounts WHERE id = ? LIMIT = 1";

      $stmt = $this->db->prepare($sql_Profile);

      //Handle any Database failures
      if(!$stmt){
        throw new RuntimeException("Database Preparation failed: {$this->db->error}");
      }
      $stmt = bind_param("i",$id);
      $stmt ->execute(); 

      $profile = $stmt -> get_result()-> fetch_assoc() ?? [];

      $Profile_Method = PaymentMeth($id);
      $User_img = $Profile_Method['profile_image'] ?? null;
      $User_Name = $profile['first_name'] ?? null;
      $User_Acad = $profile['acad_role'] ?? null;
      $User_School = $profile['school_name'] ?? null;
      $User_Major = $profile['major'] ?? null;
      $User_Location = $profile['city_state'] ?? null;
      $User_Email = $profile['email'] ?? null;
      $User_Payment = $Profile_Method['preferred_pay'] ?? null;

      $User_profile = [
        $User_img,
        $User_Name,
        $User_Acad,
        $User_School,
        $User_Major,
        $User_Location,
        $User_Email,
        $User_Payment,
      ];

      return $User_profile;

    }

    public function PaymentMeth(int $id){
      //SQL Query to extract the users profile picture and preffered Payment Method
      $sql_Profile_ad = "SELECT * FROM userprofile WHERE $int = ? LIMIT = 1";

      $stmt = $this->db->prepare($sql_Profile_ad);

      //Handle any DB issues
      if(!$stmt){
        throw new RuntimeException("Database Preparation failed: {$this->db->error}");
      }
      $stmt = bind_param("i", $int);
      $stmt -> execute();

      //Fetch all the necessary data from the databse 
      $Payment_method = $stmt -> get_result()-> fetch_assoc();

      return $Payment_method;
    } */
   
  // UserModel.php

public function ProfileExtraction(): array {
    if (empty($_SESSION['user_id'])) {
        throw new RuntimeException('Not logged in');
    }
    $id = (int) $_SESSION['user_id'];

    // ---- accounts ----
    $sql = "SELECT first_name, acad_role, school_name, major, city_state, email
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

    public function UpdateProfileImage(){

    }
    }

    


?>