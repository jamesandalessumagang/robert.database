<?php

@include 'config.php';
session_start();

if (isset($_POST['add_room'])) {
    $roomName = $_POST['room_name'];

    $stmt = $conn->prepare("INSERT INTO rooms (room_name) VALUES (?)");
    $stmt->bind_param("s", $roomName);
    $stmt->execute();
    $stmt->close();
}

if (isset($_GET['delete'])) {
    $roomId = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM rooms WHERE room_id = ?");
    $stmt->bind_param("i", $roomId);
    $stmt->execute();
    $stmt->close();

    // After deletion, reset the AUTO_INCREMENT value
    $conn->query("ALTER TABLE rooms AUTO_INCREMENT = 1");
}

$result = $conn->query("SELECT * FROM rooms");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(to bottom, #000000 0%, #800000 40%, #ff0000 100%);
            padding: 20px;
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .main-content {
            display: flex;
            flex-direction: column;
            gap: 20px;
            width: 100%;
        }

        .button-container {
            display: flex;
            justify-content: center;
            margin-bottom: 40px;
            width: 100%;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            background: linear-gradient(to bottom, #000000 0%, #220000 20%, #330000 40%, #440000 60%, #550000 80%, #660000 100%);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.1);
            min-width: 120px;
            text-align: center;
        }

        .btn:hover {
            background: linear-gradient(to bottom, #000000 0%, #330000 20%, #440000 40%, #550000 60%, #660000 80%, #770000 100%);
            border-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
        }

        .btn-primary {
            width: 100%;
        }

        .btn-danger {
            /* Remove specific background */
        }

        .card {
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: white;
            font-size: 14px;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
        }

        th {
            padding: 16px;
            text-align: left;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.7);
        }

        td {
            padding: 16px;
            background: rgba(0, 0, 0, 0.3);
        }

        tr td:first-child {
            border-radius: 12px 0 0 12px;
        }

        tr td:last-child {
            border-radius: 0 12px 12px 0;
        }

        tr:hover td {
            background: rgba(128, 0, 0, 0.3);
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: flex-start;
            align-items: center;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="card">
            <h2 style="margin-bottom: 24px">Add New Room</h2>
            <form action="manage_rooms.php" method="POST">
                <input type="text" name="room_name" placeholder="Room Name" class="form-control" required>
                <button type="submit" name="add_room" class="btn btn-primary">Add now</button>
            </form>
        </div>

        <div class="card">
            <h2 style="margin-bottom: 24px">Rooms</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Room ID</th>
                            <th>Room Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['room_id'] ?></td>
                                <td><?= $row['room_name'] ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="manage_rooms.php?delete=<?= $row['room_id'] ?>" 
                                           class="btn btn-danger" 
                                           onclick="return confirm('Are you sure you want to delete this room?')">
                                            Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <br>
        <div class="button-container">
            <a href="admin_dashboard.php" class="btn btn-primary">
                Back to dashboard
            </a>
        </div>
    </div>
</body>
</html>
