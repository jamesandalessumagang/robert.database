<?php
include 'config.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_subject'])) {
    $subject_id = $_POST['subject_id']; 
    $subject_name = $_POST['subject_name'];
    $code = $_POST['code'];

  
    $stmt = $conn->prepare("INSERT INTO subjects (subject_id, subject_name, code) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $subject_id, $subject_name, $code);
    $stmt->execute();
    $stmt->close();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_subject'])) {
    $subject_id = $_POST['subject_id'];
    $subject_name = $_POST['subject_name'];
    $code = $_POST['code'];

  
    $stmt = $conn->prepare("UPDATE subjects SET subject_name = ?, code = ? WHERE subject_id = ?");
    $stmt->bind_param("ssi", $subject_name, $code, $subject_id);
    $stmt->execute();
    $stmt->close();
}


if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    
    $stmt = $conn->prepare("DELETE FROM schedules WHERE subject_id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM subjects WHERE subject_id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
}

$result = $conn->query("SELECT subject_id, subject_name, code FROM subjects");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subject Management</title>
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
            min-width: 200px;
            text-align: center;
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

        .btn {
            padding: 12px 24px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: rgba(128, 0, 0, 0.6);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-primary:hover {
            background: rgba(128, 0, 0, 0.8);
        }

        .btn-danger {
            background: rgba(128, 0, 0, 0.6);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-danger:hover {
            background: rgba(128, 0, 0, 0.8);
        }

        .btn-secondary {
            background: rgba(0, 0, 0, 0.3);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-secondary:hover {
            background: rgba(128, 0, 0, 0.6);
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
        }

        .edit-form {
            background: rgba(0, 0, 0, 0.4);
            padding: 20px;
            border-radius: 12px;
            margin-top: 8px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body>
        <main class="main-content">
            <div class="card">
                <h2 style="margin-bottom: 24px">Add new subject</h2>
                <form method="POST" action="manage_subjects.php">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Subject ID</label>
                            <input type="number" name="subject_id" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Subject Name</label>
                            <input type="text" name="subject_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Subject Code</label>
                            <input type="text" name="code" class="form-control" required>
                        </div>
                    </div>
                    <button type="submit" name="add_subject" class="btn btn-primary">Add now</button>
                </form>
            </div>

            <div class="card">
                <h2 style="margin-bottom: 24px">Subjects</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Subject ID</th>
                                <th>Subject Name</th>
                                <th>Code</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr id="row-<?php echo $row['subject_id']; ?>">
                                    <td><?php echo $row['subject_id']; ?></td>
                                    <td>
                                        <span id="subject-name-<?php echo $row['subject_id']; ?>"><?php echo $row['subject_name']; ?></span>
                                    </td>
                                    <td>
                                        <span id="subject-code-<?php echo $row['subject_id']; ?>"><?php echo $row['code']; ?></span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn btn-secondary" onclick="toggleEdit(<?php echo $row['subject_id']; ?>)">Edit</button>
                                            <a href="manage_subjects.php?delete_id=<?php echo $row['subject_id']; ?>" 
                                               class="btn btn-danger" 
                                               onclick="return confirm('Are you sure you want to delete this subject?')">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                                <tr id="edit-form-row-<?php echo $row['subject_id']; ?>" style="display:none;">
                                    <td colspan="4">
                                        <form method="POST" action="manage_subjects.php" class="edit-form">
                                            <div class="form-grid">
                                                <input type="hidden" name="subject_id" value="<?php echo $row['subject_id']; ?>">
                                                <div class="form-group">
                                                    <label class="form-label">Subject Name</label>
                                                    <input type="text" name="subject_name" class="form-control" value="<?php echo $row['subject_name']; ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label">Subject Code</label>
                                                    <input type="text" name="code" class="form-control" value="<?php echo $row['code']; ?>" required>
                                                </div>
                                            </div>
                                            <div class="action-buttons">
                                                <button type="submit" name="update_subject" class="btn btn-primary">Update</button>
                                                <button type="button" class="btn btn-secondary" onclick="cancelEdit(<?php echo $row['subject_id']; ?>)">Cancel</button>
                                            </div>
                                        </form>
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

    <script>
        function toggleEdit(subjectId) {
            document.getElementById('edit-form-row-' + subjectId).style.display = 
                document.getElementById('edit-form-row-' + subjectId).style.display === 'none' ? 'table-row' : 'none';
        }

        function cancelEdit(subjectId) {
            document.getElementById('edit-form-row-' + subjectId).style.display = 'none';
        }
    </script>
</body>
</html>
