<?php
require_once '../config/database.php';
requireAuth();

$pageTitle = 'Edit Patient';
$basePath = '../';

// Get patient ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$id) {
    setError("Invalid patient ID.");
    header("Location: index.php");
    exit();
}

try {
    $pdo = getDBConnection();
    
    // Fetch patient
    $stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
    $stmt->execute([$id]);
    $patient = $stmt->fetch();
    
    if (!$patient) {
        setError("Patient not found.");
        header("Location: index.php");
        exit();
    }
    
} catch (PDOException $e) {
    setError("Failed to load patient.");
    header("Location: index.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $age = intval($_POST['age'] ?? 0);
    $gender = $_POST['gender'] ?? '';
    $disease = trim($_POST['disease'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if (empty($name) || empty($gender) || empty($disease) || empty($contact)) {
        $error = "Please fill in all required fields.";
    } elseif ($age <= 0 || $age > 150) {
        $error = "Please enter a valid age.";
    } else {
        try {
            $stmt = $pdo->prepare("
                UPDATE patients 
                SET name = ?, age = ?, gender = ?, disease = ?, contact = ?, address = ?
                WHERE id = ?
            ");
            $stmt->execute([$name, $age, $gender, $disease, $contact, $address, $id]);
            
            setSuccess("Patient updated successfully!");
            header("Location: index.php");
            exit();
        } catch (PDOException $e) {
            $error = "Failed to update patient.";
        }
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="main-content">
    <div class="page-header">
        <h1><i class="fas fa-edit"></i> Edit Patient</h1>
        <p class="breadcrumb">Home / Patients / Edit</p>
    </div>

    <div class="card" data-aos="fade-up">
        <div class="card-header">
            <h2>Edit Patient Information</h2>
        </div>
        
        <form method="POST" action="" class="form">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="form-row">
                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input type="text" id="name" name="name" required 
                           value="<?php echo htmlspecialchars($patient['name']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="age">Age *</label>
                    <input type="number" id="age" name="age" min="1" max="150" required
                           value="<?php echo $patient['age']; ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="gender">Gender *</label>
                    <select id="gender" name="gender" required>
                        <option value="Male" <?php echo $patient['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo $patient['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo $patient['gender'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="contact">Contact Number *</label>
                    <input type="tel" id="contact" name="contact" required
                           value="<?php echo htmlspecialchars($patient['contact']); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="disease">Disease/Condition *</label>
                <input type="text" id="disease" name="disease" required
                       value="<?php echo htmlspecialchars($patient['disease']); ?>">
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address" rows="3"><?php echo htmlspecialchars($patient['address']); ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Patient
                </button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</main>

<?php include '../includes/footer.php'; ?>