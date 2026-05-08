<?php
require_once '../config/database.php';
requireAuth();

$pageTitle = 'Dashboard';
$basePath = '../';

try {
    $pdo = getDBConnection();
    
    // Get counts for dashboard cards
    $patientsCount = $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();
    $doctorsCount = $pdo->query("SELECT COUNT(*) FROM doctors")->fetchColumn();
    $appointmentsCount = $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
    $todayAppointments = $pdo->query("SELECT COUNT(*) FROM appointments WHERE appointment_date = CURDATE()")->fetchColumn();
    
    // Get recent appointments
    $recentAppointments = $pdo->query("
        SELECT a.*, p.name as patient_name, d.name as doctor_name 
        FROM appointments a
        JOIN patients p ON a.patient_id = p.id
        JOIN doctors d ON a.doctor_id = d.id
        ORDER BY a.appointment_date DESC, a.appointment_time DESC
        LIMIT 5
    ")->fetchAll();
    
} catch (PDOException $e) {
    setError("Failed to load dashboard data.");
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="main-content">
    <div class="page-header">
        <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
        <p class="breadcrumb">Home / Dashboard</p>
    </div>

    <?php showFlashMessages(); ?>

    <!-- Dashboard Cards -->
    <div class="dashboard-cards">
        <div class="card stat-card" data-aos="fade-up" data-aos-delay="0">
            <div class="card-icon bg-primary">
                <i class="fas fa-procedures"></i>
            </div>
            <div class="card-info">
                <h3><?php echo $patientsCount; ?></h3>
                <p>Total Patients</p>
            </div>
        </div>

        <div class="card stat-card" data-aos="fade-up" data-aos-delay="100">
            <div class="card-icon bg-success">
                <i class="fas fa-user-md"></i>
            </div>
            <div class="card-info">
                <h3><?php echo $doctorsCount; ?></h3>
                <p>Total Doctors</p>
            </div>
        </div>

        <div class="card stat-card" data-aos="fade-up" data-aos-delay="200">
            <div class="card-icon bg-warning">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="card-info">
                <h3><?php echo $appointmentsCount; ?></h3>
                <p>Total Appointments</p>
            </div>
        </div>

        <div class="card stat-card" data-aos="fade-up" data-aos-delay="300">
            <div class="card-icon bg-info">
                <i class="fas fa-clock"></i>
            </div>
            <div class="card-info">
                <h3><?php echo $todayAppointments; ?></h3>
                <p>Today's Appointments</p>
            </div>
        </div>
    </div>

    <!-- Recent Appointments -->
    <div class="card" data-aos="fade-up">
        <div class="card-header">
            <h2><i class="fas fa-history"></i> Recent Appointments</h2>
            <a href="../appointments/index.php" class="btn btn-sm btn-primary">View All</a>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentAppointments)): ?>
                        <tr>
                            <td colspan="5" class="text-center">No appointments found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentAppointments as $appointment): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['doctor_name']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($appointment['appointment_date'])); ?></td>
                                <td><?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo strtolower($appointment['status']); ?>">
                                        <?php echo $appointment['status']; ?>
                                    </span>
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