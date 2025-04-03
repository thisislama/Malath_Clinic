<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();  // فقط إذا لم تكن قد بدأت
}
include 'connection.php';

// عرض رسالة خطأ إذا كانت موجودة في الـ sesstion
if (isset($_SESSION['error_message'])) {
    echo "<script>alert('" . $_SESSION['error_message'] . "');</script>";
    unset($_SESSION['error_message']); // حذف الرسالة بعد عرضها
}

// التحقق من إرسال النموذج باستخدام POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['role'])) {
        // تأكد من أن المدخلات موجودة في الـ POST
        $email = mysqli_real_escape_string($connection, $_POST['email']);
        $password = $_POST['password'];
        $role = $_POST['role'];

        // استعلام لاختيار المستخدم بناءً على البريد الإلكتروني والدور
        if ($role == "doctor") {
            $query = "SELECT id, password FROM Doctor WHERE emailAddress='$email'";
        } else {
            $query = "SELECT id, password FROM Patient WHERE emailAddress='$email'";
        }

        // تنفيذ الاستعلام
        $result = mysqli_query($connection, $query);

        // التحقق من وجود نتيجة في الاستعلام
        if (mysqli_num_rows($result) > 0) {
            // جلب البيانات من الاستعلام
            $user = mysqli_fetch_assoc($result);

            // تحقق من صحة كلمة المرور
            if (password_verify($password, $user['password'])) {
                // إضافة id و type في الجلسة
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_type'] = $role;

                // إعادة التوجيه إلى الصفحة المناسبة حسب الدور
                if ($role == "doctor") {
                    header("Location: doctor_homepage.php");
                } else {
                    header("Location: patient_homepage.php");
                }
                exit();
            } else {
                // إذا كانت كلمة المرور غير صحيحة
               $_SESSION['error_message'] = "Invalid email address or password";
header("Location: login.php");
exit();
            }
        } else {
            // إذا لم يتم العثور على البريد الإلكتروني
            $_SESSION['error_message'] = "Invalid email address or password";
            header("Location: login.php");
            exit();
        }
    } else {
        // إذا كانت البيانات مفقودة
        $_SESSION['error_message'] = "please fill all the feilds";
header("Location: login.php");
exit();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Log In - Malath | مَلاذ</title>
  <link rel="stylesheet" href="style.css">
  	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

</head>
<body>
<header class="header-container">
    <!--Logo-->
    <div class="logo">
        <img  src="images/Malath2.png" alt="logo" style="width: 130px; height: auto;">
        <h1>MALATH | مَلاذ <br><span>Clinics</span></h1>
    </div>

<nav>
  <ul >
    <li><a href="signup.php"><span class="nav-button">Sign Up</span></a></li>
    <li><a href="index.php"><span class="nav-button">Home</span></a></li>
  </ul>
</nav>
</header>
<br>

<main>

  <h1>Log In</h1>
  <div class="form-container">
      <?php if (isset($error_message)) { ?>
      <div style="color: red;"><?php echo $error_message; ?></div>
    <?php } ?>
    <form action="login.php" method="POST">
  <input type="email" name="email" placeholder="Email Address" required><br>
  <input type="password" name="password" placeholder="Password" required><br>
  <div>
    <label><input type="radio" name="role" value="patient" required> Patient</label>
    <label><input type="radio" name="role" value="doctor"> Doctor</label>
  </div>
  <button type="submit">Log In</button>
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
</body>
</html>

