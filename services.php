<?php
require_once 'config.php';
$pageTitle = "Medical Services";
include_once 'includes/components/header.php';
?>

<!-- HERO SECTION -->
<section class="hero-section" style="min-height: 40vh; background: linear-gradient(rgba(0, 51, 102, 0.9), rgba(0, 51, 102, 0.7)), url('assets/images/hero_bg.jpg') center/cover no-repeat;">
    <div class="container text-center pt-5">
        <h1 class="hero-title text-white">Comprehensive <span class="border-bottom border-3 border-accent">Healthcare Services</span></h1>
        <p class="hero-subtitle text-light mb-0 mx-auto" style="max-width: 600px;">From advanced diagnostics to premium outpatient care, we offer a full spectrum of specialized medical services under one roof.</p>
    </div>
</section>

<!-- CORE SERVICES GRID -->
<section class="section-padding bg-soft-blue">
    <div class="container">
        <div class="row g-5">
            <!-- Outpatient Care -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 feature-card bg-white p-4 d-flex flex-row align-items-center">
                    <div class="bg-primary text-white p-4 rounded-4 shadow-sm me-4 text-center" style="width: 90px; height: 90px;">
                        <i class="fas fa-user-md fs-2 mt-2"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-2">Outpatient Departments (OPD)</h4>
                        <p class="text-muted small mb-0">Consult with top specialists without needing an overnight stay. Quick triage, examination, and treatment across all medical fields.</p>
                    </div>
                </div>
            </div>

            <!-- Laboratory & Diagnostics -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 feature-card bg-white p-4 d-flex flex-row align-items-center">
                    <div class="bg-primary text-white p-4 rounded-4 shadow-sm me-4 text-center" style="width: 90px; height: 90px;">
                        <i class="fas fa-flask fs-2 mt-2"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-2">24/7 Diagnostics & Laboratory</h4>
                        <p class="text-muted small mb-0">State-of-the-art hematology, biochemistry, and imaging centers (X-Ray, Ultrasound, CT Scan) providing rapid, accurate results.</p>
                    </div>
                </div>
            </div>

            <!-- Pharmacy -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 feature-card bg-white p-4 d-flex flex-row align-items-center">
                    <div class="bg-primary text-white p-4 rounded-4 shadow-sm me-4 text-center" style="width: 90px; height: 90px;">
                        <i class="fas fa-capsules fs-2 mt-2"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-2">Fully Stocked Pharmacy</h4>
                        <p class="text-muted small mb-0">Get verified and genuine medications instantly. Our pharmacists verify digital prescriptions generated directly from the doctor's EMR.</p>
                    </div>
                </div>
            </div>

            <!-- Telemedicine -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 feature-card bg-white p-4 d-flex flex-row align-items-center">
                    <div class="bg-primary text-white p-4 rounded-4 shadow-sm me-4 text-center" style="width: 90px; height: 90px;">
                        <i class="fas fa-video fs-2 mt-2"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-2">Virtual Care (Telemedicine)</h4>
                        <p class="text-muted small mb-0">Talk to a doctor over secure video calls from home. E-prescriptions and digital medical records included.</p>
                        <a href="telemedicine.php" class="text-primary fw-bold text-decoration-none small d-block mt-2">Learn More <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            
            <!-- Inpatient -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 feature-card bg-white p-4 d-flex flex-row align-items-center">
                    <div class="bg-primary text-white p-4 rounded-4 shadow-sm me-4 text-center" style="width: 90px; height: 90px;">
                        <i class="fas fa-bed fs-2 mt-2"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-2">Premium Inpatient Care</h4>
                        <p class="text-muted small mb-0">Luxurious private wards with dedicated 24/7 nursing and automated vitals monitoring for post-surgery and admissions.</p>
                    </div>
                </div>
            </div>

            <!-- NHIS & Insurance -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 feature-card bg-white p-4 d-flex flex-row align-items-center">
                    <div class="bg-primary text-white p-4 rounded-4 shadow-sm me-4 text-center" style="width: 90px; height: 90px;">
                        <i class="fas fa-shield-alt fs-2 mt-2"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-2">HMO & NHIS Partnerships</h4>
                        <p class="text-muted small mb-0">We accept major Health Maintenance Organizations and the National Health Insurance Scheme for seamless, stress-free billing.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- CALL TO ACTION -->
<section class="section-padding bg-white text-center">
    <div class="container">
        <h2 class="mb-4 text-dark fw-bold">Ready to Experience Modern Healthcare?</h2>
        <a href="patient/register.php" class="btn btn-primary-gradient rounded-pill px-5 py-3 fw-bold fs-5 shadow-sm">Reserve Your Appointment</a>
    </div>
</section>

<?php include_once 'includes/components/footer.php'; ?>
