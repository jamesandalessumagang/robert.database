<?php
@include 'config.php';
session_start();

// Check if user is logged in and if the user is a student
if (!isset($_SESSION['student_id']) || $_SESSION['user_type'] !== 'user') {
    header('location:login_f.php');
    exit();
}

// Fetch the student's name to display on the dashboard
$student_id = $_SESSION['student_id'];
$stmt = $conn->prepare("SELECT first_name, last_name FROM students WHERE student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $fullName = $user['first_name'] . ' ' . $user['last_name'];
} else {
    // Handle the case where student data is not found
    $fullName = "Student";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 30px;
            color: white;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .welcome-text {
            font-size: 24px;
            font-weight: 500;
        }

        .dashboard-grid {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 30px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .dashboard-card {
            background: black;
            border-radius: 16px;
            padding: 25px;
            text-align: center;
            transition: transform 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
            width: 100%;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.15);
        }

        .card-icon {
            font-size: 36px;
            margin-bottom: 15px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 10px;
        }

        .card-description {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 20px;
        }

        .card-link {
            display: inline-block;
            padding: 10px 20px;
            background: linear-gradient(to bottom, #000000 0%, #800000 40%, #ff0000 100%);
            color: white;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .card-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 0, 0, 0.3);
        }

        .logout-btn {
            background: linear-gradient(to bottom, #000000 0%, #800000 40%, #ff0000 100%);
            padding: 10px 20px;
            border-radius: 30px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 0, 0, 0.3);
        }

        .message {
            background: rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 20px;
            }
            
            .dashboard-header {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="welcome-text">Welcome, <?php echo htmlspecialchars($fullName); ?></h1>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>

        <?php
        if (isset($_SESSION['message'])) {
            echo "<div class='message'>" . htmlspecialchars($_SESSION['message']) . "</div>";
            unset($_SESSION['message']);
        }
        ?>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <div class="card-icon"><i class="fas fa-user"></i></div>
                <h3 class="card-title">Profile</h3>
                <p class="card-description">View and update your personal information</p>
                <a href="user_profile.php" class="card-link">View Profile</a>
            </div>

            <div class="dashboard-card">
                <div class="card-icon"><i class="fas fa-calendar-alt"></i></div>
                <h3 class="card-title">Schedule</h3>
                <p class="card-description">Check your class schedule and timetable</p>
                <a href="view_schedule.php" class="card-link">View Schedule</a>
            </div>

            <div class="dashboard-card">
                <div class="card-icon"><i class="fas fa-chart-bar"></i></div>
                <h3 class="card-title">Grades</h3>
                <p class="card-description">View your academic performance and grades</p>
                <a href="view_grade.php" class="card-link">View Grades</a>
            </div>
        </div>
    </div>
</body>
</html>