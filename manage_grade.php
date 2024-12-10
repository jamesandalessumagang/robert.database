<?php
@include 'config.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('location:login_f.php');  // Redirect to login if not an admin
    exit();
}

// Fetch all students with their IDs and full names
$students = $conn->query("SELECT student_id, CONCAT(student_id, ' - ', first_name, ' ', last_name) AS student_name FROM students");

// Fetch all subjects
$subjects = $conn->query("SELECT subject_id, subject_name FROM subjects");

// Fetch admin's full name from the database based on session user_id
$fullName = '';
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $result = $conn->query("SELECT CONCAT(first_name, ' ', last_name) AS full_name FROM users WHERE user_id = $user_id");

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $fullName = $row['full_name'];
    }
}

// Initialize variables for edit
$edit_student_id = $edit_subject_id = $edit_grade = null;

// Handle edit request
if (isset($_GET['edit_grade_id'])) {
    $edit_grade_id = intval($_GET['edit_grade_id']);

    // Fetch the grade details
    $edit_stmt = $conn->prepare("SELECT student_id, subject_id, grade FROM student_grades WHERE grade_id = ?");
    $edit_stmt->bind_param("i", $edit_grade_id);
    $edit_stmt->execute();
    $edit_result = $edit_stmt->get_result();

    if ($edit_result->num_rows > 0) {
        $edit_data = $edit_result->fetch_assoc();
        $edit_student_id = $edit_data['student_id'];
        $edit_subject_id = $edit_data['subject_id'];
        $edit_grade = $edit_data['grade'];
    } else {
        $_SESSION['message'] = "Grade not found.";
        header('location:manage_grade.php');
        exit();
    }
    $edit_stmt->close();
}

// Handle adding/updating grades
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_grade'])) {
    $student_id = intval($_POST['student_id']);
    $subject_id = intval($_POST['subject_id']);
    $grade = floatval($_POST['grade']);
    $status = ($grade <= 3.0) ? 'Pass' : 'Fail';

    // Insert or update the grade
    if (isset($edit_grade_id)) {
        // Update existing grade
        $stmt = $conn->prepare("UPDATE student_grades SET student_id = ?, subject_id = ?, grade = ?, status = ? WHERE grade_id = ?");
        $stmt->bind_param("iissi", $student_id, $subject_id, $grade, $status, $edit_grade_id);
    } else {
        // Insert new grade
        $stmt = $conn->prepare("INSERT INTO student_grades (student_id, subject_id, grade, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $student_id, $subject_id, $grade, $status);
    }

    if ($stmt->execute()) {
        $_SESSION['message'] = "Grade successfully added/updated!";
    } else {
        $_SESSION['message'] = "Failed to add/update grade.";
    }
    $stmt->close();

    header('location:manage_grade.php');
    exit();
}

// Handle deleting grades
if (isset($_GET['delete_grade_id'])) {
    $grade_id = intval($_GET['delete_grade_id']);
    
    // Prepare DELETE query
    $delete_stmt = $conn->prepare("DELETE FROM student_grades WHERE grade_id = ?");
    $delete_stmt->bind_param("i", $grade_id);

    if ($delete_stmt->execute()) {
        $_SESSION['message'] = "Grade successfully deleted!";
    } else {
        $_SESSION['message'] = "Failed to delete grade.";
    }
    $delete_stmt->close();
    
    // Redirect back to manage grades page
    header('location:manage_grade.php');
    exit();
}

// Fetch all grades for display
$grades_result = $conn->query("SELECT 
    sg.grade_id, 
    CONCAT(s.student_id, ' - ', s.first_name, ' ', s.last_name) AS student_name, 
    sub.subject_name, 
    sg.grade, 
    sg.status 
FROM 
    student_grades sg
JOIN students s ON sg.student_id = s.student_id
JOIN subjects sub ON sg.subject_id = sub.subject_id
ORDER BY student_name, subject_name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grade Management</title>
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

        .card {
            background: rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            position: relative;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            color: white;
            font-size: 14px;
            transition: all 0.3s ease;
            -webkit-appearance: none;
            appearance: none;
        }

        .form-control:focus {
            outline: none;
            border-color: rgba(255, 255, 255, 0.5);
            background: rgba(255, 255, 255, 0.15);
        }

        .form-label {
            position: absolute;
            left: 16px;
            top: -10px;
            background: rgba(75, 0, 130, 0.8);
            padding: 0 8px;
            font-size: 12px;
            border-radius: 4px;
            color: white;
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
            background: linear-gradient(to bottom, #000000 0%, #800000 40%, #ff0000 100%);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn:hover {
            background: linear-gradient(to bottom, #000000 0%, #600000 40%, #cc0000 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .btn-primary {
            min-width: 200px;
            text-align: center;
        }

        .btn-danger {
            background: linear-gradient(to bottom, #ff4d4d 0%, #cc0000 40%, #800000 100%);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-danger:hover {
            background: linear-gradient(to bottom, #cc0000 0%, #800000 40%, #4d0000 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
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
            background: rgba(255, 255, 255, 0.05);
        }

        tr td:first-child {
            border-radius: 12px 0 0 12px;
        }

        tr td:last-child {
            border-radius: 0 12px 12px 0;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-pass {
            background: #4CAF50;
            color: white;
        }

        .status-fail {
            background: #ff4d4d;
            color: white;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        /* Add styles for select options */
        .form-control option {
            background-color: rgba(75, 0, 130, 0.9); /* Dark purple background for options */
            color: white;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <main class="main-content">
            <div class="card">
                <h2 style="margin-bottom: 24px">
                    <?php echo isset($edit_grade_id) ? 'Edit Grade' : 'Add new grade'; ?>
                </h2>
                
                <form method="POST" action="manage_grade.php">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Student</label>
                            <select name="student_id" class="form-control" required>
                                <option value="">Select Student</option>
                                <?php while ($row = $students->fetch_assoc()): ?>
                                    <option value="<?php echo $row['student_id']; ?>" 
                                        <?php echo ($row['student_id'] == $edit_student_id) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($row['student_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Subject</label>
                            <select name="subject_id" class="form-control" required>
                                <option value="">Select Subject</option>
                                <?php while ($row = $subjects->fetch_assoc()): ?>
                                    <option value="<?php echo $row['subject_id']; ?>"
                                        <?php echo ($row['subject_id'] == $edit_subject_id) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($row['subject_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Grade (3.0 or below = Pass)</label>
                            <input type="number" 
                                   step="0.01" 
                                   name="grade" 
                                   class="form-control"
                                   required 
                                   value="<?php echo isset($edit_grade) ? number_format($edit_grade, 2) : ''; ?>">
                        </div>
                    </div>

                    <button type="submit" name="submit_grade" class="btn btn-primary">
                        <?php echo isset($edit_grade_id) ? 'Update Grade' : 'Add Grade'; ?>
                    </button>
                </form>
            </div>

            <div class="card">
                <h2 style="margin-bottom: 24px">Graded</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Subject</th>
                                <th>Grade</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $grades_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                                    <td><?php echo number_format($row['grade'], 2); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $row['status'] === 'Pass' ? 'status-pass' : 'status-fail'; ?>">
                                            <?php echo $row['status']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="manage_grade.php?edit_grade_id=<?php echo $row['grade_id']; ?>" 
                                               class="btn btn-primary">Edit</a>
                                            <a href="manage_grade.php?delete_grade_id=<?php echo $row['grade_id']; ?>" 
                                               class="btn btn-danger" 
                                               onclick="return confirm('Are you sure?')">Delete</a>
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

    <script>
        // Keep existing JavaScript
    </script>
</body>
</html>