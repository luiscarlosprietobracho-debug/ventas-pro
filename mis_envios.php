<?php
include_once("conexionpdo.php");
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 2) {
    header("location: login.php");
    exit();
}

$id_cliente = $_SESSION['idusuario'];

$database = new Database();
$conexion = $database->conectar();

$sql = "SELECT ventas.id, ventas.fecha, ventas.total
        FROM ventas
        WHERE ventas.idcliente = :id_cliente";

$stmt = $conexion->prepare($sql);
$stmt->execute([':id_cliente' => $id_cliente]);
$ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Mis Envíos</title>
<link rel="stylesheet" href="css/mis_envios.css">

</head>

<body>

<h2 class="titulo">Mis Envíos</h2>

<div class="contenedor">

<table class="tabla">

<tr>
    <th>ID VENTA</th>
    <th>FECHA</th>
    <th>TOTAL</th>
    <th>ESTADO</th>
</tr>

<?php foreach ($ventas as $venta): ?>

<?php
$sqlEnvio = "SELECT * FROM envios 
             WHERE envios.id_venta = :id_venta 
             ORDER BY envios.fecha_actualizacion DESC 
             LIMIT 1";

$stmtEnvio = $conexion->prepare($sqlEnvio);
$stmtEnvio->execute([':id_venta' => $venta['id']]);
$envio = $stmtEnvio->fetch(PDO::FETCH_ASSOC);
?>

<tr>

<td><?= $venta['id']; ?></td>
<td><?= $venta['fecha']; ?></td>
<td>$<?= $venta['total']; ?></td>

<td>
<?php
if ($envio) {

    switch($envio['estado']) {

        case 'pendiente':
            echo "<span class='pendiente'>Pendiente</span>";
            break;

        case 'preparando':
            echo "<span class='preparando'>Preparando</span>";
            break;

        case 'en_camino':
            echo "<span class='camino'>En camino</span>";
            break;

        case 'entregado':
            echo "<span class='entregado'>Entregado</span>";
            break;
    }

} else {
    echo "<span class='pendiente'>Pendiente</span>";
}
?>
</td>

</tr>

<?php endforeach; ?>

</table>

</div>

<div class="contenedor-boton">
    <a href="cliente.php" class="btn">Volver</a>
</div>

</body>
</html>