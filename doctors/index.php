<?php
require_once '../config/database.php';
requireAuth();

$pageTitle = 'Doctors';
$basePath = '../';

try {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT * FROM doctors ORDER BY created_at DESC");
    $doctors = $stmt->fetchAll();
} catch (PDOException $e) {
    setError("Failed to load doctors.");
    $doctors = [];
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="main-content">
    <div class="page-header">
        <h1><i class="fas fa-user-md"></i> Doctor Management</h1>
        <p class="breadcrumb">Home / Doctors</p>
    </div>

    <?php showFlashMessages(); ?>

    <div class="card" data-aos="fade-up">
        <div class="card-header">
            <h2><i class="fas fa-list"></i> All Doctors</h2>
            <a href="add.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Doctor
            </a>
        </div>
        
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Specialization</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Experience</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($doctors)): ?>
                        <tr>
                            <td colspan="7" class="text-center">No doctors found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($doctors as $doctor): ?>
                            <tr>
                                <td>#<?php echo $doctor['id']; ?></td>
                                <td><?php echo htmlspecialchars($doctor['name']); ?></td>
                                <td><?php echo htmlspecialchars($doctor['specialization']); ?></td>
                                <td><?php echo htmlspecialchars($doctor['contact']); ?></td>
                                <td><?php echo htmlspecialchars($doctor['email']); ?></td>
                                <td><?php echo $doctor['experience']; ?> years</td>
                                <td class="actions">
                                    <a href="edit.php?id=<?php echo $doctor['id']; ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete.php?id=<?php echo $doctor['id']; ?>" 
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