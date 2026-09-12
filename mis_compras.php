<?php
include_once("conexionpdo.php");
session_start();

// validar cliente
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 2) {
    header("location: login.php");
    exit();
}

$pdo = (new Database())->conectar();

// traer ventas del cliente
$stmt = $pdo->prepare("SELECT * FROM ventas WHERE idcliente=? ORDER BY fecha DESC");
$stmt->execute([$_SESSION['idusuario']]);
$ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<title>Mis Compras</title>
<link rel="stylesheet" href="css/mis_compras.css">

</head>

<body>

<h2 class="titulo">Historial de Compras</h2>

<?php if(count($ventas) > 0): ?>

<?php foreach($ventas as $venta): ?>

<div class="card">

<h3>Compra #<?= $venta['id'] ?></h3>

<p>
<strong>Fecha:</strong> <?= $venta['fecha'] ?><br>
<strong>Total:</strong> $ <?= number_format($venta['total'],2) ?>
</p>

<table class="tabla">
<tr>
<th>Producto</th>
<th>Cantidad</th>
<th>Precio</th>
<th>Subtotal</th>
</tr>

<?php
$stmt = $pdo->prepare("
SELECT p.nombre, dv.cantidad, dv.precio
FROM detalle_venta dv
JOIN productos p ON dv.idproducto = p.id
WHERE dv.idventa=?
");

$stmt->execute([$venta['id']]);
$detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach($detalles as $item):
$subtotal = $item['cantidad'] * $item['precio'];
?>

<tr>
<td><?= htmlspecialchars($item['nombre']) ?></td>
<td><?= $item['cantidad'] ?></td>
<td>$<?= number_format($item['precio'],2) ?></td>
<td>$<?= number_format($subtotal,2) ?></td>
</tr>

<?php endforeach; ?>

</table>

</div>

<?php endforeach; ?>

<?php else: ?>

<p class="vacio">Aún no has realizado compras.</p>

<?php endif; ?>

<div class="contenedor-boton">
<a href="cliente.php" class="btn">Volver al panel</a>
</div>

</body>
</html>