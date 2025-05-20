s<?php include 'helpers/functions.php'; ?>
<?php template('header.php'); ?>

<?php
use Rasheed\MiniFrameworkStore\Models\Product;

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$products = new Product();
$amounLocale = 'en_PH';
$pesoFormatter = new NumberFormatter($amounLocale, NumberFormatter::CURRENCY);

$total = 0;
$cartItems = [];

foreach ($cart as $productId => $quantity) {
    $product = $products->getById($productId);
    if ($product) {
        $subtotal = $product['price'] * $quantity;
        $total += $subtotal;
        $cartItems[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'subtotal' => $subtotal,
            'image' => $product['image_path']
        ];
    }
}
?>

<div class="container my-5">
    <h1 class="mb-4">Shopping Cart</h1>

    <?php if (empty($cartItems)): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Your cart is empty. 
            <a href="index.php" class="alert-link">Continue shopping</a>
        </div>
    <?php else: ?>
        <div class="row">
            <!-- Cart Items -->
            <div class="col-md-8">
                <?php foreach ($cartItems as $item): ?>
                    <div class="cart-item mb-3">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <img src="<?php echo $item['image']; ?>" 
                                     class="img-fluid rounded" 
                                     alt="<?php echo $item['name']; ?>"
                                     style="max-height: 100px; object-fit: contain;">
                            </div>
                            <div class="col-md-4">
                                <h5 class="mb-1"><?php echo $item['name']; ?></h5>
                                <p class="text-success mb-0">
                                    <?php echo $pesoFormatter->formatCurrency($item['price'], 'PHP'); ?>
                                </p>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <button class="btn btn-outline-secondary btn-sm update-quantity" 
                                            data-productid="<?php echo $item['id']; ?>" 
                                            data-action="decrease">-</button>
                                    <input type="number" 
                                           class="form-control form-control-sm text-center quantity-input" 
                                           value="<?php echo $item['quantity']; ?>" 
                                           min="1" 
                                           max="99"
                                           data-productid="<?php echo $item['id']; ?>">
                                    <button class="btn btn-outline-secondary btn-sm update-quantity" 
                                            data-productid="<?php echo $item['id']; ?>" 
                                            data-action="increase">+</button>
                                </div>
                            </div>
                            <div class="col-md-2 text-end">
                                <h6 class="mb-1"><?php echo $pesoFormatter->formatCurrency($item['subtotal'], 'PHP'); ?></h6>
                                <button class="btn btn-danger btn-sm remove-item" 
                                        data-productid="<?php echo $item['id']; ?>">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Order Summary -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-olive text-white">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span>Subtotal:</span>
                            <span><?php echo $pesoFormatter->formatCurrency($total, 'PHP'); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Shipping:</span>
                            <span>Free</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong class="text-success"><?php echo $pesoFormatter->formatCurrency($total, 'PHP'); ?></strong>
                        </div>
                        <a href="checkout.php" class="btn btn-success w-100">
                            <i class="bi bi-credit-card"></i> Proceed to Checkout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
$(document).ready(function() {
    // Update quantity
    $('.update-quantity').click(function() {
        const productId = $(this).data('productid');
        const action = $(this).data('action');
        const input = $(`.quantity-input[data-productid="${productId}"]`);
        let value = parseInt(input.val());
        
        if (action === 'increase' && value < 99) {
            value++;
        } else if (action === 'decrease' && value > 1) {
            value--;
        }
        
        input.val(value);
        updateCartItem(productId, value);
    });

    // Manual quantity input
    $('.quantity-input').change(function() {
        const productId = $(this).data('productid');
        const value = parseInt($(this).val());
        updateCartItem(productId, value);
    });

    // Remove item
    $('.remove-item').click(function() {
        const productId = $(this).data('productid');
        removeCartItem(productId);
    });

    function updateCartItem(productId, quantity) {
        $.ajax({
            url: 'cart-process.php',
            method: 'POST',
            data: {
                action: 'update',
                product_id: productId,
                quantity: quantity
            },
            success: function(response) {
                const data = JSON.parse(response);
                if(data.success) {
                    location.reload();
                }
            }
        });
    }

    function removeCartItem(productId) {
        $.ajax({
            url: 'cart-process.php',
            method: 'POST',
            data: {
                action: 'remove',
                product_id: productId
            },
            success: function(response) {
                const data = JSON.parse(response);
                if(data.success) {
                    location.reload();
                }
            }
        });
    }
});
</script>

<?php template('footer.php'); ?>