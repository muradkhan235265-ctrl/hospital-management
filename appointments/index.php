<?php
require_once '../config/database.php';
requireAuth();

$pageTitle = 'Appointments';
$basePath = '../';

try {
    $pdo = getDBConnection();
    $stmt = $pdo->query("
        SELECT a.*, p.name as patient_name, d.name as doctor_name 
        FROM appointments a
        JOIN patients p ON a.patient_id = p.id
        JOIN doctors d ON a.doctor_id = d.id
        ORDER BY a.appointment_date DESC, a.appointment_time DESC
    ");
    $appointments = $stmt->fetchAll();
} catch (PDOException $e) {
    setError("Failed to load appointments.");
    $appointments = [];
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="main-content">
    <div class="page-header">
        <h1><i class="fas fa-calendar-check"></i> Appointment Management</h1>
        <p class="breadcrumb">Home / Appointments</p>
    </div>

    <?php showFlashMessages(); ?>

    <div class="card" data-aos="fade-up">
        <div class="card-header">
            <h2><i class="fas fa-list"></i> All Appointments</h2>
            <a href="add.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Book Appointment
            </a>
        </div>
        
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($appointments)): ?>
                        <tr>
                            <td colspan="7" class="text-center">No appointments found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($appointments as $appointment): ?>
                            <tr>
                                <td>#<?php echo $appointment['id']; ?></td>
                                <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['doctor_name']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($appointment['appointment_date'])); ?></td>
                                <td><?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo strtolower($appointment['status']); ?>">
                                        <?php echo $appointment['status']; ?>
                                    </span>
                                </td>
                                <td class="actions">
                                    <a href="edit.php?id=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete.php?id=<?php echo $appointment['id']; ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>