<?php
require_once '../config/database.php';
requireAuth();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("DELETE FROM doctors WHERE id = ?");
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() > 0) {
            setSuccess("Doctor deleted successfully!");
        } else {
            setError("Doctor not found.");
        }
    } catch (PDOException $e) {
        setError("Failed to delete doctor.");
    }
} else {
    setError("Invalid doctor ID.");
}

header("Location: index.php");
exit();
?>