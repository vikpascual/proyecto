<?php
if(!isset($_SESSION)) 
{ 
    session_start(); 
} 

if ($_SESSION['logueado'] != TRUE){
    header('Location: entrar.php');
}
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
    $location = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    header('Location: ' . $location);
    exit;
}
include 'includes/config/db.php';
include 'includes/funciones.php';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="img/logo.png">
    <title>Admin Oasis</title>
    <link rel="stylesheet" type="text/css" href="css/principal.css">
    <link href="https://fonts.googleapis.com/css?family=Sen&display=swap" rel="stylesheet">
    <script>
        function enviar(nombre) {
            document.forms[nombre].submit();
        }
    </script>
</head>
<body>
<header>
    <div class="cabecera logo"><a href="index.php"><img src="img/logo.png" width="100%"></a></div>
    <div class="cabecera"><a href="index.php"><h1>Admin Oasis</h1><a href="index.php"></div>
    <div class="cabecera logout"><a href="logout.php">Salir</a></div>
</header>