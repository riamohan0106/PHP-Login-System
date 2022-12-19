<?php
include_once 'header.php';
include_once './helpers/session_helper.php';
?>



    <h1 class="header">Please Signup</h1>

<?php flash('register') ?>


    <form method="post" enctype="multipart/form-data" action="./controllers/Users.php">

        <input type="hidden" name="type" value="register">
        <input type="text" name="usersFName"
               placeholder="First name">
        <input type="text" name="usersLName"
               placeholder="Last name">
        <!--        <input type="text" name="usersGender"-->
        <!--               placeholder="Enter Gender: male or female">-->
        <!--        <label for="Date Of Birth">Date Of Birth</label>-->

        <input type="date" name="usersDob" value="2022-12-12" min="1000-01-01" max="9999-12-31">
        <!--        <input type="tel" pattern="[0-9]{4}-[0-9]{6}" name="usersMobile"-->
        <!--               placeholder="Mobile Number">-->
        <input type="tel" pattern="[0-9]*" name="usersMobile"
               placeholder="Mobile Number">
        <!--        <label for="profile pic">Upload Profile Picture</label>-->
        <!--        <input type="file" id="profile pic" name="usersImage">-->
        <input type="file" name="usersImage">
        <input type="email" name="usersEmail"
               placeholder="Email">

        <input type="password" name="usersPwd"
               placeholder="Password">
        <input type="password" name="pwdRepeat"
               placeholder="Confirm password">
        <tr>Select Your Gender: </tr>
        Male<input type="radio" name="usersGender" value="male">
        Female<input type="radio" name="usersGender" value="female">
        <button type="submit" name="submit">Sign Up</button>

    </form>


<?php
include_once 'footer.php'
?>
