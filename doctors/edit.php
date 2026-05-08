<?php
require_once '../config/database.php';
requireAuth();

$pageTitle = 'Edit Doctor';
$basePath = '../';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$id) {
    setError("Invalid doctor ID.");
    header("Location: index.php");
    exit();
}

try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM doctors WHERE id = ?");
    $stmt->execute([$id]);
    $doctor = $stmt->fetch();
    
    if (!$doctor) {
        setError("Doctor not found.");
        header("Location: index.php");
        exit();
    }
} catch (PDOException $e) {
    setError("Failed to load doctor.");
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $specialization = trim($_POST['specialization'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $experience = intval($_POST['experience'] ?? 0);

    if (empty($name) || empty($specialization) || empty($contact)) {
        $error = "Please fill in all required fields.";
    } else {
        try {
            $stmt = $pdo->prepare("
                UPDATE doctors 
                SET name = ?, specialization = ?, contact = ?, email = ?, experience = ?
                WHERE id = ?
            ");
            $stmt->execute([$name, $specialization, $contact, $email, $experience, $id]);
            
            setSuccess("Doctor updated successfully!");
            header("Location: index.php");
            exit();
        } catch (PDOException $e) {
            $error = "Failed to update doctor.";
        }
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="main-content">
    <div class="page-header">
        <h1><i class="fas fa-edit"></i> Edit Doctor</h1>
        <p class="breadcrumb">Home / Doctors / Edit</p>
    </div>

    <div class="card" data-aos="fade-up">
        <div class="card-header">
            <h2>Edit Doctor Information</h2>
        </div>
        
        <form method="POST" action="" class="form">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="form-row">
                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input type="text" id="name" name="name" required 
                           value="<?php echo htmlspecialchars($doctor['name']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="specialization">Specialization *</label>
                    <input type="text" id="specialization" name="specialization" required 
                           value="<?php echo htmlspecialchars($doctor['specialization']); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="contact">Contact Number *</label>
                    <input type="tel" id="contact" name="contact" required
                           value="<?php echo htmlspecialchars($doctor['contact']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email"
                           value="<?php echo htmlspecialchars($doctor['email']); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="experience">Years of Experience</label>
                <input type="number" id="experience" name="experience" min="0"
                       value="<?php echo $doctor['experience']; ?>">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Doctor
                </button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</main>

<?php include '../includes/footer.php'; ?>