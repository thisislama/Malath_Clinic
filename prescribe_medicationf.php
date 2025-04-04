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

// Connect to the MySQL database
$conn = new mysqli("localhost", "root", "", "malath");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Get the appointment ID from the query string
$appointmentId = $_GET['appointment_id'] ?? null;
if (!$appointmentId) {
    die("No appointment ID provided.");
}

// Retrieve the appointment details and associated patient information
$appSql = "SELECT a.*, p.firstName, p.lastName, p.DoB, p.Gender FROM appointment a JOIN patient p ON a.PatientID = p.id WHERE a.id = $appointmentId";
$appResult = $conn->query($appSql);
$appointment = $appResult->fetch_assoc();

$patientId = $appointment['PatientID'];

// Calculate the patient's age from their date of birth
$age = date_diff(date_create($appointment['DoB']), date_create('today'))->y;

// Retrieve the full list of available medications
$meds = $conn->query("SELECT * FROM medication");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected = $_POST['medications'] ?? []; // Get selected medication IDs from the form

    // Insert each selected medication into the prescription table
    if (!empty($selected)) {
        foreach ($selected as $medId) {
            $conn->query("INSERT INTO prescription (AppointmentID, MedicationID) VALUES ($appointmentId, $medId)");
        }
    }

    // Update the appointment status to 'Done'
    $conn->query("UPDATE appointment SET status = 'Done' WHERE id = $appointmentId");

    // Redirect back to the doctor's homepage after processing
    header("Location: doctor_homepage.php");
    exit;
}
?>

<!--  HTML head and styles  -->
<body>
<header>
  <div class="logo">
    <img src="images/Malath2.png" alt="logo">
    <h1>MALATH | مَلاذ <br><span>Clinics</span></h1>
  </div>
  <button id="logout"><a href="logout.php">Log out</a></button>
  <h3>Welcome, Dr. Waleed</h3>
</header>

<div class="container">
  <div class="title">
    <h1>Patient's Medications</h1>
  </div>

  <!-- Medication prescription form -->
  <form method="POST">
    <!-- Hidden field to keep track of which appointment this prescription is for -->
    <input type="hidden" name="appointment_id" value="<?= $appointmentId ?>">

    <!-- Display patient name -->
    <label>Patient's Name:</label>
    <input type="text" value="<?= htmlspecialchars($appointment['firstName'] . ' ' . $appointment['lastName']) ?>" readonly>

    <!-- Display patient age -->
    <label>Age:</label>
    <input type="text" value="<?= $age ?>" readonly>

    <!-- Display patient gender -->
    <label>Gender:</label>
    <input type="text" value="<?= $appointment['Gender'] ?>" readonly>

    <!-- Medication selection checkboxes -->
    <label>Medications:</label>
    <div class="checkbox-group">
      <?php while ($m = $meds->fetch_assoc()): ?>
        <label>
          <input type="checkbox" name="medications[]" value="<?= $m['id'] ?>">
          <?= $m['MedicationName'] ?>
        </label>
      <?php endwhile; ?>
    </div>

    <!-- Submit button -->
    <button type="submit" class="btn-submit">Submit</button>
  </form>
</div>
</body>
</html>
