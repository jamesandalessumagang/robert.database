<?php
@include 'config.php';
session_start();


$isAdmin = isset($_SESSION['admin_name']); // Administrator check
$isStudent = isset($_SESSION['user_name']); // Student check

if (!$isAdmin && !$isStudent) {
    header('location:login_f.php');
    exit;
}

// Add a new room if the form is submitted by the administrator
if (isset($_POST['add_room']) && $isAdmin) {
    $roomName = $_POST['room_name'];

    $stmt = $conn->prepare("INSERT INTO rooms (room_name) VALUES (?)");
    $stmt->bind_param("s", $roomName);
    $stmt->execute();
    $stmt->close();
}

// Delete a room if the delete action is triggered by the administrator
if (isset($_GET['delete']) && $isAdmin) {
    $roomId = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM rooms WHERE room_id = ?");
    $stmt->bind_param("i", $roomId);
    $stmt->execute();
    $stmt->close();

    // After deletion, reset the AUTO_INCREMENT value
    $conn->query("ALTER TABLE rooms AUTO_INCREMENT = 1");
}

// Fetch the list of rooms
$result = $conn->query("SELECT * FROM rooms");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Rooms</title>
    <link rel="stylesheet" href="STYLE/manage_rooms.css">
</head>
<body>
    <div class="container">
        <div class="content">
            <h3>Welcome, <span><?php echo $isAdmin ? 'Administrator' : $_SESSION['user_name']; ?></span></h3>
            <div class="buttons">
                <?php if ($isAdmin): ?>
                <?php endif; ?>
                <a href="user_p.php" class="btn">Back to Dashboard</a>
                <a href="logout.php" class="btn">Logout</a>
            </div>
        </div>
    </div>

    <h2>Manage Rooms</h2>

    <?php if ($isAdmin): ?>
        <!-- Form to add new room (only visible to admins) -->
        <form action="manage_rooms.php" method="POST">
            <input type="text" name="room_name" placeholder="Room Name" required>
            <button type="submit" name="add_room">Add Room</button>
        </form>
    <?php endif; ?>

    <table border="1">
        <tr>
            <th>Room ID</th>
            <th>Room Name</th>
            <?php if ($isAdmin): ?>
                <th>Action</th>
            <?php endif; ?>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['room_id'] ?></td>
                <td><?= $row['room_name'] ?></td>

                <?php if ($isAdmin): ?>
                    <td>
                        <!-- Only administrators can delete a room -->
                        <button><a href="manage_rooms.php?delete=<?= $row['room_id'] ?>">Delete</a></button>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endwhile; ?>
    </table>

    <?php $conn->close(); ?>
</body>
</html>


