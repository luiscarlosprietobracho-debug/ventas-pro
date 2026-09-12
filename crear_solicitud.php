<?php
include_once("conexionpdo.php");
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 2) {
    header("location: login.php");
    exit();
}

$pdo=(new Database())->conectar();

if(isset($_POST['enviar'])){

    $consulta=$pdo->prepare("INSERT INTO solicitudes (idusuario,asunto,mensaje) VALUES (?,?,?)");

    $consulta->execute([
        $_SESSION['idusuario'],
        $_POST['asunto'],
        $_POST['mensaje']
    ]);

    echo "<script>alert('Solicitud enviada correctamente'); window.location='cliente.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Crear Solicitud</title>
<link rel="stylesheet" href="css/crear_solicitud.css">

</head>

<body>

<div class="container">

<div class="card">

<h2 style=" background: linear-gradient(to right, #FF512F 0%, #F09819 51%, #FF512F 100%);
            font-family: Arial, Helvetica, sans-serif;
            background-clip: text;              /* estándar */
           -webkit-background-clip: text;      /* Chrome / Edge */
            color: transparent; ">Enviar Solicitud / PQR</h2>

<form method="POST">

<label>Asunto</label>
<input class="form-control" type="text" name="asunto" placeholder="Ej: Problema con un pedido" required>

<br><br>

<label>Mensaje</label>
<textarea class="form-control" name="mensaje" rows="5" placeholder="Describe tu solicitud..." required></textarea>

<button class="btn1" type="submit" name="enviar">Enviar Solicitud</button>

</form>

<a href="cliente.php" class="volver">Volver al panel cliente</a>

</div>

</div>

</body>
</html>