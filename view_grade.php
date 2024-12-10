<?php
include 'config.php';
session_start();

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch the full name of the student
$stmt = $conn->prepare("SELECT CONCAT(first_name, ' ', last_name) AS full_name FROM students WHERE student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$stmt->bind_result($fullName);
$stmt->fetch();
$stmt->close();

// Fetch student's schedule along with grades
$stmt = $conn->prepare("
    SELECT 
        sub.subject_name,
        rm.room_name,
        s.semester,
        s.day,
        s.start_time,
        s.end_time,
        sg.grade,
        sg.status
    FROM 
        schedules s
    LEFT JOIN subjects sub ON s.subject_id = sub.subject_id
    LEFT JOIN rooms rm ON s.room_id = rm.room_id
    LEFT JOIN student_grades sg ON sg.student_id = s.student_id AND sg.subject_id = s.subject_id
    WHERE 
        s.student_id = ?
    ORDER BY 
        s.day, s.start_time
");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($subject_name, $room_name, $semester, $day, $start_time, $end_time, $grade, $status);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Overview</title>
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

        .card {
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }

        .table-container {
            overflow-x: auto;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 16px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        th {
            background: rgba(255, 255, 255, 0.1);
            font-weight: 500;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
        }

        tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            background: rgba(155, 77, 255, 0.2);
            border: 1px solid rgba(155, 77, 255, 0.3);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 24px;
            background: linear-gradient(to bottom, #000000 0%, #800000 40%, #ff0000 100%);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 500;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            width: auto;
        }

        .btn:hover {
            background: linear-gradient(to bottom, #1a0000 0%, #990000 40%, #ff1a1a 100%);
            transform: translateY(-1px);
        }

        .welcome-text {
            font-size: 24px;
            margin-bottom: 30px;
            color: white;
        }

        .welcome-text span {
            color: #9b4dff;
            font-weight: 600;
        }

        /* Adjust button container padding */
        .button-container {
            display: flex;
            justify-content: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <main class="main-content">
            <div class="card">
                <h2 class="welcome-text">Welcome, <span><?php echo htmlspecialchars($fullName); ?></span></h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Room</th>
                                <th>Semester</th>
                                <th>Day</th>
                                <th>Time</th>
                                <th>Grade</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($stmt->fetch()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($subject_name); ?></td>
                                    <td><?php echo htmlspecialchars($room_name); ?></td>
                                    <td><?php echo htmlspecialchars($semester); ?></td>
                                    <td><?php echo htmlspecialchars($day); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($start_time); ?> - 
                                        <?php echo htmlspecialchars($end_time); ?>
                                    </td>
                                    <td>
                                        <span class="status-badge">
                                            <?php 
                                            if ($grade !== null) {
                                                echo htmlspecialchars($grade);
                                            } else {
                                                echo "Pending";
                                            }
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge">
                                            <?php 
                                            if ($status !== null) {
                                                echo htmlspecialchars($status);
                                            } else {
                                                echo "N/A";
                                            }
                                            ?>
                                        </span>
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
            <a href="student_dashboard.php" class="btn btn-primary">
                Back to dashboard
            </a>
        </div>
    </div>
</body>
</html>