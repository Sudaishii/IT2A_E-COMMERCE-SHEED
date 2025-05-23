<?php include 'helpers/functions.php'; ?>
<?php template('header.php'); ?>

<?php
use Rasheed\MiniFrameworkStore\Models\Product;
use Rasheed\MiniFrameworkStore\Models\Category;

$product = new Product();
$category = new Category();

$productId = isset($_GET['id']) ? $_GET['id'] : null;
$productDetails = $product->getById($productId);

if (!$productDetails) {
    header('Location: index.php');
    exit;
}

$categoryDetails = $category->getById($productDetails['category_id']);
$amounLocale = 'en_PH';
$pesoFormatter = new NumberFormatter($amounLocale, NumberFormatter::CURRENCY);
?>

<div class="container my-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="category.php?id=<?php echo $categoryDetails['id']; ?>"><?php echo $categoryDetails['category_name']; ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo $productDetails['name']; ?></li>
        </ol>
    </nav>

    <div class="row">
   
        <div class="col-md-6">
            <div class="product-image-container">
                <img src="<?php echo $productDetails['image_path']; ?>" 
                     class="img-fluid rounded" 
                     alt="<?php echo $productDetails['name']; ?>"
                     style="max-height: 500px; width: 100%; object-fit: contain;">
            </div>
        </div>

   
        <div class="col-md-6">
            <div class="product-details">
                <h1 class="mb-3"><?php echo $productDetails['name']; ?></h1>
                <div class="category-badge mb-3">
                    <?php echo $categoryDetails['category_name']; ?>
                </div>
                <h2 class="text-success mb-4">
                    <?php echo $pesoFormatter->formatCurrency($productDetails['price'], 'PHP'); ?>
                </h2>
                <div class="description mb-4">
                    <h5>Description</h5>
                    <p><?php echo nl2br($productDetails['description']); ?></p>
                </div>
                
              
                <form class="add-to-cart-form mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <div class="input-group">
                                <button type="button" class="btn btn-outline-secondary" onclick="decrementQuantity()">-</button>
                                <input type="number" class="form-control text-center" id="quantity" value="1" min="1" max="99">
                                <button type="button" class="btn btn-outline-secondary" onclick="incrementQuantity()">+</button>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <?php if (isLoggedIn()): ?>
                                <button type="button" 
                                        class="btn btn-success w-100 add-to-cart" 
                                        data-productid="<?php echo $productDetails['id']; ?>">
                                    <i class="bi bi-cart-plus"></i> Add to Cart
                                </button>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-success w-100">
                                    <i class="bi bi-cart-plus"></i> Add to Cart
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>

          
                <div class="product-meta">
                    <p class="text-muted">
                        <small>
                            <i class="bi bi-clock"></i> Added on <?php echo date('F j, Y', strtotime($productDetails['created_at'])); ?>
                        </small>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function incrementQuantity() {
    const input = document.getElementById('quantity');
    const value = parseInt(input.value);
    if (value < 99) input.value = value + 1;
}

function decrementQuantity() {
    const input = document.getElementById('quantity');
    const value = parseInt(input.value);
    if (value > 1) input.value = value - 1;
}

$(document).ready(function() {
    console.log('Binding add-to-cart click event with delegation');
    let ajaxInProgress = false;
    let debounceTimeout = null;
    $(document).off('click', '.add-to-cart').on('click', '.add-to-cart', function(e) {
        e.preventDefault();
        if (ajaxInProgress) {
            console.log('AJAX call already in progress, ignoring click');
            return;
        }
        if (debounceTimeout) {
            console.log('Click ignored due to debounce');
            return;
        }
        debounceTimeout = setTimeout(() => {
            debounceTimeout = null;
        }, 500);
        ajaxInProgress = true;
        console.log('Add to cart clicked');
        const productId = $(this).data('productid');
        const quantityInput = $('#quantity');
        const quantity = quantityInput.val();
        const button = $(this);

        button.prop('disabled', true);

        console.log(`Product ID: ${productId}, Quantity: ${quantity}`);

        $.ajax({
            url: 'cart-process.php',
            method: 'POST',
            data: {
                action: 'add',
                product_id: productId,
                quantity: quantity
            },
            success: function(data) {
                if(data.success) {
                    console.log('Add to cart success:', data);
                    $('.badge').text(data.cart_count);
                    quantityInput.val(1); 
                    location.reload(); 
                } else {
                    console.error('Error adding product to cart');
                }
            },
            error: function() {
                console.error('Error occurred while processing your request');
            },
            complete: function() {
                button.prop('disabled', false);
                ajaxInProgress = false;
            }
        });
    });
});
</script>

<?php template('footer.php'); ?>