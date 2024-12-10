<?php
@include 'config.php';
session_start();

// Check if the student is logged in
if (!isset($_SESSION['student_id']) || $_SESSION['user_type'] !== 'user') {
    header('location:login_f.php');
    exit();
}

// Get the student ID from the session
$student_id = $_SESSION['student_id'];

// Fetch the student's full name
$stmt = $conn->prepare("SELECT first_name, last_name FROM students WHERE student_id = ?");
if (!$stmt) {
    die("Error preparing statement: " . htmlspecialchars($conn->error));
}

$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the student's full name
$fullName = '';
if ($row = $result->fetch_assoc()) {
    $fullName = $row['first_name'] . ' ' . $row['last_name'];  // Concatenate first and last name
}
$stmt->close();

$stmt = $conn->prepare("
    SELECT 
        s.schedule_id,
        sub.subject_name,
        r.room_name,
        s.semester,
        s.day,
        s.start_time,
        s.end_time
    FROM 
        schedules s
    LEFT JOIN 
        subjects sub ON s.subject_id = sub.subject_id
    LEFT JOIN 
        rooms r ON s.room_id = r.room_id
    WHERE 
        s.student_id = ?
");
$stmt->bind_param("i", $student_id);  // The student's ID is passed
$stmt->execute();
$result = $stmt->get_result();

// Collect the schedule data
$schedules = [];
while ($row = $result->fetch_assoc()) {
    $schedules[] = $row;
}
$stmt->close();

// Function to format time into 12-hour format with AM/PM
function formatTime($time) {
    $date = new DateTime($time);
    return $date->format('h:i A');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Schedule</title>
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
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .main-content {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
        }

        .schedule-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .schedule-item {
            background: rgba(255, 255, 255, 0.05);
            padding: 20px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .schedule-day {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #9b4dff;
        }

        .schedule-details {
            font-size: 14px;
            line-height: 1.6;
        }

        .schedule-time {
            display: inline-block;
            padding: 4px 12px;
            background: rgba(155, 77, 255, 0.2);
            border-radius: 20px;
            margin-top: 10px;
            font-size: 12px;
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
        }

        .btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-1px);
        }

        .message {
            background: rgba(155, 77, 255, 0.1);
            border: 1px solid rgba(155, 77, 255, 0.2);
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 20px;
            color: white;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <main class="main-content">
            <div class="card">
                <h2 class="welcome-text">Welcome, <span><?php echo htmlspecialchars($fullName); ?></span></h2>
                
                <?php if(isset($_SESSION['message'])): ?>
                    <div class="message">
                        <?php 
                        echo htmlspecialchars($_SESSION['message']);
                        unset($_SESSION['message']);
                        ?>
                    </div>
                <?php endif; ?>

                <?php if (count($schedules) > 0): ?>
                    <div class="schedule-grid">
                        <?php foreach ($schedules as $schedule): ?>
                            <div class="schedule-item">
                                <div class="schedule-day">
                                    <?= htmlspecialchars($schedule['day']) ?>
                                </div>
                                <div class="schedule-details">
                                    <strong><?= htmlspecialchars($schedule['subject_name'] ?? 'N/A') ?></strong><br>
                                    Room: <?= htmlspecialchars($schedule['room_name'] ?? 'N/A') ?><br>
                                    Semester: <?= htmlspecialchars($schedule['semester']) ?>
                                    <div class="schedule-time">
                                        <?= formatTime($schedule['start_time']) ?> - <?= formatTime($schedule['end_time']) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                    </a>
                </div>
            </div>
        </main>
        <div style="text-align: center; margin-top: 30px;">
                    <a href="student_dashboard.php" class="btn">
                        Back to Dashboard
                    </a>
    </div>
    <script>
        window.onload = function() {
            <?php if (count($schedules) === 0): ?>
                alert("No schedule found for your account.");
            <?php endif; ?>
        }
    </script>
</body>
</html>
