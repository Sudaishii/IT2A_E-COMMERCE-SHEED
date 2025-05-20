<?php include 'helpers/functions.php'; ?>
<?php template('header.php'); ?>
<?php

use Rasheed\MiniFrameworkStore\Models\User;
use Carbon\Carbon;

$user = new User();

if(isset($_POST['submit'])) {
    $registered = $user->register([
        'name' => $_POST['full-name'],
        'email' => $_POST['email'],
        'password' => $_POST['password'],
        'created_at' => Carbon::now('Asia/Manila'),
        'updated_at' => Carbon::now('Asia/Manila')
    ]);
}

if(isset($_SESSION['user']) && !empty($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}

?>

<div class="container">
    <div class="row align-items-center">
        <div class="col mt-5 mb-5">
            <h1 class="text-center">Register</h1>
            <h3 class="text-center"><?php echo isset($registered) ? 'You have successfully registered! You may now <a href="login.php">login</a>' : ''; ?></h3>
            <form style="width: 400px; margin: auto;" action="register.php" method="POST">
                <div class="mb-3">
                    <label for="full-name" class="form-label">Name</label>
                    <input name="full-name" type="text" class="form-control" id="full-name" aria-describedby="full-name">
                    <div id="full-name" class="form-text">We'll never share your email with anyone else.</div>
                </div>
                <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">Email address</label>
                    <input name="email" type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
                    <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
                </div>
                <div class="mb-3">
                    <label for="exampleInputPassword1" class="form-label">Password</label>
                    <input name="password" type="password" class="form-control" id="exampleInputPassword1">
                </div>
                <button type="submit" name="submit" class="btn btn-primary">Register</button>
            </form>
        </div>
    </div>
</div>

<?php template('footer.php'); ?>