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
    <script src="assets/js/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            // Add to cart functionality
            $('.add-to-cart').click(function(e) {
                e.preventDefault();
                const productId = $(this).data('productid');
                const quantity = $(this).data('quantity');

                $.ajax({
                    url: 'cart-process.php',
                    method: 'POST',
                    data: {
                        action: 'add',
                        product_id: productId,
                        quantity: quantity
                    },
                    success: function(response) {
                        const data = JSON.parse(response);
                        if(data.success) {
                            alert('Product added to cart successfully!');
                            // Update cart count
                            $('.badge').text(data.cart_count);
                        } else {
                            alert('Error adding product to cart');
                        }
                    },
                    error: function() {
                        alert('Error occurred while processing your request');
                    }
                });
            });
        });
    </script>
</body>
</html>