<?php
require 'connect_DB.php';

if (isset($_GET['id'])) {
    $appointment_id = $_GET['id'];

    // Delete the appointment
    $query = "DELETE FROM Appointment WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();

    $stmt->close();
    $conn->close();

    header("Location: patient_homepage.php"); // Redirect to homepage
    exit();
}
?>
