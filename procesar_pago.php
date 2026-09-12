<?php
include_once("conexionpdo.php");
session_start();

if (!isset($_SESSION['carrito']) || count($_SESSION['carrito']) == 0) {
    header("location: cliente.php");
    exit();
}

$pdo = (new Database())->conectar();

$total = 0;
foreach ($_SESSION['carrito'] as $item) {
    $total += $item['precio'] * $item['cantidad'];
}

try {

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO ventas (idcliente, fecha, total) VALUES (?, NOW(), ?)");
    $stmt->execute([$_SESSION['idusuario'], $total]);

    $idventa = $pdo->lastInsertId();

    foreach ($_SESSION['carrito'] as $idproducto => $item) {

        $stmt = $pdo->prepare("INSERT INTO detalle_venta (idventa, idproducto, cantidad, precio) VALUES (?, ?, ?, ?)");
        $stmt->execute([$idventa, $idproducto, $item['cantidad'], $item['precio']]);

        $stmt = $pdo->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");
        $stmt->execute([$item['cantidad'], $idproducto]);
    }

    $pdo->commit();

    $_SESSION['carrito'] = [];

    echo "<script>
        alert('Pago registrado correctamente');
        window.location='cliente.php';
    </script>";

} catch(Exception $e){

    $pdo->rollBack();
    echo "Error en el pago: " . $e->getMessage();
}
?>