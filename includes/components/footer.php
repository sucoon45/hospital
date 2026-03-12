<!-- FOOTER -->
<footer class="footer mt-5">
    <div class="container pb-5">
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <h5 class="navbar-brand text-white mb-4">
                    <i class="fas fa-heartbeat text-primary me-2"></i>
                    Kamirex Specialist
                </h5>
                <p class="mb-4">Providing affordable, world-class healthcare with integrity and innovation. Your health and wellbeing are our top priority.</p>
                <div class="footer-social">
                    <a href="#" class="me-3 fs-5"><i class="fab fa-facebook text-white"></i></a>
                    <a href="#" class="me-3 fs-5"><i class="fab fa-twitter text-white"></i></a>
                    <a href="#" class="me-3 fs-5"><i class="fab fa-instagram text-white"></i></a>
                    <a href="#" class="fs-5"><i class="fab fa-linkedin text-white"></i></a>
                </div>
            </div>
            
            <div class="col-lg-2 col-md-6 mb-4">
                <h5 class="text-white">Quick Links</h5>
                <a href="index.php" class="footer-link">Home</a>
                <a href="about.php" class="footer-link">About Us</a>
                <a href="doctors.php" class="footer-link">Our Doctors</a>
                <a href="services.php" class="footer-link">Services</a>
                <a href="contact.php" class="footer-link">Contact Us</a>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="text-white">Our Services</h5>
                <a href="#" class="footer-link">General Medicine</a>
                <a href="#" class="footer-link">Emergency Support</a>
                <a href="#" class="footer-link">Laboratory Analysis</a>
                <a href="#" class="footer-link">Pharmacy Care</a>
                <a href="#" class="footer-link">Telemedicine</a>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="text-white">Emergency Contact</h5>
                <p><i class="fas fa-phone-alt text-primary me-2"></i> +234 812 XXX XXXX</p>
                <p><i class="fas fa-envelope text-primary me-2"></i> info@kamirexhospital.com</p>
                <p><i class="fas fa-map-marker-alt text-primary me-2"></i> Victoria Island, Lagos, Nigeria</p>
            </div>
        </div>
    </div>
    <div class="bg-dark py-3 border-top border-secondary">
        <div class="container text-center">
            <p class="small mb-0 text-secondary">&copy; <?php echo date('Y'); ?> Kamirex Specialist Hospital. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- AI CHAT FLOATING BUTTON -->
<div class="position-fixed bottom-0 end-0 m-4 z-index-100">
    <button id="aiChatBtn" class="btn btn-primary-gradient rounded-circle shadow-lg p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
        <i class="fas fa-robot fs-3"></i>
    </button>
</div>

<!-- AI CHAT WINDOW (Hidden by default) -->
<div id="aiChatWindow" class="position-fixed bottom-0 end-0 m-4 mb-5 d-none z-index-100" style="width: 350px; height: 450px;">
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden h-100">
        <div class="card-header bg-primary text-white p-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold"><i class="fas fa-robot me-2"></i> Kamirex AI Assitant</h6>
            <button id="aiChatClose" class="btn btn-sm text-white border-0"><i class="fas fa-times"></i></button>
        </div>
        <div id="aiChatBody" class="card-body bg-white p-3 overflow-auto" style="height: 320px;">
            <div class="mb-3 text-start">
                <div class="d-inline-block px-3 py-2 rounded-4 bg-light text-dark small">
                    Hello! 👋 I'm your virtual health assistant. Describe your symptoms, and I'll help you navigate our services.
                </div>
            </div>
        </div>
        <div class="card-footer bg-white border-0 p-3">
            <div class="input-group">
                <input id="aiChatInput" type="text" class="form-control rounded-pill-start small" placeholder="Type symptoms here...">
                <button id="aiChatSend" class="btn btn-primary rounded-pill-end"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
    </div>
</div>

<!-- JS Dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="<?php echo APP_URL; ?>/assets/js/main.js"></script>
<script src="<?php echo APP_URL; ?>/assets/js/ai-chat.js"></script>

</body>
</html>
