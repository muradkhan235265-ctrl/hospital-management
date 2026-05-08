<?php
require_once '../config/database.php';
requireAuth();

$pageTitle = 'Add Doctor';
$basePath = '../';

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
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("
                INSERT INTO doctors (name, specialization, contact, email, experience) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $specialization, $contact, $email, $experience]);
            
            setSuccess("Doctor added successfully!");
            header("Location: index.php");
            exit();
        } catch (PDOException $e) {
            $error = "Failed to add doctor.";
        }
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="main-content">
    <div class="page-header">
        <h1><i class="fas fa-user-plus"></i> Add Doctor</h1>
        <p class="breadcrumb">Home / Doctors / Add</p>
    </div>

    <div class="card" data-aos="fade-up">
        <div class="card-header">
            <h2>Doctor Information</h2>
        </div>
        
        <form method="POST" action="" class="form">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="form-row">
                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="specialization">Specialization *</label>
                    <input type="text" id="specialization" name="specialization" required 
                           placeholder="e.g., Cardiology, Neurology">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="contact">Contact Number *</label>
                    <input type="tel" id="contact" name="contact" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email">
                </div>
            </div>

            <div class="form-group">
                <label for="experience">Years of Experience</label>
                <input type="number" id="experience" name="experience" min="0" value="0">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Doctor
                </button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</main>

<?php include '../includes/footer.php'; ?>