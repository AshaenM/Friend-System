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

// Retrieve session variables from previous page
$name = $_SESSION["profile_name"];
$email = $_SESSION["my_email"];

// Get database login details
require_once("settings.php");

// Create a connection to the database
$conn = new mysqli($host, $user, $pswd, $dbnm);

// Get total number of friends count for pagination
$getTotalFriendsSql = "
SELECT COUNT(f.friend_id) AS total_friends 
FROM friends f 
WHERE f.profile_name != '$name' 
AND f.friend_id NOT IN (
    SELECT mf.friend_id2 
    FROM myfriends mf 
    JOIN friends f1 ON mf.friend_id1 = f1.friend_id 
    WHERE f1.profile_name = '$name'
)";

// Run query and get the total number of friends
$totalResult = $conn->query($getTotalFriendsSql);
$totalFriends = $totalResult->fetch_assoc()['total_friends'];

// Set number of friends to display per page
$friendsPerPage = 10;

// Calculate and round up the total number of pages
$totalPages = ceil($totalFriends / $friendsPerPage);

// Get the current page number from the URL (1 if not set)
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

// Calculate the offset for the SQL query
$offset = ($page - 1) * $friendsPerPage;

// SQL query to fetch the friends for the current page along with the count of mutual friends
$getAllFriendsExceptMeSql = "
SELECT f.profile_name, 
    (
        SELECT COUNT(*) 
        FROM myfriends mf1
        JOIN myfriends mf2 ON mf1.friend_id2 = mf2.friend_id2
        WHERE mf1.friend_id1 = (
            SELECT friend_id FROM friends WHERE profile_name = '$name'
        )
        AND mf2.friend_id1 = f.friend_id
    ) AS mutual_friends_count
FROM friends f
WHERE f.profile_name != '$name'
AND f.friend_id NOT IN (
    SELECT mf.friend_id2
    FROM myfriends mf
    JOIN friends f1 ON mf.friend_id1 = f1.friend_id
    WHERE f1.profile_name = '$name'
)
ORDER BY f.profile_name
LIMIT $friendsPerPage OFFSET $offset
";


$result = $conn->query($getAllFriendsExceptMeSql);

// Function to get the friend count for the logged-in user
function getFriendsCount($emailInput, $conn)
{
    $getFriendsCountSql = "SELECT num_of_friends FROM friends WHERE friend_email = '$emailInput'";
    $result = $conn->query($getFriendsCountSql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['num_of_friends'];
    }
}

$_SESSION["friends_count"] = getFriendsCount($email, $conn);
$friendsCount = $_SESSION["friends_count"];

// If user clicks the Add Friend button
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['addfriend'])) {
    $friendToAdd = $_POST['addfriend'];

    // Get ID of the current user and friend
    $getCurrentUserIdSql = "SELECT friend_id FROM friends WHERE profile_name = '$name'";
    $getFriendIdSQL = "SELECT friend_id FROM friends WHERE profile_name = '$friendToAdd'";

    $result1 = $conn->query($getCurrentUserIdSql);
    $result2 = $conn->query($getFriendIdSQL);

    if ($result1->num_rows > 0 && $result2->num_rows > 0) {
        $row1 = $result1->fetch_assoc();
        $row2 = $result2->fetch_assoc();

        $id1 = $row1['friend_id'];
        $id2 = $row2['friend_id'];

        // Insert the user ID and the friends ID into myfriends table
        $addFriendSql = "
        INSERT INTO myfriends (friend_id1, friend_id2) VALUES ('$id1', '$id2')
        ";

        // Increment and update the count of friends for the user in the friends table by 1
        $updateUserSql = "
        UPDATE friends SET num_of_friends = num_of_friends + 1 
        WHERE profile_name = '$name'
        ";

        // Update both tables with the new friend ID linking and the count
        if ($conn->query($addFriendSql) === TRUE) {
            $conn->query($updateUserSql);
            $_SESSION["friends_count"] = getFriendsCount($email, $conn);
            header("Location: friendadd.php?page=$page");
            exit();
        }
    }
}
?>
    <main id="friendadd-main">

        <h1>My Friend System</h1>
        <h2><?php echo "$name"; ?>'s Add Friend Page</h2>
        <h2>Total number of friends is <?php echo "$friendsCount"; ?></h2>

        <form action="friendadd.php" method="post">
        <table id="friendadd-table">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row["profile_name"]) . "</td>";
                    echo "<td>Mutual Friends: " . htmlspecialchars($row["mutual_friends_count"]) . "</td>";
                    echo "<td><button type='submit' name='addfriend' value='" . htmlspecialchars($row["profile_name"]) . "' class='submit-buttons'>Add as friend</button></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No friends available to add</td></tr>";
            }
            ?>
        </table>
        </form>

        <div id="pagination">
            <?php if ($page > 1): ?>
                <a href="friendadd.php?page=<?php echo $page - 1; ?>">Previous</a>
            <?php endif; ?>

            <?php if ($page < $totalPages): ?>
                <a href="friendadd.php?page=<?php echo $page + 1; ?>">Next</a>
            <?php endif; ?>
        </div>

        <hr>
        <a href="friendadd.php?action=friendlist">Friend Lists</a>
        <a href="friendadd.php?action=logout">Log Out</a>

    </main>
    <?php
    // If user clicks the Friend Lists button, redirect to friendlist.php 
    if (isset($_GET['action']) && $_GET['action'] == 'friendlist') {
        header("Location: friendlist.php");
        exit;
    }
    // If user clicks the logout button, redirect to logout.php 
    if (isset($_GET['action']) && $_GET['action'] == 'logout') {
        header("Location: logout.php");
        exit;
    }
    ?>

</body>

</html>