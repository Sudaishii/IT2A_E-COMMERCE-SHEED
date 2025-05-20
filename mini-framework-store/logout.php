<?php

session_start();

unset($_SESSION['user']); // Unset the user session variable

header('Location: login.php'); // Redirect to the login page
exit; // Ensure no further code is executed after the redirect