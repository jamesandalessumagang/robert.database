<?php
@include 'config.php';
session_start();

if (isset($_POST['submit'])) {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $birth_date = mysqli_real_escape_string($conn, $_POST['birth_date']);
    $contact_number = mysqli_real_escape_string($conn, $_POST['contact_number']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $id_number = mysqli_real_escape_string($conn, $_POST['id_number']);
    $user_type = $_POST['user_type']; // "admin" or "user"

    $check_email = $conn->prepare("SELECT * FROM students WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $email_result = $check_email->get_result();

    $check_id = $conn->prepare("SELECT * FROM students WHERE id_number = ?");
    $check_id->bind_param("s", $id_number);
    $check_id->execute();
    $id_result = $check_id->get_result();

    if ($email_result->num_rows > 0) {
        $error[] = 'Email already exists!';
    } elseif ($id_result->num_rows > 0) {
        $error[] = 'ID Number already exists!';
    } else {
        $stmt = $conn->prepare("INSERT INTO students (first_name, last_name, email, password, birth_date, contact_number, address, id_number, user_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", $first_name, $last_name, $email, $password, $birth_date, $contact_number, $address, $id_number, $user_type);
        
        if ($stmt->execute()) {
            header('location:login_f.php');
            exit();
        } else {
            $error[] = 'Registration failed. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 8px;
            padding: 30px;
            width: 100%;
            max-width: 360px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        h2 {
            color: #ffffff;
            font-size: 42px;
            font-weight: 600;
            margin-bottom: 30px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3),
                         0 0 20px rgba(255, 0, 0, 0.5),
                         0 0 40px rgba(255, 0, 0, 0.3);
            animation: glow 2s ease-in-out infinite alternate;
        }

        @keyframes glow {
            from {
                text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3),
                             0 0 20px rgba(255, 0, 0, 0.5),
                             0 0 40px rgba(255, 0, 0, 0.3);
            }
            to {
                text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3),
                             0 0 20px rgba(255, 0, 0, 0.5),
                             0 0 40px rgba(255, 0, 0, 0.3);
            }
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-label {
            color: #ffffff;
            font-size: 13px;
            margin-bottom: 5px;
            display: block;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="date"],
        input[type="file"],
        select {
            width: 100%;
            padding: 12px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            color: #ffffff;
            font-size: 14px;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #ff0000;
            background: rgba(255, 255, 255, 0.2);
        }

        input::placeholder,
        select {
            color: rgba(255, 255, 255, 0.7);
        }

        /* Style for date input */
        input[type="date"]::-webkit-calendar-picker-indicator {
            opacity: 0.7;
        }

        /* Style for select dropdown */
        select {
            cursor: pointer;
            appearance: none;
            background: rgba(255, 255, 255, 0.1) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23fff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") no-repeat right center;
            padding-right: 35px;
        }

        select option {
            background: #800000;
            color: #ffffff;
        }

        select option:hover,
        select option:focus,
        select option:active,
        select option:checked {
            background: #a00000 linear-gradient(0deg, #a00000 0%, #a00000 100%);
            color: #ffffff;
        }

        .form-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(to bottom, #000000 0%, #800000 40%, #ff0000 100%);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            margin: 20px 0 15px;
            transition: all 0.3s ease;
        }

        .form-btn:hover {
            background: linear-gradient(to bottom, #1a0000 0%, #990000 40%, #ff1a1a 100%);
            transform: translateY(-1px);
            border-color: rgba(255, 255, 255, 0.3);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }

        .error-msg {
            background: rgba(255, 77, 77, 0.2);
            color: #ffffff;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 13px;
            text-align: center;
        }

        p {
            text-align: center;
            color: #ffffff;
            font-size: 13px;
        }

        p a {
            color: #9b4dff;
            text-decoration: none;
        }

        p a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Register</h2>
        <form action="" method="post">
            <?php
            if(isset($error)){
                foreach($error as $error){
                    echo '<div class="error-msg">'.$error.'</div>';
                }
            }
            ?>
            
            <div class="input-group">
                <label class="input-label">First Name</label>
                <input type="text" name="first_name" required>
            </div>
            
            <div class="input-group">
                <label class="input-label">Last Name</label>
                <input type="text" name="last_name" required>
            </div>
            
            <div class="input-group">
                <label class="input-label">Email</label>
                <input type="email" name="email" required>
            </div>
            
            <div class="input-group">
                <label class="input-label">Password</label>
                <input type="password" name="password" required>
            </div>
            
            <div class="input-group">
                <label class="input-label">Birth Date</label>
                <input type="date" name="birth_date" required>
            </div>
            
            <div class="input-group">
                <label class="input-label">Contact Number</label>
                <input type="text" name="contact_number" required>
            </div>
            
            <div class="input-group">
                <label class="input-label">Address</label>
                <input type="text" name="address" required>
            </div>
            
            <div class="input-group">
                <label class="input-label">ID Number</label>
                <input type="text" name="id_number" required>
            </div>
            
            <div class="input-group">
                <label class="input-label">User Type</label>
                <select name="user_type">
                    <option value="user">Student</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <input type="submit" name="submit" value="Register Now" class="form-btn">
            
            <p>Already have an account? <a href="login_f.php">Login now</a></p>
        </form>
    </div>
</body>
</html>