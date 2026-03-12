<?php
/**
 * Sidebar Component for HMS Dashboards
 * Adapts based on SESSION role
 */
$role = $_SESSION['role'] ?? 'patient';
$full_name = $_SESSION['full_name'] ?? 'User';
?>

<div class="sidebar col-lg-2 d-none d-lg-block bg-white border-end min-vh-100 p-0 shadow-sm fixed-start sticky-top">
    <div class="sidebar-brand p-4 text-center border-bottom">
        <h5 class="navbar-brand text-primary m-0 fw-bold">
            <i class="fas fa-heartbeat me-2"></i> Kamirex
        </h5>
    </div>

    <!-- User Profile Quick View -->
    <div class="text-center p-4">
        <img src="<?php echo APP_URL; ?>/assets/images/default_user.jpg" class="rounded-circle shadow-sm border border-2 border-primary p-1 mb-3" width="70" height="70">
        <h6 class="mb-0 fw-bold"><?php echo $full_name; ?></h6>
        <span class="badge bg-soft-blue text-primary mt-1 px-2 border border-primary small"><?php echo ucfirst($role); ?></span>
    </div>

    <div class="nav flex-column sidebar-nav p-3">
        <!-- Generic Home -->
        <?php if(in_array($role, ['admin', 'doctor', 'patient'])): ?>
            <a href="dashboard.php" class="nav-link py-3 rounded-4 mb-2 active"><i class="fas fa-th-large me-3"></i> Overview</a>
        <?php endif; ?>

        <!-- ROLE-BASED LINKS -->
        <?php if($role == 'patient'): ?>
            <a href="appointments.php" class="nav-link py-3 rounded-4 mb-2 text-secondary"><i class="fas fa-calendar-check me-3"></i> Book Appointment</a>
            <a href="medical-records.php" class="nav-link py-3 rounded-4 mb-2 text-secondary"><i class="fas fa-history me-3"></i> Medical History</a>
            <a href="billing.php" class="nav-link py-3 rounded-4 mb-2 text-secondary"><i class="fas fa-file-invoice-dollar me-3"></i> Billing & Payments</a>
            <a href="telemedicine.php" class="nav-link py-3 rounded-4 mb-2 text-secondary"><i class="fas fa-video me-3"></i> Tele-Consultation</a>
        <?php endif; ?>

        <?php if($role == 'doctor'): ?>
            <a href="appointments.php" class="nav-link py-3 rounded-4 mb-2 text-secondary"><i class="fas fa-calendar-alt me-3"></i> Appointments</a>
            <a href="patients-list.php" class="nav-link py-3 rounded-4 mb-2 text-secondary"><i class="fas fa-user-injured me-3"></i> My Patients (EMR)</a>
            <a href="tele-sessions.php" class="nav-link py-3 rounded-4 mb-2 text-secondary"><i class="fas fa-headset me-3"></i> Online Sessions</a>
            <a href="prescriptions.php" class="nav-link py-3 rounded-4 mb-2 text-secondary"><i class="fas fa-prescription me-3"></i> Prescriptions</a>
        <?php endif; ?>

        <?php if($role == 'admin'): ?>
            <a href="appointments.php" class="nav-link py-3 rounded-4 mb-2 text-secondary"><i class="fas fa-calendar-alt me-3"></i> Appointments</a>
            <a href="manage-doctors.php" class="nav-link py-3 rounded-4 mb-2 text-secondary"><i class="fas fa-user-md me-3"></i> Doctors</a>
            <a href="patients.php" class="nav-link py-3 rounded-4 mb-2 text-secondary"><i class="fas fa-users me-3"></i> Patients</a>
            <a href="departments.php" class="nav-link py-3 rounded-4 mb-2 text-secondary"><i class="fas fa-hospital me-3"></i> Departments</a>
            <a href="pharmacy.php" class="nav-link py-3 rounded-4 mb-2 text-secondary"><i class="fas fa-capsules me-3"></i> Pharmacy</a>
            <a href="lab.php" class="nav-link py-3 rounded-4 mb-2 text-secondary"><i class="fas fa-microscope me-3"></i> Laboratory</a>
            <a href="reports.php" class="nav-link py-3 rounded-4 mb-2 text-secondary"><i class="fas fa-chart-line me-3"></i> Reports</a>
        <?php endif; ?>

        <?php if($role == 'lab_tech'): ?>
            <a href="lab.php" class="nav-link py-3 rounded-4 mb-2 active"><i class="fas fa-microscope me-3"></i> Laboratory Queue</a>
        <?php endif; ?>

        <?php if($role == 'pharmacist'): ?>
            <a href="pharmacy.php" class="nav-link py-3 rounded-4 mb-2 active"><i class="fas fa-capsules me-3"></i> Pharmacy Inventory</a>
        <?php endif; ?>

        <?php if($role == 'receptionist'): ?>
            <a href="appointments.php" class="nav-link py-3 rounded-4 mb-2 active"><i class="fas fa-calendar-alt me-3"></i> Active Appointments</a>
            <a href="patients.php" class="nav-link py-3 rounded-4 mb-2 text-secondary"><i class="fas fa-users me-3"></i> Patient Directory</a>
        <?php endif; ?>

        <!-- Shared Settings & Logout -->
        <hr class="text-secondary opacity-25">
        <a href="../logout.php" class="nav-link py-3 rounded-4 mb-2 text-danger"><i class="fas fa-sign-out-alt me-3"></i> Logout</a>
    </div>
</div>

<style>
/* Dashboard Styles */
.nav-link.active {
    background-color: var(--primary-color) !important;
    color: white !important;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.2);
}
.nav-link:hover:not(.active) {
    background-color: #f8f9fa;
    color: var(--primary-color) !important;
}
.sidebar-nav .nav-link {
    font-weight: 500;
    transition: 0.3s;
}
</style>
