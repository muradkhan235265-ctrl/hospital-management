<?php
// Get current page for active state
$currentPage = basename($_SERVER['PHP_SELF']);
$currentDir = basename(dirname($_SERVER['PHP_SELF']));
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <i class="fas fa-hospital-alt"></i>
            <span>MediCare</span>
        </div>
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <nav class="sidebar-nav">
        <ul>
            <li class="<?php echo $currentDir === 'dashboard' ? 'active' : ''; ?>">
                <a href="<?php echo $basePath; ?>dashboard/index.php">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="<?php echo $currentDir === 'patients' ? 'active' : ''; ?>">
                <a href="<?php echo $basePath; ?>patients/index.php">
                    <i class="fas fa-procedures"></i>
                    <span>Patients</span>
                </a>
            </li>
            <li class="<?php echo $currentDir === 'doctors' ? 'active' : ''; ?>">
                <a href="<?php echo $basePath; ?>doctors/index.php">
                    <i class="fas fa-user-md"></i>
                    <span>Doctors</span>
                </a>
            </li>
            <li class="<?php echo $currentDir === 'appointments' ? 'active' : ''; ?>">
                <a href="<?php echo $basePath; ?>appointments/index.php">
                    <i class="fas fa-calendar-check"></i>
                    <span>Appointments</span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="sidebar-footer">
        <div class="user-info">
            <i class="fas fa-user-circle"></i>
            <div>
                <p class="user-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
                <p class="user-role"><?php echo ucfirst($_SESSION['user_role']); ?></p>
            </div>
        </div>
        <a href="<?php echo $basePath; ?>auth/logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>