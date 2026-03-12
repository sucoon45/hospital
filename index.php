<?php
require_once 'config.php';
$pageTitle = "Modern Healthcare Excellence";
include_once 'includes/components/header.php';
?>

<!-- HERO SECTION -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="badge bg-soft-blue text-primary px-3 py-2 rounded-pill mb-3 fw-bold">Welcome to Kamirex Specialist</span>
                <h1 class="hero-title">Premier Healthcare You Can <span class="text-primary border-bottom border-primary border-3">Trust</span></h1>
                <p class="hero-subtitle mb-4">Experience world-class medical services at Lagos' most advanced multi-specialist hospital. From high-tech diagnostics to compassionate care, we prioritize your wellness above all.</p>
                <div class="d-flex flex-wrap">
                    <a href="patient/register.php" class="btn btn-primary-gradient btn-lg me-3 mb-3">Book Appointment</a>
                    <a href="services.php" class="btn btn-outline-secondary btn-lg rounded-pill mb-3">Explore Services <i class="fas fa-arrow-right ms-2"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- EMERGENCY & QUICK ACCESS BANNER -->
<section class="container mb-5">
    <div class="appointment-banner shadow-lg">
        <div class="container">
            <div class="row text-center text-md-start align-items-center">
                <div class="col-md-4 mb-3 mb-md-0 border-end border-secondary">
                    <div class="d-flex align-items-center justify-content-center justify-content-md-start">
                        <i class="fas fa-phone-alt fs-2 me-3 text-primary"></i>
                        <div>
                            <h5 class="mb-0 text-white">Emergency Hotline</h5>
                            <p class="mb-0 text-light opacity-75">+234 800 000 0000</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3 mb-md-0 border-end border-secondary">
                    <div class="d-flex align-items-center justify-content-center justify-content-md-start">
                        <i class="fas fa-clock fs-2 me-3 text-primary"></i>
                        <div>
                            <h5 class="mb-0 text-white">Working Hours</h5>
                            <p class="mb-0 text-light opacity-75">24/7 Care & Support</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <a href="contact.php" class="btn btn-primary rounded-pill px-4">Get Location <i class="fas fa-map-marker-alt ms-2"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURES SECTION -->
<section class="section-padding">
    <div class="container text-center mb-5">
        <h2 class="mb-3">Our Dedicated Services</h2>
        <p class="text-muted mx-auto" style="max-width: 600px;">We take a patient-centered approach to healthcare, providing a wide range of specialized services with precision and excellence.</p>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card text-center">
                    <div class="feature-icon mx-auto"><i class="fas fa-stethoscope"></i></div>
                    <h4>General Checkup</h4>
                    <p class="text-muted">Stay ahead of potential health issues with our comprehensive routine medical examinations.</p>
                    <a href="#" class="text-primary text-decoration-none fw-bold">Learn More <i class="fas fa-chevron-right ms-1 small"></i></a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card text-center">
                    <div class="feature-icon mx-auto"><i class="fas fa-video"></i></div>
                    <h4>Telemedicine</h4>
                    <p class="text-muted">Consult with our expert doctors from the comfort of your home via protected video sessions.</p>
                    <a href="telemedicine.php" class="text-primary text-decoration-none fw-bold">Book Online <i class="fas fa-chevron-right ms-1 small"></i></a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card text-center">
                    <div class="feature-icon mx-auto"><i class="fas fa-microscope"></i></div>
                    <h4>Modern Lab</h4>
                    <p class="text-muted">Get fast and accurate results with our state-of-the-art laboratory and diagnostic equipment.</p>
                    <a href="#" class="text-primary text-decoration-none fw-bold">Test Status <i class="fas fa-chevron-right ms-1 small"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- DEPARTMENTS PREVIEW -->
<section class="section-padding bg-soft-blue">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-md-6">
                <h2>Health Solutions Through Our <span class="text-primary">Specialized Departments</span></h2>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="departments.php" class="btn btn-outline-primary rounded-pill">View All Departments</a>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 col-sm-6 mb-4">
                <div class="card border-0 shadow-sm p-4 text-center rounded-4 h-100">
                    <i class="fas fa-heart fs-1 text-danger mb-3"></i>
                    <h5>Cardiology</h5>
                    <p class="small text-muted">Heart health is our specialty. Advanced cardiac screening.</p>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-4">
                <div class="card border-0 shadow-sm p-4 text-center rounded-4 h-100">
                    <i class="fas fa-baby fs-1 text-primary mb-3"></i>
                    <h5>Pediatrics</h5>
                    <p class="small text-muted">Caring for your little ones with expert child health services.</p>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-4">
                <div class="card border-0 shadow-sm p-4 text-center rounded-4 h-100">
                    <i class="fas fa-brain fs-1 text-info mb-3"></i>
                    <h5>Neurology</h5>
                    <p class="small text-muted">Expert care for brain and neurological conditions.</p>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-4">
                <div class="card border-0 shadow-sm p-4 text-center rounded-4 h-100">
                    <i class="fas fa-tooth fs-1 text-warning mb-3"></i>
                    <h5>Dental Care</h5>
                    <p class="small text-muted">Comprehensive dental treatments for a brighter smile.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- AI CHATBOT PREVIEW -->
<section class="section-padding">
    <div class="container">
        <div class="card bg-primary text-white border-0 shadow-lg rounded-5 overflow-hidden">
            <div class="row g-0 align-items-center">
                <div class="col-lg-7 p-5">
                    <h2 class="text-white mb-4">Virtual AI Health Assistant</h2>
                    <p class="lead mb-4">Need instant medical advice or symptom checking? Our AI-powered assistant is available 24/7 to help you navigate your health concerns.</p>
                    <ul class="list-unstyled mb-4">
                        <li><i class="fas fa-check-circle me-2 text-info"></i> Instant Symptom Checker</li>
                        <li><i class="fas fa-check-circle me-2 text-info"></i> Department Recommendation</li>
                        <li><i class="fas fa-check-circle me-2 text-info"></i> FAQ Support</li>
                    </ul>
                    <a href="#" class="btn btn-light rounded-pill px-5 text-primary fw-bold">Chat Now <i class="fas fa-robot ms-2"></i></a>
                </div>
                <div class="col-lg-5 d-none d-lg-block">
                    <div class="h-100 opacity-25 p-5 text-center">
                        <i class="fas fa-robot" style="font-size: 200px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include_once 'includes/components/footer.php'; ?>
