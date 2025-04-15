<?php
session_start();
require_once '../includes/utils.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafeteria - Home</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Raleway:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="../assets/stylesheet.css" rel="stylesheet">
</head>
<body>
    <div class="background-overlay"></div>
    
    <?php include '../includes/header.php'; ?>
    
    <main>
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="hero-carousel">
                <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                    </div>
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <div class="carousel-content">
                                <h1>Welcome to Cafeteria</h1>
                                <p>Enjoy our premium coffee and delicious pastries delivered right to your doorstep.</p>
                                <a href="products" class="btn btn-primary btn-lg btn-hero">Explore Our Menu</a>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="carousel-content">
                                <h1>Fresh Ingredients</h1>
                                <p>We use only the finest ingredients to create our delicious menu items.</p>
                                <a href="products" class="btn btn-primary btn-lg btn-hero">View Products</a>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="carousel-content">
                                <h1>Fast Delivery</h1>
                                <p>Order online and get your favorite items delivered quickly to your location.</p>
                                <a href="products" class="btn btn-primary btn-lg btn-hero">Order Now</a>
                            </div>
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </section>
        
        <!-- Features Section -->
        <section class="features-section py-5">
            <div class="container">
                <h2 class="text-center section-title mb-5">Why Choose Cafeteria?</h2>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-coffee"></i>
                            </div>
                            <h3>Premium Coffee</h3>
                            <p>We source our beans from the finest coffee farms around the world, ensuring every cup delivers exceptional flavor and aroma.</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-bread-slice"></i>
                            </div>
                            <h3>Fresh Pastries</h3>
                            <p>Our pastries are baked fresh daily using only the finest ingredients, creating delightful treats that complement our coffee perfectly.</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-truck"></i>
                            </div>
                            <h3>Fast Delivery</h3>
                            <p>We deliver your orders quickly and efficiently to your location, ensuring your coffee is still hot and pastries fresh when they arrive.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- About Section -->
        <section class="about-section py-5">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <div class="about-image">
                            <img src="../assets/about_ca.png" alt="About Cafeteria" class="img-fluid rounded shadow">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="about-content">
                            <h2 class="section-title">Our Story</h2>
                            <p class="lead">Serving the community since 2023</p>
                            <p>At Cafeteria, we believe in creating memorable experiences through exceptional food and beverages. Our journey began with a simple idea: to provide a welcoming space where people can enjoy quality coffee and delicious food.</p>
                            <p>Today, we continue to uphold our commitment to quality, using only the finest ingredients and maintaining the highest standards in everything we do.</p>
                            <a href="#" class="btn btn-primary btn-lg btn-hero">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
      
        <!-- Testimonials Section -->
        <section class="testimonials py-5">
            <div class="container">
                <h2 class="text-center section-title mb-5">What Our Customers Say</h2>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="testimonial-card">
                            <div class="testimonial-content">
                                <p>"The coffee at Cafeteria is simply amazing. I order from them every morning and the delivery is always prompt!"</p>
                            </div>
                            <div class="testimonial-author">
                                <div class="testimonial-avatar">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div class="testimonial-info">
                                    <h5>Sarah Johnson</h5>
                                    <div class="testimonial-rating">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="testimonial-card">
                            <div class="testimonial-content">
                                <p>"Their pastries are to die for! Fresh, flavorful, and always delivered in perfect condition. Highly recommend!"</p>
                            </div>
                            <div class="testimonial-author">
                                <div class="testimonial-avatar">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div class="testimonial-info">
                                    <h5>Michael Chen</h5>
                                    <div class="testimonial-rating">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star-half-alt"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="testimonial-card">
                            <div class="testimonial-content">
                                <p>"I love the variety of options at Cafeteria. The ordering process is simple and the food quality is consistently excellent."</p>
                            </div>
                            <div class="testimonial-author">
                                <div class="testimonial-avatar">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div class="testimonial-info">
                                    <h5>Emily Rodriguez</h5>
                                    <div class="testimonial-rating">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action Section -->
        <section class="cta-section py-5">
            <div class="container">
                <div class="cta-content text-center">
                    <h2>Ready to Order?</h2>
                    <p class="lead">Explore our menu and enjoy delicious food delivered to your doorstep</p>
                    <a href="products" class="btn btn-primary btn-lg mt-3">Order Now</a>
                </div>
            </div>
        </section>
    </main>
    
    <?php include '../includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>