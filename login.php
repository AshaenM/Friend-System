<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="description" content="Web application development - Assignment 1" />
    <meta name="keywords" content="Html,CSS,PHP,SQL" />
    <meta name="author" content="Ashaen Manuel" />
    <link rel="stylesheet" href="style.css" type="text/css">
    <title>My Friend System</title>
</head>

<body>

<?php
// Start session
session_start();
?>

    <main>
        <h1>My Friend System</h1>
        <h2>Log in Page</h2>

        <?php
            // If there is a session variable for errors, display them
            if (isset($_SESSION["errors"])) {
                echo "<h3 class='errors-heading'>The following errors were found</h3>";
                echo "<ul class='php-messages'>";
                foreach ($_SESSION["errors"] as $error) {
                    echo "<li>$error</li>";
                }
                echo "</ul>";
                unset($_SESSION["errors"]);
            }
        ?>

        <form action="login.php" method="post">
            <table id="login-table">
                <tr>
                    <td>Email:</td>
                    <td><input type="text" name="email" value="<?php //Save the state of this input
                        echo isset($_SESSION['user_email']) ? $_SESSION['user_email'] : ''; ?>">
                    </td>
                </tr>
                <tr>
                    <td>Password:</td>
                    <td><input type="password" name="password"></td>
                </tr>
            </table>
            <input type="submit" value="Log in" class="submit-buttons">
            <input type="submit" value="Clear" name="clear" class="submit-buttons">
        </form>
        
        <hr>
        <a href="login.php?action=home">Home</a>
    </main>
    <?php

    // Get database login details   
    require_once("settings.php");

    // Create a connection to the database
    $conn = new mysqli($host, $user, $pswd, $dbnm);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $errors = [];

    // If user clicks the clear button, destroy the session and refresh
    if (isset($_POST['clear'])) {
        session_unset();
        session_destroy();

        header("Location: login.php");
        exit();
    }

    // If user clicks the home button, destory the session and redirect to index.php
    if (isset($_GET['action']) && $_GET['action'] == 'home') {
        session_destroy();
        header("Location: index.php");
        exit;
    }

    // Function to validate email input of the user
    function validateEmail($emailInput, $conn) {
        if (!empty($emailInput)) {
            if (filter_var($emailInput, FILTER_VALIDATE_EMAIL)) {
                $getEmailssql = "SELECT friend_email FROM friends WHERE friend_email = '$emailInput'";
                $result = $conn->query($getEmailssql);
            
                if ($result->num_rows > 0) {
                    return true;
                }
                else{
                    return "No user found with that email address, please try again.";
                }
            }
            else {
                return "Email address is in an invalid format, please enter a valid email.";
            }
        }
        else{
            return "Please enter your email address.";
        }
    }

    // Function to validate password input of the user
    function validatePassword($emailInput, $passwordInput, $conn) {
        $getPasswordSql = "SELECT password FROM friends WHERE friend_email = '$emailInput'"; 
        $result = $conn->query($getPasswordSql);
    
        if (!empty($passwordInput)){
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
        
                if ($passwordInput == $row['password']) {
                    return true;
                }
                else {
                    return "Incorrect password. Please try again.";
                }
            }
            return false;
        }
        else{
            return "Please enter your password.";
        }
    }

    // Function to get the profile name of the user
    function getProfileName($emailInput, $conn){
        $getProfileSql = "SELECT profile_name FROM friends WHERE friend_email = '$emailInput'"; 
        $result = $conn->query($getProfileSql);

        if ($result->num_rows > 0){
            $row = $result->fetch_assoc();
            return $row['profile_name'];
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //Remove whitespaces
        $email = trim($_POST["email"]);
        $password = trim($_POST["password"]);

        // If the user has input an email address, validate it
        if (!empty($email)){
            $emailValidation = validateEmail($email, $conn);
            if ($emailValidation !== true and !empty($emailValidation)) {
                $errors[] = $emailValidation;
            }
        }
        else{
            $errors[] = "Please enter your email address.";
        }

        // If the user has input the password, validate it
        if (!empty($password)){
            $passwordValidation = validatePassword($email, $password, $conn);
            if ($passwordValidation !== true and !empty($passwordValidation)) {
                $errors[] = $passwordValidation;
            }
        }
        else{
            $errors[] = "Please enter your password.";
        }

        // If there are errors, create session variables to store them and redirect to current page to display them
        if (!empty($errors)) {
            $_SESSION["errors"] = $errors;
            $_SESSION["user_email"] = $email;
            header("location:login.php");
            exit;
        }
        else{
            // If no errors, create session variables to be passed to the next page
            $_SESSION['loggedin'] = true;
            $_SESSION["profile_name"] = getProfileName($email, $conn);
            $_SESSION["my_email"] = $email;
            // Checks if the user has come from the about page
            if (isset($_SESSION['gotofriendadd'])){
                if ($_SESSION['gotofriendadd']){
                    header("location:friendadd.php");
                    exit;
                }
            }
            else if (isset($_SESSION['gotofriendlist'])){
                if ($_SESSION['gotofriendlist']){
                    header("location:friendlist.php");
                    exit;
                }
            }
            // If the user has come through the index page
            else{
                header("location:friendlist.php");
                exit;
            }           
        }        
    } 

    ?>

</body>

</html>