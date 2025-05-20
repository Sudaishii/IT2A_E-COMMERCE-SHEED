<?php include 'helpers/functions.php'; ?>
<?php template('header.php'); ?>
<?php

use Rasheed\MiniFrameworkStore\Models\Product;
use Rasheed\MiniFrameworkStore\Models\Category;

$products = new Product();
$categoryModel = new Category(); // Use a different variable name for the Category model instance

$categoryId = isset($_GET['id']) ? $_GET['id'] : null;

// Redirect if no category ID is provided
if (!$categoryId) {
    header('Location: index.php'); // Or wherever you want to redirect
    exit;
}

// Fetch category details to display the name
$categoryDetails = $categoryModel->getById($categoryId);

// Redirect if category not found
if (!$categoryDetails) {
    header('Location: index.php'); // Or a 404 page
    exit;
}

$amounLocale = 'en_PH';
$pesoFormatter = new NumberFormatter($amounLocale, NumberFormatter::CURRENCY);

?>
<div class="container my-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo $categoryDetails['category_name']; ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-12 mb-4">
            <h2 class="text-center"><?php echo $categoryDetails['category_name']; ?></h2>
        </div>

        <?php foreach($products->getByCategory($categoryId) as $product): ?>
        <div class="col-md-3 mb-4">
            <div class="card product-card h-100 shadow-hover">
                <div class="position-relative">
                    <img src="<?php echo $product['image_path'] ?>" 
                         class="card-img-top" 
                         alt="<?php echo $product['name'] ?>">
                    <div class="position-absolute top-0 end-0 m-2">
                        <span class="badge bg-accent">
                            <?php echo $formattedAmount = $pesoFormatter->formatCurrency($product['price'], 'PHP') ?>
                        </span>
                    </div>
                </div>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?php echo $product['name'] ?></h5>
                    <p class="card-text flex-grow-1 text-muted"><?php echo substr($product['description'], 0, 100) . '...' ?></p>
                    <div class="d-grid gap-2 mt-3">
                        <a href="product.php?id=<?php echo $product['id'] ?>" 
                           class="btn btn-primary">
                            <i class="bi bi-eye me-2"></i>View Product
                        </a>
                        <button class="btn btn-success add-to-cart" 
                                data-productid="<?php echo $product['id'] ?>" 
                                data-quantity="1">
                            <i class="bi bi-cart-plus me-2"></i>Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php template('footer.php'); ?>