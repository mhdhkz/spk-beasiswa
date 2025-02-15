<?php
$host = "localhost";
$user = "app_spk";
$pass = "kelompokkkpguabeban";
$db   = "spk_beasiswa";

$connection = new mysqli($host, $user, $pass, $db);

if ($connection->connect_error) {
    die("<h3>ERROR: Koneksi database gagal! " . $connection->connect_error . "</h3>");
}

$_PAGE = isset($_GET["page"]) ? htmlspecialchars($_GET["page"]) : "home";

function page($page) {
    return "page/" . basename($page) . ".php";
}

function alert($msg, $to = null) {
    $to = $to ? htmlspecialchars($to, ENT_QUOTES, 'UTF-8') : htmlspecialchars($_SERVER["PHP_SELF"], ENT_QUOTES, 'UTF-8');
    return "<script>alert('" . addslashes($msg) . "'); window.location='" . $to . "';</script>";
}
