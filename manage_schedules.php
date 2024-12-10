<?php
include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_schedule'])) {
    $student_id = $_POST['student_id'];
    $subject_id = $_POST['subject_id'];
    $room_id = $_POST['room_id'];
    $semester = $_POST['semester'];
    $day = $_POST['day'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

   
    $checkStudent = $conn->prepare("SELECT student_id FROM students WHERE student_id = ?");
    $checkStudent->bind_param("i", $student_id);
    $checkStudent->execute();
    $checkStudent->store_result();

    $checkSubject = $conn->prepare("SELECT subject_id FROM subjects WHERE subject_id = ?");
    $checkSubject->bind_param("i", $subject_id);
    $checkSubject->execute();
    $checkSubject->store_result();

    $checkRoom = $conn->prepare("SELECT room_id FROM rooms WHERE room_id = ?");
    $checkRoom->bind_param("i", $room_id);
    $checkRoom->execute();
    $checkRoom->store_result();

    if ($checkStudent->num_rows === 0) {
        echo "Error: Student ID $student_id does not exist.";
    } elseif ($checkSubject->num_rows === 0) {
        echo "Error: Subject ID $subject_id does not exist.";
    } elseif ($checkRoom->num_rows === 0) {
        echo "Error: Room ID $room_id does not exist.";
    } else {
       
        $stmt = $conn->prepare("INSERT INTO schedules (student_id, subject_id, room_id, semester, day, start_time, end_time) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiissss", $student_id, $subject_id, $room_id, $semester, $day, $start_time, $end_time);
        
        if ($stmt->execute()) {
            echo "Schedule added successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }

   
    $checkStudent->close();
    $checkSubject->close();
    $checkRoom->close();
}


if (isset($_GET['delete'])) {
    $scheduleId = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM schedules WHERE schedule_id = ?");
    $stmt->bind_param("i", $scheduleId);
    if ($stmt->execute()) {
        echo "Schedule deleted successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}


$result = $conn->query("SELECT * FROM schedules");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Management</title>
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

        .card {
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            position: relative;
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
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .form-label {
            position: absolute;
            left: 16px;
            top: -10px;
            background: rgba(128, 0, 0, 0.8);
            padding: 0 8px;
            font-size: 12px;
            border-radius: 4px;
            color: white;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: flex-start;
            align-items: center;
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
    </style>
</head>
<body>
    <div class="dashboard-container">

        <main class="main-content">
            <div class="card">
                <h2 style="margin-bottom: 24px">Add Schedule</h2>
                <form action="manage_schedules.php" method="POST">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Student ID</label>
                            <input type="number" name="student_id" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Subject ID</label>
                            <input type="number" name="subject_id" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Room ID</label>
                            <input type="number" name="room_id" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Semester</label>
                            <input type="text" name="semester" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Day</label>
                            <input type="text" name="day" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Start Time</label>
                            <input type="time" name="start_time" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">End Time</label>
                            <input type="time" name="end_time" class="form-control" required>
                        </div>
                    </div>
                    <button type="submit" name="add_schedule" class="btn btn-primary">add schedule</button>
                </form>
            </div>

            <div class="card">
                <h2 style="margin-bottom: 24px">Scheduled</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Schedule ID</th>
                                <th>Student ID</th>
                                <th>Subject ID</th>
                                <th>Room ID</th>
                                <th>Semester</th>
                                <th>Day</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['schedule_id'] ?></td>
                                    <td><?= $row['student_id'] ?></td>
                                    <td><?= $row['subject_id'] ?></td>
                                    <td><?= $row['room_id'] ?></td>
                                    <td><?= $row['semester'] ?></td>
                                    <td><?= $row['day'] ?></td>
                                    <td><?= $row['start_time'] ?></td>
                                    <td><?= $row['end_time'] ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="manage_schedules.php?delete=<?= $row['schedule_id'] ?>" 
                                               class="btn btn-danger"
                                               onclick="return confirm('Are you sure you want to delete this schedule?')">
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
        </main>
        <br>
        <div class="button-container">
            <a href="admin_dashboard.php" class="btn btn-primary">
                Back to dashboard
            </a>
        </div>
    </div>
</body>
</html>