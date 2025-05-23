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
        <div class="row">
            <!-- Cart Items -->
            <div class="col-md-8">
                <?php foreach ($cartItems as $item): ?>
                    <div class="card mb-3">
                        <div class="card-body">
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

<!-- Remove Item Confirmation Modal -->
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
        // Store the product ID on the confirm button data attribute
        $('#confirmRemoveBtn').data('productid', productId);
        // Show the confirmation modal
        $('#removeItemModal').modal('show');
    });

    // Handle click on the confirm remove button in the modal
    $('#confirmRemoveBtn').click(function() {
        // Get the product ID from the confirm button's data attribute
        const productId = $(this).data('productid');
        // Hide the modal
        $('#removeItemModal').modal('hide');
        // Call the remove function
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
                try {
                    // Try to parse response if it's a string, otherwise assume it's already an object
                    const data = typeof response === 'string' ? JSON.parse(response) : response;
                    console.log('Update cart response:', data);

                    if(data && data.success) {
                        // Find the updated item row and update its subtotal
                        const itemCard = $(`.card:has(.quantity-input[data-productid="${productId}"])`);
                        itemCard.find('h6').text(formatCurrency(data.updated_item_subtotal));

                        // Update total summary and header cart count
                        updateOrderSummary(data.total_items, data.total_price);
                        updateHeaderCartCount(data.total_items);

                    } else {
                         console.error('Error updating cart item:', data);
                        alert(data ? (data.message || 'Error updating item quantity') : 'Unknown error');
                    }
                } catch (e) {
                    console.error('Error processing update cart response:', e);
                    alert('Error processing server response for update.');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX update error:', {xhr, status, error});
                alert('Error occurred while updating cart.');
            }
        });
    }

    function removeCartItem(productId) {
        // Confirm removal with the user
        // if (!confirm('Are you sure you want to remove this item from the cart?')) {
        //     return; // Do nothing if user cancels
        // }

        $.ajax({
            url: 'cart-process.php',
            method: 'POST',
            data: {
                action: 'remove',
                product_id: productId
            },
            success: function(response) {
                try {
                    // Try to parse response if it's a string, otherwise assume it's already an object
                    const data = typeof response === 'string' ? JSON.parse(response) : response;
                    console.log('Remove item response:', data);

                    if(data && data.success) {
                        // Remove the item's HTML element from the page
                        $(`.card:has(button[data-productid="${productId}"].remove-item)`).remove();

                        // Update total summary and header cart count
                        updateOrderSummary(data.total_items, data.total_price);
                        updateHeaderCartCount(data.total_items);

                        // Check if the cart is empty after removal
                        if (data.total_items === 0) {
                             // Hide the item list and order summary, show the empty cart message
                            $('.container > .row').hide();
                            $('.container > .alert.alert-info').show();
                        }

                    } else {
                        console.error('Error removing item:', data);
                        alert(data ? (data.message || 'Error removing item from cart') : 'Unknown error');
                    }
                } catch (e) {
                    console.error('Error processing remove item response:', e);
                     alert('Error processing server response for removal.');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX remove error:', {xhr, status, error});
                alert('Error occurred while removing item.');
            }
        });
    }

    // Function to update the order summary total
    function updateOrderSummary(totalItems, totalPrice) {
        // Update the subtotal and total price displays in the order summary card
        $('.col-md-4 .card-body .d-flex:nth-child(1) span:last-child').text(formatCurrency(totalPrice)); // Assuming first d-flex is subtotal
        $('.col-md-4 .card-body .d-flex:nth-child(4) strong:last-child').text(formatCurrency(totalPrice)); // Assuming fourth d-flex is total

         if (totalItems === 0) {
             $('.col-md-4 .card').hide(); // Hide the order summary card if cart is empty
         } else {
             $('.col-md-4 .card').show(); // Show if items are present
         }
    }

     // Function to update the header cart count badge
    function updateHeaderCartCount(totalItems) {
         $('#cart-count-badge').text(totalItems);
         console.log('Header cart count updated to:', totalItems);
    }

    // Helper function for currency formatting (basic)
    function formatCurrency(amount) {
        // This is a basic example, you might need a more robust function for different currencies/locales
        const formattedAmount = parseFloat(amount).toFixed(2);
        return '₱' + formattedAmount;
    }

});
</script>

<?php template('footer.php'); ?>