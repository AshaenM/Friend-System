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
//Start the session
session_start();
?>

    <main>
        <h1>My Friend System</h1>
        <h2>Registration Page</h2>

        <?php
            // If there are errors, display them
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

        <form action="signup.php" method="post">
            <table id="registration-table">
                <tr>
                    <td>Email:</td>
                    <td><input type="text" name="email" value="<?php //Save the state 
                        echo isset($_SESSION['my_email']) ? $_SESSION['my_email'] : ''; ?>">
                    </td>
                </tr>
                <tr>
                    <td>Profile Name:</td>
                    <td><input type="text" name="name" value="<?php //Save the state
                        echo isset($_SESSION['profile_name']) ? $_SESSION['profile_name'] : ''; ?>">
                    </td>
                </tr>
                <tr>
                    <td>Password:</td>
                    <td><input type="password" name="password1"></td>
                </tr>
                <tr>
                    <td>Confirm Password:</td>
                    <td><input type="password" name="password2"></td>
                </tr>
            </table>
            <input type="submit" value="Register" class="submit-buttons">
            <input type="submit" value="Clear" name="clear" class="submit-buttons">
        </form>
        
        <hr>
        <a href="signup.php?action=home">Home</a>
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

        header("Location: signup.php");
        exit();
    }

    // If user clicks the home button, destory the session and redirect to index.php
    if (isset($_GET['action']) && $_GET['action'] == 'home') {
        session_destroy();
        header("Location: index.php");
        exit;
    }

    // Function to validate email input of the user
    function validateEmail($emailInput, $conn){
        if (filter_var($emailInput, FILTER_VALIDATE_EMAIL)) {
            $getEmailssql = "SELECT friend_email FROM friends WHERE friend_email = '$emailInput'";
            $result = $conn->query($getEmailssql);

            if ($result->num_rows > 0) {
                return "Email address already exists, please try another email.";
            }
            return true;
        } else {
            return "Email address is in an invalid format, please enter a valid email.";
        }
    }

    // Function to validate name input of the user
    function validateProfile($nameInput){
        if (empty($nameInput)) {
            return "Profile name cannot be empty.";
        }
        if (!ctype_alpha($nameInput)) {
            return "Profile name can only include letters.";
        }
        return true;
    }

    // Function to validate password inputs of the user
    function validatePasswords($passwordInput1, $passwordInput2){
        if (empty($passwordInput1)){
            return "Please enter a password";
        }
        if (empty($passwordInput2)){
            return "Please confirm your password";
        }
        else if (!ctype_alnum($passwordInput1) || !ctype_alnum($passwordInput2)){
            return "Password can only contain letters and numbers.";
        }
        else if ($passwordInput1 != $passwordInput2){
            return "Passwords must match! Please try again.";
        }
        return true;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Remove whitespaces
        $email = trim($_POST["email"]);
        $name = trim($_POST["name"]);
        $password1 = trim($_POST["password1"]);
        $password2 = trim($_POST["password2"]);

        // Validate email, if there are errors, add it to an array to displayed later
        $emailValidation = validateEmail($email, $conn);
        if ($emailValidation !== true) {
            $errors[] = $emailValidation;
        }

        // Validate profile, if there are errors, add it to an array to displayed later
        $profileValidation = validateProfile($name);
        if ($profileValidation !== true) {
            $errors[] = $profileValidation;
        }

        // Validate password, if there are errors, add it to an array to displayed later
        $passwordValidation = validatePasswords($password1, $password2);
        if ($passwordValidation !== true) {
            $errors[] = $passwordValidation;
        }

        // if there are errors, assign it to a session variable along with other variables and redirect to the current page to display them
        if (!empty($errors)) {
            $_SESSION["errors"] = $errors;
            $_SESSION["my_email"] = $email;
            $_SESSION["profile_name"] = $name;
            header("location:signup.php");
            exit;
        }

        // Remove special characters
        $email = $conn->real_escape_string($email);
        $name = $conn->real_escape_string($name);
        $password = $conn->real_escape_string($password1);
        
        // Query to enter new user details into friends table as a record
        $insertDataToTable1sql = "
        INSERT INTO friends (friend_email, password, profile_name, date_started, num_of_friends)
        VALUES ('$email', '$password', '$name', CURDATE(), 0)
        ";
        
        // If running the above query was successful, create session variables for the next page and redirect to friendadd.php
        if ($conn->query($insertDataToTable1sql) === TRUE) {
            $_SESSION["my_email"] = $email;
            $_SESSION["profile_name"] = $name;
            header("location:friendadd.php");
            exit;
        } else {
            echo "Error: " . $conn->error;
        }
    }

    $conn->close();
    ?>

</body>

</html>