<?php
require_once '../config/database.php';
requireAuth();

$pageTitle = 'Book Appointment';
$basePath = '../';

try {
    $pdo = getDBConnection();
    
    // Fetch only doctors for dropdown (patients don't select themselves)
    $doctors = $pdo->query("SELECT id, name, specialization FROM doctors ORDER BY name")->fetchAll();
    
    // Get current logged-in user's info from users table
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $currentUser = $stmt->fetch();
    
    // Check if this user already has a patient record, if not create one
    $stmt = $pdo->prepare("SELECT * FROM patients WHERE name = ?");
    $stmt->execute([$currentUser['name']]);
    $patient = $stmt->fetch();
    
    // If no patient record exists, auto-create one
    if (!$patient) {
        $stmt = $pdo->prepare("
            INSERT INTO patients (name, age, gender, disease, contact) 
            VALUES (?, 25, 'Other', 'General Checkup', 'Not Provided')
        ");
        $stmt->execute([$currentUser['name']]);
        
        // Get the newly created patient ID
        $patientId = $pdo->lastInsertId();
    } else {
        $patientId = $patient['id'];
    }
    
} catch (PDOException $e) {
    setError("Failed to load data.");
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doctor_id = intval($_POST['doctor_id'] ?? 0);
    $appointment_date = $_POST['appointment_date'] ?? '';
    $appointment_time = $_POST['appointment_time'] ?? '';
    $notes = trim($_POST['notes'] ?? '');

    if (empty($doctor_id) || empty($appointment_date) || empty($appointment_time)) {
        $error = "Please fill in all required fields.";
    } elseif (strtotime($appointment_date) < strtotime(date('Y-m-d'))) {
        $error = "Appointment date cannot be in the past.";
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, notes) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$patientId, $doctor_id, $appointment_date, $appointment_time, $notes]);
            
            setSuccess("Appointment booked successfully!");
            header("Location: index.php");
            exit();
        } catch (PDOException $e) {
            $error = "Failed to book appointment.";
        }
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="main-content">
    <div class="page-header">
        <h1><i class="fas fa-calendar-plus"></i> Book Appointment</h1>
        <p class="breadcrumb">Home / Appointments / Book</p>
    </div>

    <div class="card" data-aos="fade-up">
        <div class="card-header">
            <h2>Appointment Details</h2>
        </div>
        
        <form method="POST" action="" class="form">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Patient Name (Read-only - shows logged in user) -->
            <div class="form-group">
                <label><i class="fas fa-user"></i> Patient Name</label>
                <input type="text" value="<?php echo htmlspecialchars($currentUser['name']); ?>" disabled class="readonly-input">
                <input type="hidden" name="patient_id" value="<?php echo $patientId; ?>">
                <small style="color: var(--text-secondary);">Booking as: <?php echo htmlspecialchars($currentUser['email']); ?></small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="doctor_id">Select Doctor *</label>
                    <select id="doctor_id" name="doctor_id" required>
                        <option value="">Choose Doctor</option>
                        <?php foreach ($doctors as $doctor): ?>
                            <option value="<?php echo $doctor['id']; ?>"
                                <?php echo (isset($_POST['doctor_id']) && $_POST['doctor_id'] == $doctor['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($doctor['name'] . ' - ' . $doctor['specialization']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="appointment_date">Appointment Date *</label>
                    <input type="date" id="appointment_date" name="appointment_date" required
                           min="<?php echo date('Y-m-d'); ?>"
                           value="<?php echo isset($_POST['appointment_date']) ? $_POST['appointment_date'] : ''; ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="appointment_time">Appointment Time *</label>
                <input type="time" id="appointment_time" name="appointment_time" required
                       value="<?php echo isset($_POST['appointment_time']) ? $_POST['appointment_time'] : ''; ?>">
            </div>

            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea id="notes" name="notes" rows="3" placeholder="Additional notes..."><?php echo isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : ''; ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-calendar-check"></i> Book Appointment
                </button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</main>

<?php include '../includes/footer.php'; ?>