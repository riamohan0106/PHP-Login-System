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
            'usersImage' => ($_POST['usersImage']),
            'usersEmail' => trim($_POST['usersEmail']),
            'usersPwd' => trim($_POST['usersPwd']),
            'pwdRepeat' => trim($_POST['pwdRepeat'])
        ];
//        $image = $data->usersImage;
//        $img_name = $_FILES[$image['usersImage']]['name'];
//        $_FILES['usersImage']['name'] = $data->usersImage;
//
//
//        $image = $_POST['usersImage'];
//        $image_name = $_FILES[$data['usersImage']]['name'];
//        $image_size = $_FILES[$data['usersImage']]['size'];
//        $image_tmp_name = $_FILES[$data['usersImage']]['tmp_name'];
//        $image_folder = 'images/'.$image;









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
            empty($data['usersMobile']) || empty($data['usersImage']) || empty($data['usersEmail']) || empty($data['usersPwd']) || empty($data['pwdRepeat']))
//            enter validation for usersImage
        {
            flash("register", "Please fill out all inputs");
            redirect("../signup.php");
        }
        if(!preg_match("/^[6-9][0-9]{9}$/", $data['usersMobile'])){
            flash("register", "Invalid Mobile Number");
            redirect("../signup.php");
        }

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
        if($image_size>2000000){
            flash("register","Image size is too big!");
            redirect("../signup.php");
        }

        //User with the same email or mobile number already exists
        if($this->userModel->findUserByEmailOrMobile($data['usersEmail'], $data['usersMobile'])){
            flash("register", "Email Address or Mobile Number already taken");
            redirect("../signup.php");
        }

        //Passed all validation checks.
        //Now going to hash password
//        if (isset($_FILES[$data['usersImage']]['name']) AND !empty($_FILES[$data['usersImage']]['name'])){
//            $img_name = $_FILES[$data['usersImage']]['name'];
//            $tmp_name = $_FILES[$data['usersImage']]['tmp_name'];
//            $img_size = $_FILES[$data['usersImage']]['size'];
//            $error = $_FILES[$data['usersImage']]['error'];
//            if($error === 0 AND $img_size<2000000){
//                $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
//                $img_ex_to_lc = strtolower($img_ex);
//                $allowed_exs = array('jpg', 'jpeg', 'png');
//                if(in_array($img_ex_to_lc, $allowed_exs)){
//
//                    $new_img_name = $img_name.'.'.$img_ex_to_lc;
//                    $img_upload_path = '../upload/'.$new_img_name;
//                    move_uploaded_file($tmp_name, $img_upload_path);
//
//
//                }
//
//
//            }
//
//
//        }
        $data['usersPwd'] = password_hash($data['usersPwd'], PASSWORD_DEFAULT);

        //Register User
        if($this->userModel->register($data)){
            move_uploaded_file($image_tmp_name, $image_folder);
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

    
