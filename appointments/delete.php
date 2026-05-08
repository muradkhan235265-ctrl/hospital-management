<?php
require_once '../config/database.php';
requireAuth();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() > 0) {
            setSuccess("Appointment cancelled successfully!");
        } else {
            setError("Appointment not found.");
        }
    } catch (PDOException $e) {
        setError("Failed to cancel appointment.");
    }
} else {
    setError("Invalid appointment ID.");
}

header("Location: index.php");
exit();
?>