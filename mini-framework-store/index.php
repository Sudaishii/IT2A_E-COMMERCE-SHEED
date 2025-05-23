<?php include 'helpers/functions.php'; ?>
<?php template('header.php'); ?>
<?php

use Rasheed\MiniFrameworkStore\Models\Product;
use Rasheed\MiniFrameworkStore\Models\Category;

$products = new Product();
$categories = new Category();

$amounLocale = 'en_PH';
$pesoFormatter = new NumberFormatter($amounLocale, NumberFormatter::CURRENCY);

// Get selected category if any
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : null;
$productsList = $selectedCategory ? $products->getByCategory($selectedCategory) : $products->getAll();

?>

<!-- Sakura Petals Animation -->
<div class="sakura-container">
    <?php for($i = 0; $i < 15; $i++): ?>
        <div class="sakura-petal" style="
            left: <?php echo rand(0, 100); ?>vw;
            width: <?php echo rand(10, 25); ?>px;
            height: <?php echo rand(10, 25); ?>px;
            animation-duration: <?php echo rand(10, 25); ?>s;
            animation-delay: <?php echo rand(0, 5); ?>s;
        "></div>
    <?php endfor; ?>
</div>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8 mx-auto text-center">
                <h1 class="hero-title fade-in">Welcome to Snezhy Online Store</h1>
                <p class="hero-subtitle fade-in">Discover our premium Sakura Skincare, Cherry Blossom Makeup, and enchanting Fragrances!</p>
                <div class="mt-4 fade-in">
                    <a href="#products" class="btn btn-light btn-lg px-5 py-3 shadow-hover">
                        <i class="bi bi-arrow-down-circle me-2"></i>Explore Products
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Categories -->
<section class="container mb-5">
    <div class="row">
        <div class="col-12 text-center mb-4">
            <h2 class="text-gradient">Featured Categories</h2>
            <p class="text-muted">Browse through our carefully curated collections</p>
        </div>
        <?php foreach($categories->getAll() as $category): ?>
            <div class="col-md-4 mb-4">
                <div class="card category-card h-100 shadow-hover">
                    <div class="card-body text-center">
                        <i class="bi bi-tag display-4 mb-3 text-primary"></i>
                        <h3 class="card-title"><?php echo $category['category_name']; ?></h3>
                        <a href="index.php?category=<?php echo $category['id']; ?>" class="btn btn-outline-primary mt-3">
                            View Products
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Main Content -->
<div class="container my-5" id="products">
    <div class="row">
        <!-- Category Filter Sidebar -->
        <div class="col-md-3">
            <div class="card category-sidebar mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-grid me-2"></i>Categories</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="index.php" class="list-group-item list-group-item-action <?php echo !$selectedCategory ? 'active' : ''; ?>">
                            <i class="bi bi-box me-2"></i>All Products
                        </a>
                        <?php foreach($categories->getAll() as $category): ?>
                            <a href="index.php?category=<?php echo $category['id']; ?>" 
                               class="list-group-item list-group-item-action <?php echo $selectedCategory == $category['id'] ? 'active' : ''; ?>">
                                <i class="bi bi-tag me-2"></i><?php echo $category['category_name']; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-md-9">
            <?php if(empty($productsList)): ?>
                <div class="alert alert-info shadow-hover">
                    <i class="bi bi-info-circle me-2"></i>No products found in this category.
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach($productsList as $product): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card product-card h-100 shadow-hover">
                                <div class="position-relative">
                                    <img src="<?php echo $product['image_path'] ?>" 
                                         class="card-img-top" 
                                         alt="<?php echo $product['name'] ?>">
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <span class="badge bg-accent">
                                            <?php echo $pesoFormatter->formatCurrency($product['price'], 'PHP') ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?php echo $product['name'] ?></h5>
                                    <p class="card-text flex-grow-1 text-muted">
                                        <?php echo substr($product['description'], 0, 100) . '...' ?>
                                    </p>
                                    <div class="d-grid gap-2 mt-3">
                                        <a href="product.php?id=<?php echo $product['id'] ?>" 
                                           class="btn btn-primary">
                                            <i class="bi bi-eye me-2"></i>View Details
                                        </a>
                                        <?php if (isLoggedIn()): ?>
                                            <button type="button" 
                                                    class="btn btn-success add-to-cart" 
                                                    data-productid="<?php echo $product['id'] ?>" 
                                                    data-quantity="1">
                                                <i class="bi bi-cart-plus me-2"></i>Add to Cart
                                            </button>
                                        <?php else: ?>
                                            <a href="login.php" class="btn btn-success">
                                                <i class="bi bi-cart-plus me-2"></i>Add to Cart
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php template('footer.php'); ?>