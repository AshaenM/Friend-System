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
$_SESSION['loggedin'] = false;
?>

    <main>
        <h1>My Friend System</h1>
        <h2>Assignment Home Page</h2>

        <table id="my-info-table">
            <tr>
                <td>Name:</td>
                <td>Ashaen Manuel</td>
            </tr>
            <tr>
                <td>Student ID:</td>
                <td>104313773</td>
            </tr>
            <tr>
                <td>Email:</td>
                <td><a href="mailto:104313773@student.swin.edu.au">104313773@student.swin.edu.au</a></td>
            </tr>
        </table>
        <div id="acknowledgement">
            I declare that this assignment is my individual work. I have not worked collaboratively nor have I copied
            from any other student's
            work or from any other source.
        </div>
        <a href="signup.php">Sign-Up</a>
        <a href="login.php">Log-In</a>
        <a href="about.php">About</a>
    </main>
    <?php
    // Get database login details
    require_once("settings.php");

    // Create a connection to the database
    $conn = new mysqli($host, $user, $pswd, $dbnm);

    // Query to create a table named friends if it doesnt exist already
    $createFriendsTablesql = "
    CREATE TABLE IF NOT EXISTS friends (
        friend_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        friend_email VARCHAR(50) NOT NULL,
        password VARCHAR(20) NOT NULL,
        profile_name VARCHAR(30) NOT NULL,
        date_started DATE NOT NULL,
        num_of_friends INT UNSIGNED
    )";

    // Query to create a table named myfriends if it doesnt exist already
    $createMyFriendsTablesql = "
    CREATE TABLE IF NOT EXISTS myfriends (
        friend_id1 INT NOT NULL,
        friend_id2 INT NOT NULL
    )";

    $conn->query($createFriendsTablesql);
    $conn->query($createMyFriendsTablesql);

    $checkData = "SELECT COUNT(*) AS count FROM friends";
    $resultData= $conn->query($checkData);
    $rowData = $resultData->fetch_assoc();

    // If the friends table doesnt have any data (it was just created)
    if ($rowData['count'] == 0){

        // Query to insert 10 rows of sample data to the friends tables
        $insertDataToTable1sql = "
        INSERT INTO friends (friend_email, password, profile_name, date_started, num_of_friends)
        VALUES ('ashaenmanuel41@gmail.com', 'ashaen2003', 'Ashaen', '2024-10-02', 2), ('joeforn123@gmail.com', 'joef', 'Joe', '2024-09-12', 1),
        ('harrymaguire@gmail.com', 'harrymaguire', 'Harry', '2024-10-01', 2), ('mankiratsinghe@gmail.com', 'Manksin123', 'Mankirat', '2024-10-02', 3),
        ('pepeluis@gmail.com', 'pass#wordPepe', 'Pepe', '2023-01-24', 0), ('fazegurk@gmail.com', 'fazeg', 'Faze', '2024-12-12', 2),
        ('timmyt@gmail.com', 'timm', 'Tim', '2024-11-06', 1), ('bobjohnson@gmail.com', 'bobjohnson123', 'Bob', '2024-10-02', 3),
        ('ethanwick@gmail.com', 'ethanw', 'Ethan', '2024-03-29', 3), ('emilyblake@gmail.com', 'emblake123', 'Emily', '2024-02-10', 3)
        ";
    
        // Query to insert 20 rows of sample data to the myfriends tables
        $insertDataToTable2sql = "
        INSERT INTO myfriends (friend_id1, friend_id2) VALUES
        (1, 2), (1, 3), (2, 4), (3, 5), (3, 6), (4, 7),
        (4, 8), (4, 9), (6, 10), (6, 1), (7, 2), (8, 3),
        (8, 4), (8, 5), (9, 6), (9, 7), (9, 10), (10, 1), (10, 2), (10, 3)
        ";
    
        // If running both queries was successful, display a message saying this was successful
        if ($conn->query($insertDataToTable1sql) === TRUE && $conn->query($insertDataToTable2sql) === TRUE) {
            echo "<p id='success-tables-messages'>Tables successfully created and populated.</p>";
        } else {
            echo "<p>Error inserting data: " . $conn->error . "</p>";
        }
    }

    $conn->close();

    ?>
</body>

</html>