<?php
require_once '../config/database.php';
requireAuth();

$pageTitle = 'Edit Appointment';
$basePath = '../';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$id) {
    setError("Invalid appointment ID.");
    header("Location: index.php");
    exit();
}

try {
    $pdo = getDBConnection();
    
    // Fetch appointment
    $stmt = $pdo->prepare("SELECT * FROM appointments WHERE id = ?");
    $stmt->execute([$id]);
    $appointment = $stmt->fetch();
    
    if (!$appointment) {
        setError("Appointment not found.");
        header("Location: index.php");
        exit();
    }
    
    // Fetch patients and doctors
    $patients = $pdo->query("SELECT id, name FROM patients ORDER BY name")->fetchAll();
    $doctors = $pdo->query("SELECT id, name, specialization FROM doctors ORDER BY name")->fetchAll();
    
} catch (PDOException $e) {
    setError("Failed to load data.");
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = intval($_POST['patient_id'] ?? 0);
    $doctor_id = intval($_POST['doctor_id'] ?? 0);
    $appointment_date = $_POST['appointment_date'] ?? '';
    $appointment_time = $_POST['appointment_time'] ?? '';
    $status = $_POST['status'] ?? 'Scheduled';
    $notes = trim($_POST['notes'] ?? '');

    if (empty($patient_id) || empty($doctor_id) || empty($appointment_date) || empty($appointment_time)) {
        $error = "Please fill in all required fields.";
    } else {
        try {
            $stmt = $pdo->prepare("
                UPDATE appointments 
                SET patient_id = ?, doctor_id = ?, appointment_date = ?, 
                    appointment_time = ?, status = ?, notes = ?
                WHERE id = ?
            ");
            $stmt->execute([$patient_id, $doctor_id, $appointment_date, $appointment_time, $status, $notes, $id]);
            
            setSuccess("Appointment updated successfully!");
            header("Location: index.php");
            exit();
        } catch (PDOException $e) {
            $error = "Failed to update appointment.";
        }
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="main-content">
    <div class="page-header">
        <h1><i class="fas fa-edit"></i> Edit Appointment</h1>
        <p class="breadcrumb">Home / Appointments / Edit</p>
    </div>

    <div class="card" data-aos="fade-up">
        <div class="card-header">
            <h2>Edit Appointment Details</h2>
        </div>
        
        <form method="POST" action="" class="form">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="form-row">
                <div class="form-group">
                    <label for="patient_id">Select Patient *</label>
                    <select id="patient_id" name="patient_id" required>
                        <?php foreach ($patients as $patient): ?>
                            <option value="<?php echo $patient['id']; ?>" 
                                <?php echo $appointment['patient_id'] == $patient['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($patient['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="doctor_id">Select Doctor *</label>
                    <select id="doctor_id" name="doctor_id" required>
                        <?php foreach ($doctors as $doctor): ?>
                            <option value="<?php echo $doctor['id']; ?>"
                                <?php echo $appointment['doctor_id'] == $doctor['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($doctor['name'] . ' - ' . $doctor['specialization']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="appointment_date">Appointment Date *</label>
                    <input type="date" id="appointment_date" name="appointment_date" required
                           value="<?php echo $appointment['appointment_date']; ?>">
                </div>
                
                <div class="form-group">
                    <label for="appointment_time">Appointment Time *</label>
                    <input type="time" id="appointment_time" name="appointment_time" required
                           value="<?php echo $appointment['appointment_time']; ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="Scheduled" <?php echo $appointment['status'] === 'Scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                    <option value="Completed" <?php echo $appointment['status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="Cancelled" <?php echo $appointment['status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>

            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea id="notes" name="notes" rows="3"><?php echo htmlspecialchars($appointment['notes']); ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Appointment
                </button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</main>

<?php include '../includes/footer.php'; ?>