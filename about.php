<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="description" content="Web application development - Assignment 2" />
    <meta name="keywords" content="HTML, CSS, PHP" />
    <meta name="author" content="Your Name" />
    <link rel="stylesheet" href="style.css" type="text/css">
    <title>About - My Friend System</title>
</head>

<body>
    
<?php
// Start session
session_start();

// Check if the user clicks the friendlist button
if (isset($_GET['action']) && $_GET['action'] == 'friendlist') {
    // Check if the user has logged in
    if (isset($_SESSION['loggedin'])) {
        // If user has not logged in yet, take the user to the login page and once the user logs in, go to friendlist.php
        if ($_SESSION['loggedin'] == false) {
            $_SESSION['gotofriendlist'] = true;
            header("location:login.php");
        }
        // If the user has already logged in, take the user to friendlist.php
        else {
            header("location:friendlist.php");
        }
    }
}

// Check if the user clicks the friendadd button
if (isset($_GET['action']) && $_GET['action'] == 'friendadd') {
    // Check if the user has logged in
    if (isset($_SESSION['loggedin'])) {
        // If user has not logged in yet, take the user to the login page and once the user logs in, go to friendadd.php
        if ($_SESSION['loggedin'] == false) {
            $_SESSION['gotofriendadd'] = true;
            header("location: login.php");
        }
        // If the user has already logged in, take the user to friendadd.php
        else {
            header("location: friendadd.php");
        }
    }
}
?>

    <main id="about-main">
        <h1>About My Friend System</h1>

        <h2>Report</h2>
        <ol>
            <li><strong>What tasks have you not attempted or not completed?</strong>
                <ul>
                    <li>I was able to attempt and complete all tasks including the extra challenge.</li>
                </ul>
            </li>

            <li><strong>What special features have you done, or attempted, in creating the site that we should know
                    about?</strong>
                <ul>
                    <li>I have used CSS styling in a way that it automatically resizes elements for different screen
                        orientations which enhances responsiveness.</li>
                </ul>
            </li>

            <li><strong>Which parts did you have trouble with?</strong>
                <ul>
                    <li>One particular challenge was managing the session variables between the pages.</li>
                    <li>Another challenge was trying to get the SQL queries right because they got complicated
                        sometimes.</li>
                </ul>
            </li>

            <li><strong>What would you like to do better next time?</strong>
                <ul>
                    <li>Maybe fill the screen with more elements to improve the UI.</li>
                </ul>
            </li>

            <li><strong>What additional features did you add to the assignment?</strong>
                <ul>
                    <li>In order to view user's friend list or to add friends, the user has to be logged in. So if they
                        aren't logged in, they
                        are first taken to login.php and then to the respective page. This functionality can be tested
                        from this page itself using
                        the Friend list and Add Friends buttons at the bottom of the screen.
                    </li>
                    <li>The implementation of responsive CSS features.</li>
                </ul>
            </li>
        </ol>


        <h2>Discussion Board Contribution</h2>
        <p>Below is a screenshot of a discussion response that I contributed to the unit’s discussion board for
            Assignment 2:</p>
        <img src="style/discussion1.png" alt="Screenshot of a discussion board response"
            style="max-width:100%; height:auto;">

        <p>*Please ensure you are logged in order to add friends or view your list of friends.</p>
        <a href="about.php?action=friendlist">Friend List</a>
        <a href="about.php?action=friendadd">Add Friends</a>
        <a href="index.php">Home Page</a>

    </main>
</body>

</html>