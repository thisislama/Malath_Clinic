<?php
// بدء الجلسة إذا لم تكن قد بدأت
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// التحقق من وجود المستخدم في الجلسة
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html"); // إعادة التوجيه إلى الصفحة الرئيسية إذا لم يكن المستخدم مسجل دخول
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
?>

