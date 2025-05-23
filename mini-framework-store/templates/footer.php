    <footer class="footer mt-auto py-3">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>About Us</h5>
                    <p>Your trusted online shopping destination for quality products and excellent service.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-light">Home</a></li>
                        <li><a href="cart.php" class="text-light">Shopping Cart</a></li>
                        <li><a href="my-account.php" class="text-light">My Account</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact Us</h5>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-envelope"></i> support@onlinestore.com</li>
                        <li><i class="bi bi-telephone"></i> +63 123 456 7890</li>
                        <li><i class="bi bi-geo-alt"></i> Manila, Philippines</li>
                    </ul>
                </div>
            </div>
            <hr class="mt-4 mb-3" style="border-color: var(--beige-light);">
            <div class="row">
                <div class="col-md-12 text-center">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> Online Store. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Add to cart functionality
            $('.add-to-cart').click(function(e) {
                e.preventDefault();
                console.log('Add to cart clicked'); // Debug log
                
                const productId = $(this).data('productid');
                // Get quantity from input if it exists (product page) or use default 1 (index page)
                const quantity = $('#quantity').length ? $('#quantity').val() : 1;
                
                console.log('Product ID:', productId); // Debug log
                console.log('Quantity:', quantity); // Debug log

                $.ajax({
                    url: 'cart-process.php',
                    method: 'POST',
                    data: {
                        action: 'add',
                        product_id: productId,
                        quantity: quantity
                    },
                    success: function(response) {
                        try {
                            // Try to parse response if it's a string, otherwise assume it's already an object
                            const data = typeof response === 'string' ? JSON.parse(response) : response;
                            console.log('Server response:', data); // Debug log
                            
                            if(data && data.success) {
                                // Update cart count in header
                                $('#cart-count-badge').text(data.total_items);
                                console.log('Updated cart count badge with value:', data.total_items);
                                
                                // Show success message (optional - can be removed if you don't want alerts)
                                // alert(data.message || 'Product added to cart successfully!');

                                // You can add a visual confirmation here, like a temporary highlight or a small notification
                                // For now, we will remove the alert as requested.
                                if (data.message) {
                                    console.log('Success message:', data.message);
                                }


                            } else if(data && data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                // Handle cases where success is false or data is not as expected
                                console.error('Error adding to cart:', data);
                                // alert(data ? (data.message || 'Error adding product to cart') : 'Unknown error'); // Optional alert
                            }
                        } catch (e) {
                            console.error('Error processing response:', e);
                            // alert('Error processing server response'); // Remove this alert
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', {xhr, status, error});
                        alert('Error occurred while processing your request');
                    }
                });
            });
        });
    </script>
</body>
</html>