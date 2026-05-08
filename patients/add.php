<?php
require_once '../config/database.php';
requireAuth();

$pageTitle = 'Add Patient';
$basePath = '../';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $age = intval($_POST['age'] ?? 0);
    $gender = $_POST['gender'] ?? '';
    $disease = trim($_POST['disease'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $address = trim($_POST['address'] ?? '');

    // Validation
    if (empty($name) || empty($gender) || empty($disease) || empty($contact)) {
        $error = "Please fill in all required fields.";
    } elseif ($age <= 0 || $age > 150) {
        $error = "Please enter a valid age.";
    } else {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("
                INSERT INTO patients (name, age, gender, disease, contact, address) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $age, $gender, $disease, $contact, $address]);
            
            setSuccess("Patient added successfully!");
            header("Location: index.php");
            exit();
        } catch (PDOException $e) {
            $error = "Failed to add patient. Please try again.";
        }
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="main-content">
    <div class="page-header">
        <h1><i class="fas fa-user-plus"></i> Add Patient</h1>
        <p class="breadcrumb">Home / Patients / Add</p>
    </div>

    <div class="card" data-aos="fade-up">
        <div class="card-header">
            <h2>Patient Information</h2>
        </div>
        
        <form method="POST" action="" class="form">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="form-row">
                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input type="text" id="name" name="name" required 
                           value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="age">Age *</label>
                    <input type="number" id="age" name="age" min="1" max="150" required
                           value="<?php echo isset($_POST['age']) ? htmlspecialchars($_POST['age']) : ''; ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="gender">Gender *</label>
                    <select id="gender" name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="Male" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Male') ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Female') ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="contact">Contact Number *</label>
                    <input type="tel" id="contact" name="contact" required
                           value="<?php echo isset($_POST['contact']) ? htmlspecialchars($_POST['contact']) : ''; ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="disease">Disease/Condition *</label>
                <input type="text" id="disease" name="disease" required
                       value="<?php echo isset($_POST['disease']) ? htmlspecialchars($_POST['disease']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address" rows="3"><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Patient
                </button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</main>

<?php include '../includes/footer.php'; ?>