<?php
  class Users extends Controller {
    private $userModel;
    private $courseModel;

    public function __construct(){

        $this->userModel = $this->model("User");
    }

    public function index()
    {
        
    }
    
    public function register()
    {
        if($this->isLoggedIn()) {
            die("Already logged in!");
            redirect("pages");
        }

        if($_SERVER["REQUEST_METHOD"] === "POST") {
            $data["username"] = $_POST['username'];
            $data["fname"] = $_POST['fname'];
            $data["lname"] = $_POST['lname'];
            $data["email"] = $_POST['email'];
            $data["password"] = $_POST['password'];
            $data["user_level_id"] = $_POST['user_level_id'];
            $data["error"] = "";
            $data["title"] = "Registration";

            if($this->userModel->findUserByUsername($data["username"])) {
                $data["error"] = "This username is already taken!";
            }

            if($this->userModel->findUserByEmail($data["email"])) {
                $data["error"] = "This email is already registered!";
            }

            if(empty($data["error"])) {
                $data["password"] = password_hash($data["password"], PASSWORD_BCRYPT);

                if($this->userModel->register($data, $_FILES)) {
                    flash("register_success", "Check your mailbox for confirmation email!");

                    $token = md5(uniqid() . $data['email']);
                    $body = "Thank you for registering. Please click the link below to confirm your email address:<br><br>";
                    $body .= "<a href='". URLROOT ."/users/confirm/$token'>Confirm Email</a>";

                    $this->userModel->updateToken($token, $data['email']);

                    die("User is registered successfully!");
                    redirect("users/login");
                }
            } else {
                echo json_encode($data);
                die();
                $this->view("users/register", $data);
            }
        } else {
            $data["title"] = "Registration";
            $data["error"] = "";
            $this->view('users/register', $data);
        }
    }

    public function login()
    {
        if($this->isLoggedIn()) {
            die("Already logged in!");
            redirect("pages");
        }

        if($_SERVER["REQUEST_METHOD"] === "POST") {
            $data["email"] = $_POST['email'];
            $data["password"] = $_POST['password'];
            $data["error"] = "";
            $data["title"] = "Login";

            $user = $this->userModel->findUserByEmail($data["email"]);

            if(!empty($user)) {
                $loggedInUser = $this->userModel->login($data);

                if($loggedInUser) {
                    $this->createUserSession($loggedInUser);
                    echo json_encode($data + $_SESSION);
                    die("Logged in successfully!");
                    redirect("pages");
                } else {
                    $data["error"] = "Email or password is incorrect!";
                }
            } else {
                $data["error"] = "Email or password is incorrect!";
            }

            echo json_encode($data);
            die();
            $this->view("users/login", $data);
        } else {
            $data["title"] = "Login";
            $data["error"] = "";
            $this->view('users/login', $data);
        }
    }

    public function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    public function createUserSession($user){
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_email'] = $user->email; 
        $_SESSION['user_fname'] = $user->fname;
        $_SESSION['user_lname'] = $user->lname;
        $_SESSION['user_username'] = $user->username;
        $_SESSION['user_level_id'] = $user->user_level_id;
    }

    public function logout() {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_fname']);
        unset($_SESSION['user_lname']);
        unset($_SESSION['user_username']);
        unset($_SESSION['user_level_id']);

        die("User logged out!");
        redirect("users/login");
    }

    public function profile()
    {
        if(!$this->isLoggedIn()) {
            redirect("users/login");
        }
        $user = $this->userModel->getUserById($_SESSION['user_id']);
        $courses = $this->courseModel->getCourses();

        $this->view('users/profile', ["title" => "Profile", "user" => $user, "courses" => $courses]);
    }

    public function editProfile()
    {
        if (!$this->isLoggedIn()) {
            redirect("users/login");
        }

        $user_data = $_POST;
        $file = $_FILES;

        $res = $this->userModel->editProfile($user_data, $file);
        if($res) {
            $_SESSION['user_name'] = $user_data['name'];
            $_SESSION['user_email'] = $user_data['email']; 
            redirect("users/profile");
        }
    }

    public function confirm($token) {
        $res = $this->userModel->confirmEmail($token);
        if(!empty($res)) {
            $this->userModel->updateConfirmed($res->id);
            flash("email_confirmed", "Email confirmed successfully!");
            redirect("users/login");
        } else {
            flash("email_not_confirmed", "Email could not be confirmed!", "errorMsg");
            redirect("users/login");
        }
    }

    public function create() {
        $hasPrivilege = $this->userModel->hasPrivilege($_SESSION['user_level_id'], "create_user");

        if(!$hasPrivilege) {
            die("You don't have permission to create user!");
        } else {
            die("You have the permission to create user!");
        }
    }
  }
