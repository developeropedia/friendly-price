<?php
class User {
  private $db;

  public function __construct() {
    $this->db = Database::getInstance();
  }

  // Add User / Register
  public function register($data, $file) {
    // Prepare Query
    $query = "INSERT INTO users (fname, lname, username, email, password, user_level_id";

    $filename = "";
    if (isset($file) && !empty($file['image']["name"])) {
      $filename = $file['image']['name'];
      $filename = uniqid() . "-" . $filename;
      $dir = "../public/images/";
      if (move_uploaded_file($file["image"]["tmp_name"], $dir . $filename)) {
        $query .= ", img";
      }
    }

    $query .= ") VALUES (:fname, :lname, :username, :email, :password, :user_level_id";

    if (!empty($filename)) {
      $query .= ", :img";
    }

    $query .= ")";

    $this->db->query($query);

    // Bind Values
    $this->db->bind(':fname', $data['fname']);
    $this->db->bind(':lname', $data['lname']);
    $this->db->bind(':username', $data['username']);
    $this->db->bind(':email', $data['email']);
    $this->db->bind(':password', $data['password']);
    $this->db->bind(':user_level_id', $data['user_level_id']);

    if (!empty($filename)) {
      $this->db->bind(":img", $filename);
    }

    //Execute
    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }

  // Login / Authenticate User
  public function login($data) {
    $this->db->query("SELECT * FROM users WHERE email = :email");
    $this->db->bind(':email', $data["email"]);

    $row = $this->db->single();

    $hashed_password = $row->password;
    if (password_verify($data["password"], $hashed_password)) {
      return $row;
    } else {
      return false;
    }
  }

  public function updateToken($token, $email) {
    $query = "UPDATE users SET confirmation_token = :token WHERE email = :email";
    $this->db->query($query);
    $this->db->bind(":token", $token);
    $this->db->bind(":email", $email);
    $this->db->execute();
  }

  // Find USer BY Email
  public function findUserByUsername($username) {
    $this->db->query("SELECT * FROM users WHERE username = :username");
    $this->db->bind(':username', $username);

    $row = $this->db->single();

    //Check Rows
    if ($this->db->rowCount() > 0) {
      return true;
    } else {
      return false;
    }
  }

  // Find USer BY Email
  public function findUserByEmail($email) {
    $this->db->query("SELECT * FROM users WHERE email = :email");
    $this->db->bind(':email', $email);

    $row = $this->db->single();

    //Check Rows
    if ($this->db->rowCount() > 0) {
      return true;
    } else {
      return false;
    }
  }

  // Find User By ID
  public function getUserById($id) {
    $this->db->query("SELECT *, users.course AS courseID, users.id as userID, users.name as userName, courses.name as course FROM users INNER JOIN courses ON courses.id = users.course WHERE users.id = :id");
    $this->db->bind(':id', $id);

    $user = $this->db->single();

    return $user;
  }

  public function getTotalUsers() {
    $query = "SELECT COUNT(*) AS totalUsers FROM users WHERE is_admin = 0";
    $this->db->query($query);

    return $this->db->single();
  }

  public function editProfile($user_data, $file) {
    $name = $user_data['name'];
    $email = $user_data['email'];
    $password = $user_data['password'];
    $course = $user_data['course'];
    $qualification = $user_data['qualification'];
    $institution = $user_data['institution'];

    $query = "UPDATE users SET name = :name, email = :email, course = :course,
      qualification = :qualification, institution = :institution";

    if (!empty($password)) {
      $query .= ", password = :password";
    }

    $filename = "";
    if (!empty($file['image']["name"])) {
      $filename = $file['image']['name'];
      $filename = uniqid() . "-" . $filename;
      $dir = "../public/images/";
      if (move_uploaded_file($file["image"]["tmp_name"], $dir . $filename)) {
        $query .= ", img = :img";
      }
    }

    $query .= " WHERE id = :user_id";

    $this->db->query($query);
    $this->db->bind(":user_id", $_SESSION['user_id']);
    $this->db->bind(":name", $name);
    $this->db->bind(":email", $email);
    $this->db->bind(":course", $course);
    $this->db->bind(":qualification", $qualification);
    $this->db->bind(":institution", $institution);

    if (!empty($password)) {
      $password = password_hash($password, PASSWORD_BCRYPT);
      $this->db->bind(":password", $password);
    }

    if (!empty($filename)) {
      $this->db->bind(":img", $filename);
    }

    return $this->db->execute();
  }

  public function deleteUser($id) {
    $query = "UPDATE users SET status = 0 WHERE id = :id";
    $this->db->query($query);
    $this->db->bind(":id", $id);
    return $this->db->execute();
  }

  public function editUser($user_data) {
    $id = $user_data['id'];
    $name = $user_data['name'];
    $email = $user_data['email'];
    $course = $user_data['course'];
    $qualification = $user_data['qualification'];
    $institution = $user_data['institution'];

    $query = "UPDATE users SET name = :name, email = :email, course = :course,
        qualification = :qualification, institution = :institution";

    $query .= " WHERE id = :user_id";

    $this->db->query($query);
    $this->db->bind(":user_id", $id);
    $this->db->bind(":name", $name);
    $this->db->bind(":email", $email);
    $this->db->bind(":course", $course);
    $this->db->bind(":qualification", $qualification);
    $this->db->bind(":institution", $institution);

    return $this->db->execute();
  }

  public function confirmEmail($token) {
    $query = "SELECT id FROM users WHERE confirmation_token = :token LIMIT 1";
    $this->db->query($query);
    $this->db->bind(":token", $token);
    $this->db->execute();
    return $this->db->single();
  }

  public function updateConfirmed($id) {
    $query = "UPDATE users SET is_confirmed = 1, confirmation_token = null WHERE id = :id";
    $this->db->query($query);
    $this->db->bind(":id", $id);
    $this->db->execute();
  }

  public function hasPrivilege($user_level_id, $privilege_name) {
    $query = "SELECT * FROM privileges p INNER JOIN user_privileges up ON up.privilege_id = p.id
    WHERE p.name = :privilege_name AND up.user_level_id = :user_level_id";
    $this->db->query($query);
    $this->db->bind(":privilege_name", $privilege_name);
    $this->db->bind(":user_level_id", $user_level_id);
    $this->db->execute();
    
    if($this->db->rowCount() > 0) {
      return true;
    } else {
      return false;
    }
  }
}
