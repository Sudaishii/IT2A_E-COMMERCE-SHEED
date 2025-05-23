<?php include 'helpers/functions.php'; ?>
<?php template('header.php'); ?>
<?php

use Rasheed\MiniFrameworkStore\Models\Category;
use Rasheed\MiniFrameworkStore\Models\Product;
use Carbon\Carbon;

$categories = new Category();
$product = new Product();

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $image = $_FILES['image'];

    // Validate and process the image file
    if ($image['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($image["name"]);
        move_uploaded_file($image["tmp_name"], $targetFile);
    }

    // Insert the product into the database
    $product->insert([
        'name' => $name,
        'description' => $description,
        'price' => $price,
        'slug' => strtolower(str_replace(' ', '-', $name)),
        'image_path' => $targetFile,
        'category_id' => $category,
        'created_at' => Carbon::now('Asia/Manila'),
        'updated_at' => Carbon::now()
    ]);

    $message = "Product added successfully!";
}

?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 my-5">
            <h1 class="text-center">Add Product</h1>
            <p class="text-center">Fill in the details below to add a new product.</p>
            <?php if (isset($message)): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            <form action="add-product.php" method="POST" enctype="multipart/form-data">
                <div class="form-group my-5">
                    <div class="mb-3">
                        <label for="product-name">Product Name</label>
                        <input type="text" class="form-control" id="product-name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="product-name">Description</label>
                        <textarea class="form-control" id="product-name" name="description" rows="10"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="product-name">Price</label>
                        <input type="text" class="form-control" id="product-name" name="price" required> 
                    </div>
                    <div class="mb-3">
                        <label for="product-name">Category</label>
                        <select class="form-select" aria-label="Default select example" name="category">
                            <option selected>Select category</option>
                            <?php foreach($categories->getAll() as $category): ?>
                            <option value="<?php echo $category['id']; ?>"><?php echo $category['category_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="formFile" class="form-label">Image</label>
                        <input class="form-control" type="file" id="formFile" name="image" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary" type="submit" name="submit">Add Product</button>
                        </div>
                    </div>
                </div>
            </form>
         </div>
    </div>
</div>

<?php template('footer.php'); ?>