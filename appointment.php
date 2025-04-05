<?php
include 'security_login.php'; // Ensure this file connects to the database
require "connectDB.php";


// Fetch specialities for dropdown
$specialities = $conn->query("SELECT * FROM Speciality");

// Handle first form submission (Filter doctors by specialty)
$doctors = [];
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["speciality"])) {
    $speciality_id = intval($_POST["speciality"]);
    $doctors = $conn->query("SELECT id, firstName, lastName FROM Doctor WHERE SpecialityID = $speciality_id");
}

// Handle second form submission (Book appointment)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["doctor"])) {
    $doctor_id = intval($_POST["doctor"]);
    $patient_id = 101; // Replace with session or logged-in user ID
    $date = $_POST["date"];
    $time = $_POST["time"];
    $reason = $_POST["reason"];

    $result = $conn->query("SELECT MAX(id) AS max_id FROM Appointment");
    $row = $result->fetch_assoc();
    $new_id = $row['max_id'] + 1; // Increment last ID by 1

    $stmt = $conn->prepare("INSERT INTO Appointment (id, PatientID, DoctorID, date, time, reason, status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
    $stmt->bind_param("iissss", $new_id, $patient_id, $doctor_id, $date, $time, $reason);

    //  $stmt = $conn->prepare("INSERT INTO Appointment (PatientID, DoctorID, date, time, reason, status) VALUES (?, ?, ?, ?, ?, 'Pending')");
    //$stmt->bind_param("iisss", $patient_id, $doctor_id, $date, $time, $reason);

    if ($stmt->execute()) {
        header("Location: Patient.php?msg=Appointment booked successfully!");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
/*$result = $conn->query("SELECT MAX(id) AS max_id FROM Appointment");
$row = $result->fetch_assoc();
$new_id = $row['max_id'] + 1; // Increment last ID by 1

$stmt = $conn->prepare("INSERT INTO Appointment (id, PatientID, DoctorID, date, time, reason, status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
$stmt->bind_param("iissss", $new_id, $patient_id, $doctor_id, $date, $time, $reason);
*/

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Appointment</title>
    <link rel="stylesheet" href="booking.css">
</head>
<body>
<header>
    <div class="logo">
        <img src="images/Malath2.png" alt="logo" style="width: 130px; height: auto;">
        <h1>MALATH | مَلاذ <br><span>Clinics</span></h1>
    </div>
    <button id="logout"><a href="index.html">Log out</a></button>
    <h3>Welcome, Nora</h3>
</header>

<div class="booking">
    <h2>Book an Appointment</h2>

    <!-- First Form: Select Speciality -->
    <form method="POST" action="">
        <div class="input">
            <select name="speciality" required >
                <option value="" disabled selected></option>
                <?php while ($row = $specialities->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>" <?= isset($_POST['speciality']) && $_POST['speciality'] == $row['id'] ? 'selected' : '' ?>>
                        <?= $row['speciality'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <span  class="drsList" >Choose a specialty:</span>
        </div>
        <span><button id="s2" type="submit" onchange="this.form.submit()">Submit</button></span>
        <hr><br>
    </form>

    <!-- Second Form: Select Doctor and Enter Details -->
    <?php if (!empty($doctors)): ?>
        <form id="doc" method="POST" action="">
            <div class="input">
                <select name="doctor" required>
                    <option value="" disabled selected></option>
                    <?php while ($row = $doctors->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>"><?= $row['firstName'] . " " . $row['lastName'] ?></option>
                    <?php endwhile; ?>
                </select>
                <span for="dr" class="drsList">Choose a doctor:</span>
            </div>

            <div class="input">
                <input type="date" name="date" required>
                <span>Choose a date:</span>
            </div>

            <div class="input">
                <input type="time" name="time" required>
                <span>Choose a time:</span>
            </div>

            <div class="input">
                <input type="text" name="reason" required>
                <span>Reason for visit:</span>
            </div>

            <button id="submitButton" type="submit">Submit Booking</button>
            <br> </form>
    <?php endif; ?>
</div>

<footer>
    <img src="images/Malath%20logo.png" alt="logo">
    <p>مرحبًا بكم في مَلاذ، وجهتكم الأمثل للرعاية الصحية الشاملة...</p>
</footer>
</body>
</html>
