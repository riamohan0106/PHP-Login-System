<?php
require_once '../DB-Config/Database.php';
//include  '../controllers/Users.php';

class User {

    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    //Find user by email or mobile
    public function findUserByEmailOrMobile($email, $usermobile){
        $this->db->query('SELECT * FROM users WHERE usersMobile = :usermobile OR usersEmail = :email');
//        binding the values from the returned query according to our needs.
        $this->db->bind(':usermobile', $usermobile);
        $this->db->bind(':email', $email);
//      The single method will execute the query in a way that ensures there is only a single corresponding value.
//      The func finduserbyemailorusername is used for validating check.
        $row = $this->db->single();

        //Check row
        if($this->db->rowCount() > 0){
            return $row;
        }else{
            return false;
        }
    }

    //Register User
    public function register($data){
        $this->db->query('INSERT INTO users (usersFName, usersLName, usersGender,usersDob,usersMobile,usersImage,usersEmail, usersPwd) 
        VALUES (:firstname, :lastname, :gender, :DOB, :mobile,:image :email, :password)');
        //Bind values
        $this->db->bind(':firstname', $data['usersFName']);
        $this->db->bind(':lastname', $data['usersLName']);
        $this->db->bind(':gender', $data['usersGender']);
        $this->db->bind(':DOB', $data['usersDob']);
        $this->db->bind(':mobile', $data['usersMobile']);
        $this->db->bind(':image', $data['usersImage']);
        $this->db->bind(':email', $data['usersEmail']);
        $this->db->bind(':password', $data['usersPwd']);


        //Execute
        if($this->db->execute()){
            return true;
        }else{
            return false;
        }
    }
//    public function addPicture($data){
//        $this->db->query('INSERT INTO users(usersImage)VALUES ()')
//
//
//
//    }

    //Login user
    public function login($mobileOrEmail, $password){
        $row = $this->findUserByEmailOrMobile($mobileOrEmail, $mobileOrEmail);

        if($row == false) return false;

        $hashedPassword = $row->usersPwd;
        if(password_verify($password, $hashedPassword)){
            return $row;
        }else{
            return false;
        }
    }

    //Reset Password
    public function resetPassword($newPwdHash, $tokenEmail){
        $this->db->query('UPDATE users SET usersPwd=:pwd WHERE usersEmail=:email');
        $this->db->bind(':pwd', $newPwdHash);
        $this->db->bind(':email', $tokenEmail);

        //Execute
        if($this->db->execute()){
            return true;
        }else{
            return false;
        }
    }
}
