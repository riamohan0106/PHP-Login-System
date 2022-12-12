<?php

    require_once '../models/User.php';
    require_once '../helpers/session_helper.php';

    class  Users {

        private $userModel;
        
        public function __construct(){
            $this->userModel = new User;
        }

        public function register(){
            //Process form
            
            //Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
//            need to sanitize seperately


            //Init data -- contains the values the user sends from the post request
            $data = [
                'usersFName' => trim($_POST['usersFName']),
                'usersLName' => trim($_POST['usersLName']),
                'usersGender' => trim($_POST['usersGender']),
                'usersMobile' => ($_POST['usersMobile']),
                'usersDob' => ($_POST['usersDob']),
//                'usersImage' => ($_POST['usersImage']),
                'usersEmail' => trim($_POST['usersEmail']),
                'usersPwd' => trim($_POST['usersPwd']),
                'pwdRepeat' => trim($_POST['pwdRepeat'])
            ];

//            $usersimage = $_POST['usersImage'];

//            //This is the directory where images will be saved
//            $target = "upload/";
//            $target = $target . basename( $_FILES['usersImage']['name']);
//            //This gets all the other information from the form
//            $image = $_FILES['usersImage']['name'];
//            $image_size = $_FILES['usersImage']['size'];
//            $image_tmp_name = $_FILES['usersImage']['tmp_name'];
//            $image_folder = 'uploaded_img/'.$image;

//            $filename = $_FILES["usersimage"]["name"];
//            $tempname = $_FILES["usersimage"]["tmp_name"];
//            $folder = "./images/" . $filename;
//            $filesize = $_FILES["usersimage"]["size"];

            //Validate inputs

            if(empty($data['usersFName']) || empty($data['usersLName']) || empty($data['usersGender']) || empty($data['usersDob']) ||
                empty($data['usersMobile']) ||empty($data['usersEmail']) || empty($data['usersPwd']) || empty($data['pwdRepeat']))
//            enter validation for usersImage
            {
                flash("register", "Please fill out all inputs");
                redirect("../signup.php");
            }
            if(!preg_match("/^[6-9][0-9]{9}$/", $data['usersMobile'])){
                flash("register", "Invalid Mobile Number");
                redirect("../signup.php");
            }
//            if(!preg_match("/\bfemale\b/i , /\bmale\b/i", $data['usersGender'])){
//                flash("register", "Invalid Gender, please type: female or male");
//                redirect("../signup.php");
//            }
//            if(!preg_match("/\bmale\b/i", $data['usersGender'])){
//                flash("register", "Invalid Gender, please type: female or male");
//                redirect("../signup.php");
//            }
//            if($image_size>2000000){
//                flash("register","Image Size Too large");
//                redirect("../signup.php");
//            }
//            if (isset($_POST['submit'])){
//                $filename = $_FILES["usersImage"]["name"];
//                $tempname = $_FILES["usersImage"]["tmp_name"];
//                $folder = "./images/" . $filename;
//
//            }



                if(!filter_var($data['usersEmail'], FILTER_VALIDATE_EMAIL)){
                flash("register", "Invalid email");
                redirect("../signup.php");
            }

            if(strlen($data['usersPwd']) < 6){
                flash("register", "Password less than 6 characters");
                redirect("../signup.php");
            } else if($data['usersPwd'] !== $data['pwdRepeat']){
                flash("register", "Passwords don't match");
                redirect("../signup.php");
            }

            //User with the same email or mobile number already exists
            if($this->userModel->findUserByEmailOrMobile($data['usersEmail'], $data['usersMobile'])){
                flash("register", "Email Address or Mobile Number already taken");
                redirect("../signup.php");
            }
//            if (isset($_POST['submit'])) {
//
//                if($filesize>2000000){
//                    flash("register","Image size to large");
//                    redirect("../signup.php");
//                }
//
//                if (move_uploaded_file($tempname, $folder)) {
//                    flash("register","Image Uploaded Succesfully");
//                    redirect("../signup.php");
//                } else {
//                    flash("register","Image Upload Failed");
//                    redirect("../signup.php");
//                }
//
//            }

            //Passed all validation checks.
            //Now going to hash password
            $data['usersPwd'] = password_hash($data['usersPwd'], PASSWORD_DEFAULT);

            //Register User
            if($this->userModel->register($data)){
                redirect("../login.php");
            }else{
                die("Something went wrong");
            }
        }

    public function login(){
        //Sanitize POST data
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
//        need to sanitize the mobile number

        //Init data
        $data=[
            'mobile/email' => trim($_POST['mobile/email']),
            'usersPwd' => trim($_POST['usersPwd'])
        ];

        if(empty($data['mobile/email']) || empty($data['usersPwd'])){
            flash("login", "Please fill out all inputs");
            header("location: ../login.php");
            exit();
        }

        //Check for mobile/email
        if($this->userModel->findUserByEmailOrMobile($data['mobile/email'], $data['mobile/email'])){
            //User Found
            $loggedInUser = $this->userModel->login($data['mobile/email'], $data['usersPwd']);
            if($loggedInUser){
                //Create session
                $this->createUserSession($loggedInUser);
            }else{
                flash("login", "Password Incorrect");
                redirect("../login.php");
            }
        }else{
            flash("login", "No user found");
            redirect("../login.php");
        }
    }

    public function createUserSession($user){
        $_SESSION['usersId'] = $user->usersId;
        $_SESSION['usersFName'] = $user->usersFName;
//        $_SESSION['usersLName'] = $user->usersLName;
        $_SESSION['usersEmail'] = $user->usersEmail;
        redirect("../index.php");
    }

    public function logout(){
        unset($_SESSION['usersId']);
        unset($_SESSION['usersFName']);
//        unset($_SESSION['usersLName']);
        unset($_SESSION['usersEmail']);
        session_destroy();
        redirect("../index.php");
    }
}

    $init = new Users;

    //Ensure that user is sending a post request
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        switch($_POST['type']){
            case 'register':
                $init->register();
                break;
            case 'login':
                $init->login();
                break;
            default:
            redirect("../index.php");
        }
        
    }else{
        switch($_GET['q']){
            case 'logout':
                $init->logout();
                break;
            default:
            redirect("../index.php");
        }
    }

    