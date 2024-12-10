<?php

@include 'config.php';
session_start();


if (isset($_POST['add_student'])) {
    $idNumber = $_POST['id_number'];
    $studentId = $_POST['student_id'];
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $birthDate = $_POST['birth_date'];
    $contactNumber = $_POST['contact_number'];
    $address = $_POST['address'];

    if (empty($idNumber) || empty($studentId)) {
        $_SESSION['message'] = "ID Number and Student ID are required.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM students WHERE id_number = ? OR student_id = ?");
        $stmt->bind_param("si", $idNumber, $studentId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $_SESSION['message'] = "ID Number or Student ID already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO students (id_number, student_id, first_name, last_name, birth_date, contact_number, address) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sisssss", $idNumber, $studentId, $firstName, $lastName, $birthDate, $contactNumber, $address);
            $stmt->execute();
            $_SESSION['message'] = "Student added successfully!";
        }
    }
}


if (isset($_GET['edit'])) {
    $idNumber = $_GET['edit'];

    
    $stmt = $conn->prepare("SELECT * FROM students WHERE id_number = ?");
    $stmt->bind_param("s", $idNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
}


if (isset($_POST['update_student'])) {
    $idNumber = $_POST['id_number'];
    $studentId = $_POST['student_id'];
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $birthDate = $_POST['birth_date'];
    $contactNumber = $_POST['contact_number'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("UPDATE students SET student_id = ?, first_name = ?, last_name = ?, birth_date = ?, contact_number = ?, address = ? WHERE id_number = ?");
    $stmt->bind_param("sssssss", $studentId, $firstName, $lastName, $birthDate, $contactNumber, $address, $idNumber);
    $stmt->execute();

    $_SESSION['message'] = "Student details updated successfully!";
    header("Location: manage_students.php"); 
    exit();
}

if (isset($_GET['delete'])) {
    $idNumber = $_GET['delete'];

    
    $stmt = $conn->prepare("DELETE FROM students WHERE id_number = ?");
    $stmt->bind_param("s", $idNumber);
    $stmt->execute();
    $_SESSION['message'] = "Student deleted successfully!";

  
    header("Location: manage_students.php");
    exit();
}


$result = $conn->query("SELECT * FROM students");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Students</title>
    <link rel="stylesheet" href="STYLE/manage_students.css">
</head>
<body>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600&display=swap');

* {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    outline: none;
    border: none;
    text-decoration: none;
}

body {
    background: url('11.jpg') no-repeat center center fixed;
    background-size: cover;
    color: #fff;
    margin: 0;
    padding: 0;
}

h2 {
    color: #2c3e50;
    margin-bottom: 20px;
    text-align: center;
    padding-left: 50px;
}

form {
    background-color: #ecf0f1;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    margin-bottom: 20px;
    width: 100%;
    max-width: 500px;
    text-align: center;
    margin-left: auto;
    margin-right: auto;
}

form input[type="text"],
form input[type="date"],
form button {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border-radius: 4px;
    border: 1px solid #ddd;
    font-size: 16px;
    background-color: #fff;
    color: #333;
}

form input[type="text"]:focus,
form input[type="date"]:focus {
    border-color: #3498db;
    outline: none;
}

form button {
    background-color: #3498db;
    color: #fff;
    border: none;
    cursor: pointer;
}

form button:hover {
    background-color: #2980b9;
}

table {
    width: 100%;
    max-width: 800px;
    border-collapse: collapse;
    background-color: blue;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    overflow: hidden;
}

table th,
table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

table th {
    background-color: #3498db;
    color: yellow;
    font-weight: bold;
}

table tr:nth-child(even) {
    background-color: skyblue;
}

a {
    color: #3498db;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

.webintro {
    color: #2c3e50;
}

.container {
    justify-content: center;
    padding: 1px;
    padding-bottom: 60px;
}

.container .content {
    text-align: center;
}

.container .content h3 {
    font-size: 30px;
    color: #3498db;
}

.container .content h3 span {
    background: black;
    color: #fff;
    border-radius: 5px;
    padding: 0 15px;
    font-weight: bold;
    padding-right: 50px;
    
}

.container .content h1 {
    font-size: 50px;
    color: #2c3e50;
}

.container .content h1 span {
    color: #e74c3c;
}

.container .content p {
    font-size: 25px;
    margin-bottom: 20px;
}

.container .content .btn {
    display: inline-block;
    padding: 10px 30px;
    font-size: 20px;
    background: #2c3e50;
    color: #fff;
    margin: 0 5px;
    text-transform: capitalize;
    border-radius: 5px;
}

.container .content .btn:hover {
    background: #e74c3c;
}

.content {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 20px;
    padding: 20px;
    border-radius: 10px;
    background-color: #34495e;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    justify-content: space-between;
}

button {
    padding: 12px 20px;
    background-color: #34495e;
    border: none;
    color: white;
    cursor: pointer;
    border-radius: 5px;
}

button:hover {
    background-color: #e74c3c;
}

    </style>
<div class="container">
   <div class="content">
     <h3>Welcome, <span>Administrator</span></h3>
      <div class="buttons">
         <a href="admin_p.php" class="btn">Back to Dashboard</a>
      </div>
   </div>
</div>


    <?php if (!isset($_GET['edit'])): ?>
        
        <h2>Manage Students</h2>

   
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message">
                <?= $_SESSION['message'] ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

      
        <button onclick="document.getElementById('add-form').style.display='block'; document.getElementById('edit-form').style.display='none';">Add Student</button>

      
        <div id="add-form" style="display:none;">
            <h3>Add Student</h3>
            <form action="manage_students.php" method="POST">
                <input type="text" name="id_number" placeholder="ID Number" required>
                <input type="text" name="student_id" placeholder="Student ID" required>
                <input type="text" name="first_name" placeholder="First Name" required>
                <input type="text" name="last_name" placeholder="Last Name" required>
                <input type="date" name="birth_date" placeholder="Birth Date" required>
                <input type="text" name="contact_number" placeholder="Contact Number" required>
                <input type="text" name="address" placeholder="Address" required>
                <button type="submit" name="add_student">Add Student</button>
            </form>
        </div>

       
        <table border="1">
            <tr>
                <th>IDNumber</th>
                <th>StudentID</th>
                <th>FirstName</th>
                <th>LastName</th>
                <th>BirthDate</th>
                <th>ContactNumber</th>
                <th>Address</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id_number']) ?></td>
                    <td><?= htmlspecialchars($row['student_id']) ?></td>
                    <td><?= htmlspecialchars($row['first_name']) ?></td>
                    <td><?= htmlspecialchars($row['last_name']) ?></td>
                    <td><?= htmlspecialchars($row['birth_date']) ?></td>
                    <td><?= htmlspecialchars($row['contact_number']) ?></td>
                    <td><?= htmlspecialchars($row['address']) ?></td>
                    <td>
                        <button><a href="manage_students.php?edit=<?= $row['id_number'] ?>">Edit</a></button> |
                        <button><a href="manage_students.php?delete=<?= $row['id_number'] ?>" onclick="return confirm('Are you sure you want to delete this student?')">Delete</a><button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

    <?php else: ?>
       
        <h2>Edit Student</h2>

        <div id="edit-form">
           
            <form action="manage_students.php" method="POST">
                <input type="text" name="id_number" value="<?= htmlspecialchars($student['id_number']) ?>" placeholder="ID Number" readonly>
                <input type="text" name="student_id" value="<?= htmlspecialchars($student['student_id']) ?>" placeholder="Student ID" required>
                <input type="text" name="first_name" value="<?= htmlspecialchars($student['first_name']) ?>" placeholder="First Name" required>
                <input type="text" name="last_name" value="<?= htmlspecialchars($student['last_name']) ?>" placeholder="Last Name" required>
                <input type="date" name="birth_date" value="<?= htmlspecialchars($student['birth_date']) ?>" placeholder="Birth Date" required>
                <input type="text" name="contact_number" value="<?= htmlspecialchars($student['contact_number']) ?>" placeholder="Contact Number" required>
                <input type="text" name="address" value="<?= htmlspecialchars($student['address']) ?>" placeholder="Address" required>
                <button type="submit" name="update_student">Update Student</button>
            </form>
        </div>
    <?php endif; ?>

</body>
</html>