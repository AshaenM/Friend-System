<?php
session_start();

$name = $_SESSION["profile_name"];
$email = $_SESSION["my_email"];

require_once("settings.php");
$conn = new mysqli($host, $user, $pswd, $dbnm, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['action']) && $_GET['action'] == 'friendadd') {
    header("Location: friendadd.php");
    exit;
}
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    header("Location: logout.php");
    exit;
}

function getFriendsCount($emailInput, $conn) {
    $getFriendsCountSql = "SELECT num_of_friends FROM friends WHERE friend_email = '$emailInput'";
    $result = $conn->query($getFriendsCountSql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['num_of_friends'];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['unfriend'])) {
    $friendToRemove = $_POST['unfriend'];

    $removeFriendSql = "
    DELETE mf FROM myfriends mf
    JOIN friends f1 ON mf.friend_id1 = f1.friend_id
    JOIN friends f2 ON mf.friend_id2 = f2.friend_id
    WHERE f1.profile_name = '$name' AND f2.profile_name = '$friendToRemove'
    ";
    $updateUserSql = "UPDATE friends SET num_of_friends = num_of_friends - 1 WHERE profile_name = '$name'";

    if ($conn->query($removeFriendSql) === TRUE) {
        $conn->query($updateUserSql);
        $_SESSION["friends_count"] = getFriendsCount($email, $conn);
        header("Location: friendlist.php");
        exit();
    }
}

$_SESSION["friends_count"] = getFriendsCount($email, $conn);
$friendsCount = $_SESSION["friends_count"];

$getFriendsSql = "
SELECT f2.profile_name AS friend_name FROM friends f1 JOIN
myfriends mf ON f1.friend_id = mf.friend_id1 JOIN friends
f2 ON mf.friend_id2 = f2.friend_id WHERE f1.profile_name = '$name' ORDER BY friend_name
";
$result = $conn->query($getFriendsSql);
?>
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
    <main id="friendlist-main">
        <h1>My Friend System</h1>
        <h2><?php echo "$name"; ?>'s Friend List Page</h2>
        <h2>Total number of friends is <?php echo "$friendsCount"; ?></h2>

        <form action="friendlist.php" method="post">
            <table id="friendlist-table">
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["friend_name"] . "</td>";
                        echo "<td><button type='submit' name='unfriend' value='" . $row["friend_name"] . "' class='submit-buttons'>Unfriend</button></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='2'>You have no friends.</td></tr>";
                }
                ?>
            </table>
        </form>

        <hr>
        <a href="friendlist.php?action=friendadd">Add Friends</a>
        <a href="friendlist.php?action=logout">Log Out</a>
    </main>
</body>
</html>