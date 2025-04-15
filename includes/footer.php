<footer class="py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-3">
                <h5 class="mb-3" style="font-family: 'Playfair Display', serif;">Cafeteria</h5>
                <p>Serving premium coffee and delicious pastries since 2023.</p>
            </div>
            <div class="col-md-4 mb-3">
                <h5 class="mb-3" style="font-family: 'Playfair Display', serif;">Quick Links</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="home" class="text-white text-decoration-none">Home</a></li>
                    <li class="mb-2"><a href="products" class="text-white text-decoration-none">Products</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="mb-2"><a href="logout" class="text-white text-decoration-none">Logout</a></li>
                    <?php else: ?>
                        <li class="mb-2"><a href="login" class="text-white text-decoration-none">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="col-md-4">
                <h5 class="mb-3" style="font-family: 'Playfair Display', serif;">Contact Us</h5>
                <address style="font-style: normal;">
                    <div class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> 123 Coffee Street, Brewville</div>
                    <div class="mb-2"><i class="fas fa-phone me-2"></i> (123) 456-7890</div>
                    <div class="mb-2"><i class="fas fa-envelope me-2"></i> info@cafeteria.com</div>
                </address>
            </div>
        </div>
        <hr style="border-color: rgba(255,255,255,0.2);">
        <div class="text-center">
            <p class="mb-0">&copy; <?= date('Y') ?> Cafeteria. All rights reserved.</p>
        </div>
    </div>
</footer>