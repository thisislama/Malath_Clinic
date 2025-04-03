<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'connection.php';

if (isset($_SESSION['error_message'])) {
    echo "<script>alert('" . $_SESSION['error_message'] . "');</script>";
    unset($_SESSION['error_message']); // حذف الرسالة بعد عرضها
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // تأكيد أن specialityID موجود
    if (isset($_POST['specialityID'])) {
        $specialityID = $_POST['specialityID'];
        echo "Speciality ID: " . $specialityID;  // لاختبار القيمة المستلمة
    }

    $id = mysqli_real_escape_string($connection, $_POST['id']);
    $firstName = mysqli_real_escape_string($connection, $_POST['firstName']);
    $lastName = mysqli_real_escape_string($connection, $_POST['lastName']);
    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $type = $_POST['type'];
    
    // التحقق من أن الـ ID غير مستخدم مسبقًا
    $check_id = "SELECT id FROM Patient WHERE id='$id' UNION SELECT id FROM Doctor WHERE id='$id'";
    $result_id = mysqli_query($connection, $check_id);
    if (mysqli_num_rows($result_id) > 0) {
        $_SESSION['error_message'] = "ID already exists";  // تخزين رسالة الخطأ في الجلسة
        header("Location: signup.php");  // إعادة توجيه إلى نفس الصفحة
        exit();
    }

    // التحقق من عدم وجود البريد الإلكتروني في كلا الجدولين
    $check_email = "SELECT id FROM Patient WHERE emailAddress='$email' UNION SELECT id FROM Doctor WHERE emailAddress='$email'";
    $result_email = mysqli_query($connection, $check_email);
    if (mysqli_num_rows($result_email) > 0) {
        $_SESSION['error_message'] = "Email already exists";  // تخزين رسالة الخطأ في الجلسة
        header("Location: signup.php");  // إعادة توجيه إلى نفس الصفحة
        exit();
    }

    if ($type == "doctor") {
        // التحقق من أن التخصص تم اختياره
        if (empty($specialityID)) {
            $_SESSION['error_message'] = "Please select a speciality.";
            header("Location: signup.php");
            exit();
        }

        // التحقق من رفع صورة الشهادة
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $photo_tmp_name = $_FILES['photo']['tmp_name'];
            $photo_name = $_FILES['photo']['name'];
            $photo_ext = pathinfo($photo_name, PATHINFO_EXTENSION);
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];

            // التحقق من الامتداد المسموح به
            if (in_array(strtolower($photo_ext), $allowed_extensions)) {
                $uniqueFileName = uniqid("doctor_") . "." . $photo_ext;
                $upload_dir = 'uploads/';
                $upload_path = $upload_dir . $uniqueFileName;

                // رفع الملف
                if (move_uploaded_file($photo_tmp_name, $upload_path)) {
                    // إذا تم رفع الملف بنجاح، نكمل عملية التسجيل
                    $insert_doctor = "INSERT INTO Doctor (id, firstName, lastName, uniqueFileName, SpecialityID, emailAddress, password)
                                      VALUES ('$id', '$firstName', '$lastName', '$uniqueFileName', '$specialityID', '$email', '$password')";
                    
                    if (mysqli_query($connection, $insert_doctor)) {
                        $_SESSION['user_id'] = mysqli_insert_id($connection);
                        $_SESSION['user_type'] = "doctor";
                        header("Location: doctor_homepage.php");
                        exit();
                    } else {
                        echo "Error: " . mysqli_error($connection);
                    }
                } else {
                    $_SESSION['error_message'] = "Failed to upload the file.";
                    header("Location: signup.php");
                    exit();
                }
            } else {
                $_SESSION['error_message'] = "Invalid file format. Only JPG, JPEG, PNG, and PDF are allowed.";
                header("Location: signup.php");
                exit();
            }
        } else {
            $_SESSION['error_message'] = "Please upload a file.";
            header("Location: signup.php");
            exit();
        }
    } else {
        // التحقق من مدخلات المريض
        $gender = mysqli_real_escape_string($connection, $_POST['gender']);
        $dob = mysqli_real_escape_string($connection, $_POST['dob']);

        // إضافة المريض إلى قاعدة البيانات
        $insert_patient = "INSERT INTO Patient (id, firstName, lastName, Gender, DoB, emailAddress, password)
                           VALUES ('$id', '$firstName', '$lastName', '$gender', '$dob', '$email', '$password')";

        if (mysqli_query($connection, $insert_patient)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['user_type'] = "patient";
            header("Location: patient_homepage.php");
            exit();
        } else {
            echo "Error: " . mysqli_error($connection);
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up - Malath | مَلاذ</title>
  <link rel="stylesheet" href="style.css">
  	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<!-- Link to the external CSS -->
</head>
<body>
<header class="header-container">
    <!--Logo-->
    <div class="logo">
      <img  src="images/Malath2.png" alt="logo" style="width: 130px; height: auto;">
        <h1>MALATH | مَلاذ <br><span>Clinics</span></h1>
    </div><br>
<nav>
  <ul>
    <li><a href="login.php"><span class="nav-button">Log In</span></a></li>
    <li><a href="index.php"><span class="nav-button">Home</span></a></li>
  </ul>
</nav>
</header>

<main>


  <h1>Create Your Account</h1>
<form id="role-form">
    <h2>Choose Your Role</h2>
    <div class="radio-group">
      <label><input type="radio" name="type" value="patient" onclick="showForm('patient')" required> Patient</label>
      <label><input type="radio" name="type" value="doctor" onclick="showForm('doctor')" required> Doctor</label>
    </div>
</form>

<div class="form-container">
  <!-- Patient Form -->
  <form id="patient-form" action="signup.php" method="POST" style="display:none;">
    <h2>Patient Information</h2>
    <input type="hidden" name="type" value="patient">
    <div class="form-group">
      <label>First Name:</label>
      <input type="text" name="firstName" required>
    </div>
    <div class="form-group">
      <label>Last Name:</label>
      <input type="text" name="lastName" required>
    </div>
        <div class="form-group">
      <label for="patient-id">ID:</label>
      <input type="text" id="patient-id" name="id" required>
    </div>
    <div class="form-group">
      <label>Gender:</label>
      <select name="gender" required>
        <option value="" disabled selected>Select Gender</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
      </select>
    </div>
    <div class="form-group">
      <label>Date of Birth:</label>
      <input type="date" name="dob" required>
    </div>
    <div class="form-group">
      <label>Email Address:</label>
      <input type="email" name="email" required>
    </div>
    <div class="form-group">
      <label>Password:</label>
      <input type="password" name="password" required>
    </div>
    <button type="submit">Sign Up as Patient</button>
  </form>

  <!-- Doctor Form -->
  <form id="doctor-form" action="signup.php" method="POST" enctype="multipart/form-data" style="display:none;">
    <h2>Doctor Information</h2>
    <input type="hidden" name="type" value="doctor">
    <div class="form-group">
      <label>First Name:</label>
      <input type="text" name="firstName" required>
    </div>
    <div class="form-group">
      <label>Last Name:</label>
      <input type="text" name="lastName" required>
          <div class="form-group">
      <label for="patient-id">ID:</label>
      <input type="text" id="patient-id" name="id" required>
    </div>
    </div>
    <div class="form-group">
      <label>Photo:</label>
      <input type="file" name="photo" accept="image/*" required>
    </div>
    <div class="form-group">
    <label for="speciality">Speciality:</label>
    <select name="specialityID" id="speciality">
        <option value="">Select Speciality</option>
        <?php
        // استرجاع التخصصات من قاعدة البيانات
        $speciality_query = "SELECT id, speciality FROM speciality";
        $result = mysqli_query($connection, $speciality_query);
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<option value='" . $row['id'] . "'>" . $row['speciality'] . "</option>";
        }
        ?>
    </select>
    </div>
    <div class="form-group">
      <label>Email Address:</label>
      <input type="email" name="email" required>
    </div>
    <div class="form-group">
      <label>Password:</label>
      <input type="password" name="password" required>
    </div>
    <button type="submit">Sign Up as Doctor</button>
  </form>
</div>

</main>
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

<script>
// JavaScript to show the correct form based on role
// JavaScript to show the correct form based on role
function showForm(role) {
  // Hide both forms initially
  document.getElementById('patient-form').style.display = 'none';
  document.getElementById('doctor-form').style.display = 'none';
  
  // Show the form based on the role selected
  if (role === 'patient') {
    document.getElementById('patient-form').style.display = 'block';
  } else if (role === 'doctor') {
    document.getElementById('doctor-form').style.display = 'block';
  }
}

</script>
</body>
</html> 


