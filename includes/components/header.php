<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . " | " . APP_NAME : APP_NAME; ?></title>
    
    <!-- FAVICON -->
    <link rel="icon" type="image/png" href="<?php echo APP_URL; ?>/assets/images/favicon.png">
    
    <!-- CSS Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo APP_URL; ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- TOP BAR -->
<div class="bg-primary text-white py-1 d-none d-md-block">
    <div class="container d-flex justify-content-between">
        <div class="small">
            <i class="fas fa-map-marker-alt me-2"></i> Lagos, Nigeria
            <i class="fas fa-phone-alt ms-4 me-2"></i> +234 XXX XXX XXXX
        </div>
        <div class="small">
            <a href="#" class="text-white me-3 text-decoration-none"><i class="fab fa-facebook"></i></a>
            <a href="#" class="text-white me-3 text-decoration-none"><i class="fab fa-twitter"></i></a>
            <a href="#" class="text-white text-decoration-none"><i class="fab fa-instagram"></i></a>
        </div>
    </div>
</div>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container text-center">
        <a class="navbar-brand d-flex align-items-center" href="<?php echo APP_URL; ?>/index.php">
            <i class="fas fa-heartbeat text-primary me-2"></i>
            NovaCare <span class="text-primary ms-1">Specialist</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="<?php echo APP_URL; ?>/index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo APP_URL; ?>/about.php">About</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo APP_URL; ?>/services.php">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo APP_URL; ?>/doctors.php">Doctors</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo APP_URL; ?>/contact.php">Contact</a></li>
            </ul>
            
            <div class="d-flex align-items-center">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="<?php echo APP_URL; ?>/<?php echo $_SESSION['role']; ?>/dashboard.php" class="btn btn-outline-primary rounded-pill me-2">Dashboard</a>
                    <a href="<?php echo APP_URL; ?>/logout.php" class="btn btn-primary-gradient">Logout</a>
                <?php else: ?>
                    <a href="<?php echo APP_URL; ?>/login.php" class="btn btn-outline-primary rounded-pill me-2">Login</a>
                    <a href="<?php echo APP_URL; ?>/patient/register.php" class="btn btn-primary-gradient">Appointment</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
