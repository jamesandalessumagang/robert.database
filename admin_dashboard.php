<?php
@include 'config.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('location:login_f.php');
    exit();
}

// Fetch the logged-in admin's full name and ID
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT first_name, last_name FROM students WHERE student_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $fullName = $user['first_name'] . ' ' . $user['last_name'];
} else {
    $fullName = "Admin"; // Default fallback if session data is not set
}

// Handle deletion of a student
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM students WHERE student_id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    
    // After deleting the student, reorder student IDs
    reorderStudentIds($conn);

    // Redirect to the dashboard
    $_SESSION['message'] = "Student deleted successfully.";
    header('location: admin_dashboard.php');
    exit();
}

// Function to reorder student IDs sequentially from 1
function reorderStudentIds($conn) {
    // Query all students
    $stmt = $conn->prepare("SELECT student_id FROM students WHERE user_type = 'user' ORDER BY student_id");
    $stmt->execute();
    $result = $stmt->get_result();

    $new_id = 1; // Start renumbering from 1

    // Update each student to have sequential IDs
    while ($row = $result->fetch_assoc()) {
        $current_id = $row['student_id'];

        // Update student_id to a new sequential value
        $update_stmt = $conn->prepare("UPDATE students SET student_id = ? WHERE student_id = ?");
        $update_stmt->bind_param("ii", $new_id, $current_id);
        $update_stmt->execute();

        $new_id++; // Increment for the next student
    }
}

// Handle adding a new student
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['first_name'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $id_number = $_POST['id_number'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $birth_date = $_POST['birth_date'];
    $contact_number = $_POST['contact_number'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("INSERT INTO students (first_name, last_name, email, id_number, password, birth_date, contact_number, address, user_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'user')");
    $stmt->bind_param("ssssssss", $first_name, $last_name, $email, $id_number, $password, $birth_date, $contact_number, $address);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Student added successfully.";
    } else {
        $_SESSION['message'] = "Error adding student.";
    }
    header('location:admin_dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
            max-width: 800px;
            margin: 0 auto;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 30px;
            color: white;
        }

        .dashboard-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .welcome-text {
            font-size: 24px;
            font-weight: 500;
            margin-bottom: 20px;
        }

        .nav-buttons {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            margin-bottom: 30px;
        }

        .nav-btn {
            background: rgba(128, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 12px 20px;
            border-radius: 12px;
            color: white;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
            width: 200px;
        }

        .nav-btn:hover {
            background: rgba(128, 0, 0, 0.6);
        }

        .section-title {
            font-size: 20px;
            margin-bottom: 20px;
            padding-top: 20px;
        }

        .add-student-btn {
            background: linear-gradient(to bottom, #000000 0%, #800000 40%, #ff0000 100%);
            color: white;
            padding: 12px 24px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            cursor: pointer;
            font-weight: 500;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .add-student-btn:hover {
            background: linear-gradient(to bottom, #1a0000 0%, #990000 40%, #ff1a1a 100%);
            transform: translateY(-2px);
            border-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
        }

        .student-form {
            background: rgba(0, 0, 0, 0.4);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: none;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        .input-group {
            margin-bottom: 15px;
        }

        .input-group label {
            display: block;
            margin-bottom: 5px;
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
        }

        .input-group input {
            width: 100%;
            padding: 10px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: white;
        }

        .students-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
        }

        .students-table th {
            background: rgba(128, 0, 0, 0.4);
            padding: 12px;
            text-align: left;
            font-weight: 500;
        }

        .students-table td {
            background: rgba(0, 0, 0, 0.3);
            padding: 12px;
        }

        .students-table tr:hover td {
            background: rgba(128, 0, 0, 0.3);
        }

        .action-btn {
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            color: white;
            margin-right: 8px;
            font-size: 14px;
            display: inline-block;
        }

        .edit-btn {
            background: linear-gradient(to bottom, #000000 0%, #800000 40%, #ff0000 100%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .edit-btn:hover {
            background: linear-gradient(to bottom, #1a0000 0%, #990000 40%, #ff1a1a 100%);
            transform: translateY(-1px);
            border-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
        }

        .delete-btn {
            background: linear-gradient(to bottom, #000000 0%, #220000 20%, #330000 40%, #440000 60%, #550000 80%, #660000 100%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .delete-btn:hover {
            background: linear-gradient(to bottom, #000000 0%, #330000 20%, #440000 40%, #550000 60%, #660000 80%, #770000 100%);
            border-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
        }

        .logout-btn {
            background: linear-gradient(to bottom, #000000 0%, #800000 40%, #ff0000 100%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            min-width: 200px;
            text-align: center;
        }

        .logout-btn:hover {
            background: linear-gradient(to bottom, #1a0000 0%, #990000 40%, #ff1a1a 100%);
            transform: translateY(-1px);
            border-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
        }

        /* Move button container to bottom and adjust styling */
        .button-container {
            display: flex;
            justify-content: center;
            margin-top: 40px;  /* Changed from margin-bottom */
            width: 100%;
        }

        .logout-btn {
            background: rgba(128, 0, 0, 0.4);
            min-width: 200px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
    <div class="div">
<a href="logout.php" class="nav-btn logout-btn">Logout</a>
</div>
        <div class="dashboard-header">
            <h1 class=" ">Welcome, <?php echo htmlspecialchars($fullName); ?></h1>
        </div>
        <div class="nav-buttons">
            <a href="manage_schedules.php" class="nav-btn">Schedules</a>
            <a href="manage_rooms.php" class="nav-btn">Rooms</a>
            <a href="manage_subjects.php" class="nav-btn">Subjects</a>
            <a href="manage_grade.php" class="nav-btn">Grades</a>
        </div>

        <h2 class="section-title">Manage Students</h2>
        
        <button class="add-student-btn" onclick="toggleAddStudentForm()">+ Add New Student</button>

        <form id="addStudentForm" class="student-form" action="admin_dashboard.php" method="post">
            <div class="form-grid">
                <div class="input-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" required>
                </div>
                <div class="input-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" required>
                </div>
                <div class="input-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="input-group">
                    <label>ID Number</label>
                    <input type="text" name="id_number" required>
                </div>
                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <div class="input-group">
                    <label>Birth Date</label>
                    <input type="date" name="birth_date" required>
                </div>
                <div class="input-group">
                    <label>Contact Number</label>
                    <input type="text" name="contact_number" required>
                </div>
                <div class="input-group">
                    <label>Address</label>
                    <input type="text" name="address" required>
                </div>
            </div>
            <button type="submit" class="add-student-btn">Add Student</button>
        </form>

        <table class="students-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>ID Number</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $conn->prepare("SELECT * FROM students WHERE user_type = 'user'");
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    echo "
                    <tr>
                        <td>{$row['student_id']}</td>
                        <td>{$row['first_name']} {$row['last_name']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['id_number']}</td>
                        <td>
                            <a href='edit_student.php?id={$row['student_id']}' class='action-btn edit-btn'>Edit</a>
                            <a href='admin_dashboard.php?delete={$row['student_id']}' class='action-btn delete-btn' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                        </td>
                    </tr>
                    ";
                }
                ?>
            </tbody>
        </table>
    </div>
    <script>
        function toggleAddStudentForm() {
            const form = document.getElementById('addStudentForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>
</html>
