<?php
require_once 'config.php';
require_once 'includes/functions/database.php';

$departments = getDepartments();
$pageTitle = "Medical Departments";
include_once 'includes/components/header.php';
?>

<!-- DEPTS HERO -->
<section class="hero-section" style="min-height: 40vh; background: linear-gradient(rgba(0, 51, 102, 0.9), rgba(32, 201, 151, 0.7)), url('assets/images/hero_bg.jpg') center/cover no-repeat;">
    <div class="container text-center pt-5">
        <h1 class="hero-title text-white">Our Medical <span class="border-bottom border-3">Faculties</span></h1>
        <p class="hero-subtitle text-light mb-0 mx-auto" style="max-width: 600px;">Explore the various specialized branches of Kamirex Hospital. Each department is equipped with modern infrastructure and top specialists.</p>
    </div>
</section>

<!-- DEPARTMENTS LISTING -->
<section class="section-padding bg-soft-blue min-vh-100">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <?php if(empty($departments)): ?>
                <div class="col-12 text-center py-5">
                    <h4 class="text-muted"><i class="fas fa-hospital d-block fs-1 mb-3"></i> Currently setting up our facilities.</h4>
                </div>
            <?php else: ?>
                <?php foreach($departments as $dept): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card border-0 h-100 shadow-sm rounded-4 bg-white feature-card">
                            <div class="card-body p-4 text-center">
                                <div class="bg-light d-inline-block rounded-circle p-4 mb-4 text-primary mt-3 shadow-sm border border-2 border-primary border-opacity-25" style="width: 100px; height: 100px;">
                                    <i class="fas fa-microscope fs-1"></i> <!-- You can customize icon based on name later -->
                                </div>
                                <h4 class="fw-bold mb-3"><?php echo $dept['name']; ?></h4>
                                <p class="text-muted small mb-0"><?php echo $dept['description'] ?: 'Specialized and comprehensive care units with 24/7 observation and surgical clearance.'; ?></p>
                            </div>
                            <div class="card-footer bg-transparent border-top p-3 text-center">
                                <a href="doctors.php?dept=<?php echo urlencode($dept['name']); ?>" class="text-primary text-decoration-none fw-bold small">View Specialists <i class="fas fa-arrow-right ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include_once 'includes/components/footer.php'; ?>
