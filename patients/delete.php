<?php
require_once '../config/database.php';
requireAuth();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("DELETE FROM patients WHERE id = ?");
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() > 0) {
            setSuccess("Patient deleted successfully!");
        } else {
            setError("Patient not found.");
        }
    } catch (PDOException $e) {
        setError("Failed to delete patient.");
    }
} else {
    setError("Invalid patient ID.");
}

header("Location: index.php");
exit();
?>