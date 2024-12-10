<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login_f.html");
    exit();
}

echo "<h1>Welcome to the Student Directory</h1>";
echo "<p>User Role: " . $_SESSION['role'] . "</p>";


if ($_SESSION['role'] == 'admin') {
    echo "<a href='manage_students.php'>Manage Students</a><br>";
    echo "<a href='manage_subjects.php'>Manage Subjects</a><br>";
    echo "<a href='manage_rooms.php'>Manage Rooms</a><br>";
    echo "<a href='manage_schedules.php'>Manage Schedules</a><br>"; 
} else {
    echo "<a href='user_profile.php'>View Profile</a><br>";
    echo "<a href='view_schedule.php'>View Schedule</a><br>";
}

echo "<a href='logout.php'>Logout</a>";
?>