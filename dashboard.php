<?php
session_start();
 
$role     = $_POST['role']     ?? $_SESSION['role']     ?? '';
$username = $_POST['username'] ?? $_SESSION['username'] ?? '';
 
if(empty($role)) {
    header("Location: login.php");
    exit();
}
 
$_SESSION['role']     = $role;
$_SESSION['username'] = $username;

$modules = [
    'pendaftaran' => 'module/pendaftaran/index.php',
    'dokter'      => 'module/dokter/index.php',
    'apoteker'    => 'module/apoteker/index.php',
    'kasir'       => 'module/kasir/index.php'
];
 
if(isset($modules[$role])) {
    header("Location: " . $modules[$role]);
    exit();
} else {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>
