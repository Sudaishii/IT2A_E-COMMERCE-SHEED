<?php include 'helpers/functions.php'; ?>
<?php template('header.php'); ?>

<?php
use Rasheed\MiniFrameworkStore\Models\Product;

// Initialize cart from session
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$products = new Product();
$amounLocale = 'en_PH';
$pesoFormatter = new NumberFormatter($amounLocale, NumberFormatter::CURRENCY);

$total = 0;
$cartItems = [];

// Get product details for each item in cart
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
        <form method="POST" action="checkout.php">
        <div class="row">
            <!-- Cart Items -->
            <div class="col-md-8">
                <?php foreach ($cartItems as $item): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-1">
                                    <input type="checkbox" name="selected_products[]" value="<?php echo $item['id']; ?>" checked>
                                </div>
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
<button type="button" class="btn btn-outline-secondary btn-sm update-quantity" 
                                                data-productid="<?php echo $item['id']; ?>" 
                                                data-action="decrease">-</button>
                                        <input type="number" 
                                               class="form-control form-control-sm text-center quantity-input" 
                                               value="<?php echo $item['quantity']; ?>" 
                                               min="1" 
                                               max="99"
                                               data-productid="<?php echo $item['id']; ?>">
<button type="button" class="btn btn-outline-secondary btn-sm update-quantity" 
                                                data-productid="<?php echo $item['id']; ?>" 
                                                data-action="increase">+</button>
                                    </div>
                                </div>
                                <div class="col-md-2 text-end">
                                    <h6 class="mb-1"><?php echo $pesoFormatter->formatCurrency($item['subtotal'], 'PHP'); ?></h6>
<button type="button" class="btn btn-danger btn-sm remove-item" 
                                            data-productid="<?php echo $item['id']; ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
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
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-credit-card"></i> Proceed to Checkout
                        </button>
                    </div>
                </div>
            </div>
        </div>
        </form>
    <?php endif; ?>
</div>


<div class="modal fade" id="removeItemModal" tabindex="-1" aria-labelledby="removeItemModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="removeItemModalLabel">Confirm Removal</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to remove this item from the cart?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmRemoveBtn">Remove</button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
   
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
        updateSelectedTotals();
    });

   
    $('.quantity-input').change(function() {
        const productId = $(this).data('productid');
        const value = parseInt($(this).val());
        updateCartItem(productId, value);
        updateSelectedTotals();
    });


    $('.remove-item').click(function() {
        const productId = $(this).data('productid');
     
        $('#confirmRemoveBtn').data('productid', productId);
 
        $('#removeItemModal').modal('show');
    });

 
    $('#confirmRemoveBtn').click(function() {
        const productId = $(this).data('productid');
        $('#removeItemModal').modal('hide');
        removeCartItem(productId);
        updateSelectedTotals();
    });


    $('input[name="selected_products[]"]').change(function() {
        updateSelectedTotals();
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
                try {
                    const data = typeof response === 'string' ? JSON.parse(response) : response;
                    if(data && data.success) {
                        const itemCard = $(`.card:has(.quantity-input[data-productid="${productId}"])`);
                        itemCard.find('h6').text(formatCurrency(data.updated_item_subtotal));
                        updateOrderSummary(data.total_items, data.total_price);
                        updateHeaderCartCount(data.total_items);
                        updateSelectedTotals();
                    } else {
                        alert(data ? (data.message || 'Error updating item quantity') : 'Unknown error');
                    }
                } catch (e) {
                    alert('Error processing server response for update.');
                }
            },
            error: function() {
                alert('Error occurred while updating cart.');
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
                try {
                    const data = typeof response === 'string' ? JSON.parse(response) : response;
                    if(data && data.success) {
                        $(`.card:has(button[data-productid="${productId}"].remove-item)`).remove();
                        updateOrderSummary(data.total_items, data.total_price);
                        updateHeaderCartCount(data.total_items);
                        if (data.total_items === 0) {
                            $('.container > .row').hide();
                            $('.container > .alert.alert-info').show();
                        }
                        updateSelectedTotals();
                    } else {
                        alert(data ? (data.message || 'Error removing item from cart') : 'Unknown error');
                    }
                } catch (e) {
                    alert('Error processing server response for removal.');
                }
            },
            error: function() {
                alert('Error occurred while removing item.');
            }
        });
    }

    function updateOrderSummary(totalItems, totalPrice) {
        $('.col-md-4 .card-body .d-flex:nth-child(1) span:last-child').text(formatCurrency(totalPrice));
        $('.col-md-4 .card-body .d-flex:nth-child(4) strong:last-child').text(formatCurrency(totalPrice));
        if (totalItems === 0) {
            $('.col-md-4 .card').hide();
        } else {
            $('.col-md-4 .card').show();
        }
    }

    function updateHeaderCartCount(totalItems) {
        $('#cart-count-badge').text(totalItems);
    }

    function updateSelectedTotals() {
        let totalPrice = 0;
        let totalQuantity = 0;
        let selectedCount = 0;

        $('input[name="selected_products[]"]:checked').each(function() {
            const productId = $(this).val();
            const quantityInput = $(`.quantity-input[data-productid="${productId}"]`);
            const quantity = parseInt(quantityInput.val()) || 0;
            const itemCard = $(`.card:has(.quantity-input[data-productid="${productId}"])`);
            const priceText = itemCard.find('p.text-success').text().replace(/[₱,]/g, '');
            const price = parseFloat(priceText) || 0;

            totalPrice += price * quantity;
            totalQuantity += quantity;
            selectedCount++;
        });

        $('.col-md-4 .card-body .d-flex:nth-child(1) span:last-child').text(formatCurrency(totalPrice));
        $('.col-md-4 .card-body .d-flex:nth-child(4) strong:last-child').text(formatCurrency(totalPrice));

        if (selectedCount === 0) {
            $('.col-md-4 .card').hide();
        } else {
            $('.col-md-4 .card').show();
        }
    }

    function formatCurrency(amount) {
        const formattedAmount = parseFloat(amount).toFixed(2);
        return '₱' + formattedAmount;
    }

    
    updateSelectedTotals();
});
</script>

<?php template('footer.php'); ?>
