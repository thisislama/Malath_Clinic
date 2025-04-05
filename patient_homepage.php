<?php
include 'security_login.php'; // Ensure this file connects to the database
include 'connectDB.php'; // Ensure this file connects to the database

$patient_id = $_SESSION[]; // Temporary: Replace with session variable when login is implemented

// Fetch patient details
$query = "SELECT firstName, lastName,Gender,DoB, emailAddress FROM Patient WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

if (!$patient) {
    die("Patient not found.");
}

// Fetch patient appointments
$query = "SELECT A.id, A.date, A.time, A.status, 
                 D.firstName AS doctorFirstName, D.lastName AS doctorLastName, D.uniqueFileName 
          FROM Appointment A
          JOIN Doctor D ON A.DoctorID = D.id
          WHERE A.PatientID = ?
          ORDER BY A.date, A.time";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$appointments = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>


    <link rel="stylesheet" href="patient.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


    <style>


        #bookAppo a {
            font-family: 'Amiri', serif;
            font-size: 18px;
            font-weight: bold;

        }


    </style>
</head>
<body>



<!--Header-->
<header>
    <!--Logo-->
    <div class="logo">
        <img  src="images/Malath2.png" alt="logo" style="width: 130px; height: auto;">
        <h1>MALATH | مَلاذ <br><span>Clinics</span></h1>
    </div>
    <!--Buttons Links-->
    <button id="logout" ><a href="index.php">Log out</a></button>


    <h3 > Welcome, <?= htmlspecialchars($patient['firstName']) ?> </h3>
</header>


<div class="main-content">
    <!--main-content-->


    <div style=" width: 47vh;" class="PatientInfo">
        <ul>
            <li>  <h4> Name:  <?= htmlspecialchars($patient['firstName']) . " " . htmlspecialchars($patient['lastName']) ?> </h4> </li>
            <li>  <h4> ID:  <?= $patient_id ?> </h4> </li>
            <li>  <h4> Gender:  <?= htmlspecialchars($patient['Gender']) ?> </h4> </li>
            <li>  <h4> Date Of Birth:  <?= htmlspecialchars($patient['DoB']) ?> </h4> </li>
            <li>  <h4> Email: <?= htmlspecialchars($patient['emailAddress']) ?> </h4> </li>
        </ul>
    </div>


    <!--<h3>Your Appointments</h3>-->
    <div style="height:fit-content;" class="appointmentList">
<!--appointment.php-->
        <button style="width: fit-content;" id="bookAppo"><a style=" width: 250px;" href="Booking2.php">Book an Appointment
                <b style="font-size: 24px;">&nbsp;&nbsp;+</b></a></button>




        <table id="appointmentTable">
            <thead>
            <tr style="text-align: center;">
                <th style="border-top-left-radius: 12px ;">Date</th>
                <th>Time</th>
                <th>Doctor</th>
                <th>Doctor's Photo</th>
                <th>Status</th>
                <th style="border-top-right-radius: 12px ;">Cancel</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $appointments->fetch_assoc()): ?>
                <tr>
                    <td style="border-bottom-left-radius: 12px ;"><?= htmlspecialchars($row['date']) ?></td>
                    <td style="width: 97px;"><?= htmlspecialchars($row['time']) ?></td>
                    <td style="width: 109px;">Dr. <?= htmlspecialchars($row['doctorFirstName']) . " " . htmlspecialchars($row['doctorLastName']) ?></td>
                    <td><img  style="border-radius: 8px" src="uploads/<?= htmlspecialchars($row['uniqueFileName']) ?>" alt="Doctor's Photo"></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td style="border-radius: 10px; border: 1px groove #d5d5d5; background: #f2f2f2 ;"><a href="cancel_appointment.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure?');">Cancel </a></td>
                </tr>
            <?php endwhile; ?>
            </tbody>

        </table>
    </div>
    <!--Appointment Table ends-->
</div>


<footer>
    <img src="images/Malath%20logo.png" alt="logo" >
    <!-- <h3> Malath | مَلاذ </h3>-->
    <p>مرحبًا بكم في مَلاذ، وجهتكم الأمثل للرعاية الصحية الشاملة.
        نسعى لتقديم خدمات طبية موثوقة ومتميزة في بيئة مريحة وآمنة.
        رؤيتنا هي تحسين جودة <br>الحياة من خلال الرعاية الصحية المتكاملة،
        ونهتم باحتياجاتكم الصحية بكل احترافية واهتمام.
        تواصلوا معنا للحصول على استشاراتكم الطبية وخدماتنا المتنوعة.
    </p>

    <div class="social">
        <a href="#"><i class="fa fa-facebook"></i></a>
        <a href="#"><i class="fa fa-twitter"></i></a>
        <a href="#"><i class="fa fa-google"></i></a>
        <a href="#"><i class="fa fa-youtube"></i></a>
    </div>
    <p> حقوق النشر © 2025 مَلاذ. </p>
</footer>




</body>
</html>

<?php $stmt->close(); $conn->close(); ?>
