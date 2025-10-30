<?php
require 'functions.php';

// Jika sudah login, redirect ke dashboard
if (isLoggedIn() && isActive()) {
    header('Location: dashboard.php');
} else {
    // Jika belum login, redirect ke login
    header('Location: login.php');
}
exit;
?>
