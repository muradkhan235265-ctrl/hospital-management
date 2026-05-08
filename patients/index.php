<?php
require_once '../config/database.php';
requireAuth();

$pageTitle = 'Patients';
$basePath = '../';

try {
    $pdo = getDBConnection();
    
    // Fetch all patients
    $stmt = $pdo->query("SELECT * FROM patients ORDER BY created_at DESC");
    $patients = $stmt->fetchAll();
    
} catch (PDOException $e) {
    setError("Failed to load patients.");
    $patients = [];
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="main-content">
    <div class="page-header">
        <h1><i class="fas fa-procedures"></i> Patient Management</h1>
        <p class="breadcrumb">Home / Patients</p>
    </div>

    <?php showFlashMessages(); ?>

    <div class="card" data-aos="fade-up">
        <div class="card-header">
            <h2><i class="fas fa-list"></i> All Patients</h2>
            <a href="add.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Patient
            </a>
        </div>
        
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Disease</th>
                        <th>Contact</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($patients)): ?>
                        <tr>
                            <td colspan="7" class="text-center">No patients found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($patients as $patient): ?>
                            <tr>
                                <td>#<?php echo $patient['id']; ?></td>
                                <td><?php echo htmlspecialchars($patient['name']); ?></td>
                                <td><?php echo $patient['age']; ?></td>
                                <td><?php echo $patient['gender']; ?></td>
                                <td><?php echo htmlspecialchars($patient['disease']); ?></td>
                                <td><?php echo htmlspecialchars($patient['contact']); ?></td>
                                <td class="actions">
                                    <a href="edit.php?id=<?php echo $patient['id']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete.php?id=<?php echo $patient['id']; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Are you sure you want to delete this patient?')"
                                       title="Delete">
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