<?php
    @include 'config.php';
    session_start();

    // Check if the student is logged in
    if (!isset($_SESSION['student_id'])) {
        header("Location: login_f.php");
        exit();
    }

    // Fetch the student's profile information
    $studentId = $_SESSION['student_id'];
    $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();

    if (!$student) {
        $_SESSION['message'] = "Student not found.";
        header("Location: logout.php");
        exit();
    }

    // Handle profile update
    if (isset($_POST['update_profile'])) {
        $firstName = $_POST['first_name'];
        $lastName = $_POST['last_name'];
        $birthDate = $_POST['birth_date'];
        $contactNumber = $_POST['contact_number'];
        $address = $_POST['address'];

        $updateStmt = $conn->prepare("UPDATE students SET first_name = ?, last_name = ?, birth_date = ?, contact_number = ?, address = ? WHERE student_id = ?");
        $updateStmt->bind_param("sssssi", $firstName, $lastName, $birthDate, $contactNumber, $address, $studentId);
        $updateStmt->execute();

        $_SESSION['message'] = "Profile updated successfully!";
        header("Location: user_profile.php");
        exit();
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Student Profile</title>
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
                display: flex;
                flex-direction: column;
                gap: 20px;
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

            .profile-info {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 20px;
                margin-bottom: 30px;
            }

            .info-item {
                background: rgba(128, 0, 0, 0.3);
                padding: 20px;
                border-radius: 16px;
                border: 1px solid rgba(255, 255, 255, 0.1);
            }

            .info-label {
                font-size: 12px;
                text-transform: uppercase;
                letter-spacing: 1px;
                margin-bottom: 8px;
                color: rgba(255, 255, 255, 0.7);
            }

            .info-value {
                font-size: 16px;
                font-weight: 500;
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
                border: 1px solid rgba(255, 255, 255, 0.2);
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
                text-align: center;
                background: linear-gradient(to bottom, #000000 0%, #800000 40%, #ff0000 100%);
                color: white;
                position: relative;
                overflow: hidden;
            }

            .btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(255, 0, 0, 0.3);
            }

            .btn:active {
                transform: translateY(0);
                box-shadow: 0 2px 10px rgba(255, 0, 0, 0.2);
            }

            .btn-primary {
                width: 100%;
            }

            .btn-secondary {
                color: white;
            }

            .message {
                background: rgba(128, 0, 0, 0.2);
                color: white;
                padding: 16px;
                border-radius: 12px;
                margin-bottom: 20px;
                border: 1px solid rgba(255, 255, 255, 0.2);
            }
        </style>
    </head>
    <body>
        <div class="dashboard-container">
            <main class="main-content">
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="message">
                        <?= htmlspecialchars($_SESSION['message']) ?>
                    </div>
                    <?php unset($_SESSION['message']); ?>
                <?php endif; ?>

                <div class="card">
                    <h2 style="margin-bottom: 24px">My profile </h2>
                    <div class="profile-info">
                        <div class="info-item">
                            <div class="info-label">Student ID</div>
                            <div class="info-value"><?= htmlspecialchars($student['student_id']) ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">ID Number</div>
                            <div class="info-value"><?= htmlspecialchars($student['id_number']) ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Full Name</div>
                            <div class="info-value">
                                <?= htmlspecialchars($student['first_name']) . ' ' . htmlspecialchars($student['last_name']) ?>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Birth Date</div>
                            <div class="info-value"><?= htmlspecialchars($student['birth_date']) ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Contact Number</div>
                            <div class="info-value"><?= htmlspecialchars($student['contact_number']) ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Address</div>
                            <div class="info-value"><?= htmlspecialchars($student['address']) ?></div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h2 style="margin-bottom: 24px">Update Profile</h2>
                    <form action="user_profile.php" method="POST">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control" 
                                    value="<?= htmlspecialchars($student['first_name']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control" 
                                    value="<?= htmlspecialchars($student['last_name']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Birth Date</label>
                                <input type="date" name="birth_date" class="form-control" 
                                    value="<?= htmlspecialchars($student['birth_date']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Contact Number</label>
                                <input type="text" name="contact_number" class="form-control" 
                                    value="<?= htmlspecialchars($student['contact_number']) ?>" required>
                            </div>
                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label class="form-label">Address</label>
                                <input type="text" name="address" class="form-control" 
                                    value="<?= htmlspecialchars($student['address']) ?>" required>
                            </div>
                        </div>
                        <button type="submit" name="update_profile" class="btn btn-primary">
                            Update profile
                        </button>
                        <a href="student_dashboard.php" class="btn btn-primary" style="margin-top: 30px">
                    Back to dashboard </a>
                    </form>
                </div>
            </main>
        </div>
    </body>
    </html>
                    