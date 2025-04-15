<header class="mb-4">
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="home">
                <i class="fas fa-mug-hot me-2"></i>Cafeteria
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products">Products</a>
                    </li>
                    <!-- Rest of the navigation items remain unchanged -->
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'admin'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Admin
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                            <li><a class="dropdown-item" href="add_product">Add Product</a></li>
                            <li><a class="dropdown-item" href="add_category">Add Category</a></li>
                            <li><a class="dropdown-item" href="users">Manage Users</a></li>
                            <li><a class="dropdown-item" href="checks">Order Checks</a></li>
                            <li><a class="dropdown-item" href="orders">Create Order</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                </ul>
                <div class="d-flex">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <span class="navbar-text me-3">
                            <i class="fas fa-user me-1"></i> <?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?>
                        </span>
                        <a href="logout" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-sign-out-alt me-1"></i> Logout
                        </a>
                    <?php else: ?>
                        <a href="login" class="btn btn-outline-light btn-sm me-2">
                            <i class="fas fa-sign-in-alt me-1"></i> Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</header>

<!-- Add this script to fix dropdown menu and mobile menu issues -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fix for dropdown menu - handle multiple dropdowns if present
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    
    dropdownToggles.forEach(function(toggle) {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Get the dropdown menu associated with this toggle
            const dropdownMenu = this.nextElementSibling;
            
            // Close all other dropdowns first
            document.querySelectorAll('.dropdown-menu.show').forEach(function(menu) {
                if (menu !== dropdownMenu) {
                    menu.classList.remove('show');
                }
            });
            
            // Toggle this dropdown menu
            dropdownMenu.classList.toggle('show');
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu.show').forEach(function(menu) {
                menu.classList.remove('show');
            });
        }
    });
    
    // Mobile menu toggle functionality
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');
    
    if (navbarToggler) {
        navbarToggler.addEventListener('click', function() {
            navbarCollapse.classList.toggle('show');
        });
    }
    
    // Fix for mobile menu - close when clicking on links
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link:not(.dropdown-toggle)');
    
    navLinks.forEach(function(link) {
        link.addEventListener('click', function() {
            // Check if navbar is expanded (mobile view)
            if (window.innerWidth < 992) {
                // Collapse the navbar
                navbarCollapse.classList.remove('show');
            }
        });
    });
    
    // Handle dropdown items in mobile view
    const dropdownItems = document.querySelectorAll('.dropdown-item');
    
    dropdownItems.forEach(function(item) {
        item.addEventListener('click', function() {
            // In mobile view, close both the dropdown and the navbar
            if (window.innerWidth < 992) {
                const dropdownMenu = this.closest('.dropdown-menu');
                if (dropdownMenu) {
                    dropdownMenu.classList.remove('show');
                }
                navbarCollapse.classList.remove('show');
            }
        });
    });
    
    // Handle window resize - reset menu state on desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 992) {
            // On desktop view, ensure proper state
            document.querySelectorAll('.dropdown-menu').forEach(function(menu) {
                menu.classList.remove('show');
            });
        }
    });
});
</script>