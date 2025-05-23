<?php include 'helpers/functions.php'; ?>
<?php template('header.php'); ?>
<?php

use Rasheed\MiniFrameworkStore\Models\User;
use Carbon\Carbon;

$user = new User();
$registrationSuccess = false;
$emailExistsError = '';

if(isset($_POST['submit'])) {
    $name = $_POST['full-name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $registeredUserId = $user->register([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT), // Hashing the password
            'address' => null,
            'phone' => null,
            'birthdate' => null,
            'created_at' => Carbon::now('Asia/Manila'),
            'updated_at' => Carbon::now('Asia/Manila')
        ]);

        if ($registeredUserId) {
            $registrationSuccess = true;
        }

    } catch (PDOException $e) {
        // Check for duplicate entry error (SQLSTATE 23000 and MySQL specific error 1062)
        if ($e->getCode() === '23000' && strpos($e->getMessage(), '1062 Duplicate entry') !== false) {
             if (strpos($e->getMessage(), "for key 'email'") !== false) { // Specifically check for email key
                $emailExistsError = 'This email address is already registered. Please use a different email or log in.';
            } else {
                 // Handle other potential duplicate entry errors if necessary
                $emailExistsError = 'A duplicate entry was found. Please check your information.';
            }
        } else {
            // Handle other PDO exceptions (e.g., database connection issues)
            // Log the actual error for debugging, but show a generic message to the user
            error_log('Database error during registration: ' . $e->getMessage());
            $emailExistsError = 'An error occurred during registration. Please try again later.';
        }
    }
}

// Redirect logged-in users away from the registration page
if(isset($_SESSION['user']) && !empty($_SESSION['user'])) {
    header('Location: dashboard.php'); // Or index.php, depending on where logged-in users should go
    exit;
}

?>

<div class="container">
    <div class="row align-items-center">
        <div class="col mt-5 mb-5">
            <h1 class="text-center">Register</h1>
            
            <?php if ($registrationSuccess): ?>
                <div class="alert alert-success text-center" role="alert">
                    You have successfully registered! You may now <a href="login.php" class="alert-link">login</a>
                </div>
            <?php endif; ?>

            <?php if ($emailExistsError): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $emailExistsError; ?>
                </div>
            <?php endif; ?>

            <form style="width: 400px; margin: auto;" action="register.php" method="POST">
                <div class="mb-3">
                    <label for="full-name" class="form-label">Name</label>
                    <input name="full-name" type="text" class="form-control" id="full-name" aria-describedby="full-name" required>
                </div>
                <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">Email address</label>
                    <input name="email" type="email" class="form-control <?php echo $emailExistsError ? 'is-invalid' : ''; ?>" id="exampleInputEmail1" aria-describedby="emailHelp" required>
                    <?php if ($emailExistsError): ?>
                         <div class="invalid-feedback">
                            <?php echo $emailExistsError; ?>
                         </div>
                    <?php else: ?>
                         <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="exampleInputPassword1" class="form-label">Password</label>
                    <input name="password" type="password" class="form-control" id="exampleInputPassword1" required>
                </div>
                <button type="submit" name="submit" class="btn btn-primary">Register</button>
            </form>
        </div>
    </div>
</div>

<?php template('footer.php'); ?>