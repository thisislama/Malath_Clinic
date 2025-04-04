<?php
// بدء الجلسة إذا لم تكن قد بدأت
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// التحقق من وجود المستخدم في الجلسة
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php"); // إعادة التوجيه إلى الصفحة الرئيسية إذا لم يكن المستخدم مسجل دخول
    exit();
}

// التحقق من نوع المستخدم
$current_page = basename($_SERVER['PHP_SELF']); // الحصول على اسم الملف الحالي بدون مسار

// التحقق من صفحة الطبيب
if ($current_page == 'doctor_homepage.php' && $_SESSION['user_type'] != 'doctor') {
    header("Location: patient_homepage.php"); // إعادة التوجيه إلى صفحة المريض إذا كان المستخدم ليس طبيبًا
    exit();
}

// التحقق من صفحة المريض
if ($current_page == 'patient_homepage.php' && $_SESSION['user_type'] != 'patient') {
    header("Location: doctor_homepage.php"); // إعادة التوجيه إلى صفحة الطبيب إذا كان المستخدم ليس مريضًا
    exit();
}

// Simulate doctor login if session is not already set (for demo/testing purposes)
if (!isset($_SESSION['doctor_id'])) {
    $_SESSION['doctor_id'] = 301; // Simulated login for Dr. John Doe
}

// Connect to MySQL database
$conn = new mysqli("localhost", "root", "", "malath");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$doctorId = $_SESSION['doctor_id'];

// Fetch doctor's information including their speciality
$docQuery = "SELECT d.*, s.speciality FROM doctor d JOIN speciality s ON d.SpecialityID = s.id WHERE d.id = $doctorId";
$doctor = $conn->query($docQuery)->fetch_assoc();

// Fetch upcoming appointments for the doctor where status is 'Pending' or 'Confirmed'
$appsQuery = "SELECT a.*, p.firstName AS pFirst, p.lastName AS pLast, p.Gender, p.DoB
              FROM appointment a JOIN patient p ON a.PatientID = p.id
              WHERE a.DoctorID = $doctorId AND (a.status = 'Pending' OR a.status = 'Confirmed')
              ORDER BY a.date, a.time";
$appointments = $conn->query($appsQuery);

// Fetch patients who had at least one 'Done' appointment with this doctor
$patientsQuery = "SELECT DISTINCT p.*, TIMESTAMPDIFF(YEAR, p.DoB, CURDATE()) AS age
                  FROM appointment a JOIN patient p ON a.PatientID = p.id
                  WHERE a.DoctorID = $doctorId AND a.status = 'Done'
                  ORDER BY p.firstName";
$patients = $conn->query($patientsQuery);

// Helper function to get all medications prescribed for a given appointment
function getMedications($conn, $appointmentId) {
    $sql = "SELECT m.MedicationName FROM prescription pr JOIN medication m ON pr.MedicationID = m.id WHERE pr.AppointmentID = $appointmentId";
    $result = $conn->query($sql);
    $meds = [];
    while ($row = $result->fetch_assoc()) {
        $meds[] = $row['MedicationName'];
    }
    return implode(", ", $meds);
}
?>

<!-- HTML head and styles -->

<body>
<header>
  <div class="logo">
    <img src="images/Malath2.png" alt="logo">
    <h1>MALATH | مَلاذ <br><span>Clinics</span></h1>
  </div>
  <button id="logout"><a href="logout.php">Log out</a></button>
  <h3>Welcome, Dr. <?= htmlspecialchars($doctor['firstName']) ?></h3>
</header>

<div class="container">
  <div class="doctor-info">
    <h2>Doctor's Information</h2>
    <p><strong>First Name:</strong> <?= $doctor['firstName'] ?></p>
    <p><strong>Last Name:</strong> <?= $doctor['lastName'] ?></p>
    <p><strong>Speciality:</strong> <?= $doctor['speciality'] ?></p>
    <p><strong>Email:</strong> <?= $doctor['emailAddress'] ?></p>
  </div>

  <h2>Upcoming Appointments</h2>
  <table>
    <tr><th>Date</th><th>Time</th><th>Patient</th><th>Age</th><th>Gender</th><th>Reason</th><th>Status</th></tr>
    <?php while ($row = $appointments->fetch_assoc()):
        $age = date_diff(date_create($row['DoB']), date_create('today'))->y; // Calculate patient's age
        $fullName = $row['pFirst'] . " " . $row['pLast']; // Concatenate first and last name
    ?>
      <tr>
        <td><?= $row['date'] ?></td>
        <td><?= $row['time'] ?></td>
        <td><?= htmlspecialchars($fullName) ?></td>
        <td><?= $age ?></td>
        <td><?= $row['Gender'] ?></td>
        <td><?= htmlspecialchars($row['reason']) ?></td>
        <td>
          <?php if ($row['status'] == 'Pending'): ?>
            <!-- Link to confirm the appointment -->
            <a href="confirm_appointment.php?id=<?= $row['id'] ?>" class="btn-confirm">Confirm</a>
          <?php elseif ($row['status'] == 'Confirmed'): ?>
            <!-- Link to prescribe medication for confirmed appointment -->
            <a href="prescribe_medication.php?appointment_id=<?= $row['id'] ?>" class="btn-confirm">Prescribe</a>
          <?php else: ?>
            <?= $row['status'] ?>
          <?php endif; ?>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>

  <h2>Patients List</h2>
  <table>
    <tr><th>Name</th><th>Age</th><th>Gender</th><th>Medications</th></tr>
    <?php while ($p = $patients->fetch_assoc()): 
        $fullName = $p['firstName'] . " " . $p['lastName'];

        // Fetch all 'Done' appointment IDs for the current patient
        $medsQuery = "SELECT DISTINCT a.id FROM appointment a WHERE a.status='Done' AND a.DoctorID=$doctorId AND a.PatientID=" . $p['id'];
        $apptRes = $conn->query($medsQuery);

        // Collect medications prescribed across all those appointments
        $meds = [];
        while ($a = $apptRes->fetch_assoc()) {
            $meds[] = getMedications($conn, $a['id']);
        }
    ?>
      <tr>
        <td><?= htmlspecialchars($fullName) ?></td>
        <td><?= $p['age'] ?></td>
        <td><?= $p['Gender'] ?></td>
        <td><?= implode(", ", array_filter($meds)) ?></td>
      </tr>
    <?php endwhile; ?>
  </table>
</div>
</body>
</html>
