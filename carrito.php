<?php
// Incluye el archivo donde está la conexión a la base de datos
include_once("conexionpdo.php");

// Inicia la sesión para poder usar variables como $_SESSION
session_start();

// =====================
// VALIDAR CLIENTE
// =====================

// Verifica si NO existe la sesión 'rol' o si el rol es diferente de 2 (cliente)
// Si no es cliente, lo manda al login
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 2) {
    header("location: login.php"); // Redirección
    exit(); // Detiene la ejecución del código
}

// Crea la conexión a la base de datos usando la clase Database
$pdo = (new Database())->conectar();

// =====================
// Inicializar carrito
// =====================

// Si el carrito no existe en la sesión, lo crea como un arreglo vacío
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// =====================
// AGREGAR PRODUCTO
// =====================

// Verifica si se envió el botón "agregar" desde un formulario
if (isset($_POST['agregar'])) {

    // Guarda el id del producto enviado
    $idproducto = $_POST['idproducto'];

    // Guarda la cantidad seleccionada
    $cantidad = $_POST['cantidad'];

    // Prepara una consulta para buscar el producto en la BD
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");

    // Ejecuta la consulta pasando el id del producto
    $stmt->execute([$idproducto]);

    // Obtiene el producto como un arreglo asociativo
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica si el producto existe en la BD
    if ($producto) {

        // Si el producto ya está en el carrito
        if (isset($_SESSION['carrito'][$idproducto])) {

            // Aumenta la cantidad existente
            $_SESSION['carrito'][$idproducto]['cantidad'] += $cantidad;

        } else {

            // Si no existe, lo agrega al carrito
            $_SESSION['carrito'][$idproducto] = [
                'nombre' => $producto['nombre'],   // Nombre del producto
                'precio' => $producto['precio'],   // Precio del producto
                'cantidad' => $cantidad            // Cantidad elegida
            ];
        }
    }

    // Redirige al carrito después de agregar el producto
    header("Location: carrito.php");
    exit();
}

// =====================
// ELIMINAR PRODUCTO
// =====================

// Verifica si se envió un id por la URL para eliminar
if (isset($_GET['eliminar'])) {

    // Guarda el id del producto a eliminar
    $idEliminar = $_GET['eliminar'];

    // Elimina ese producto del carrito
    unset($_SESSION['carrito'][$idEliminar]);

    // Redirige nuevamente al carrito
    header("Location: carrito.php");
    exit();
}

// =====================
// CALCULAR TOTAL
// =====================

// Inicializa el total en 0
$total = 0;

// Recorre todos los productos del carrito
foreach ($_SESSION['carrito'] as $item) {

    // Calcula total sumando precio * cantidad
    $total += $item['precio'] * $item['cantidad'];
}

// =====================
// CONFIRMAR COMPRA
// =====================

// Verifica si se presionó "confirmar" y el carrito no está vacío
if (isset($_POST['confirmar']) && $total > 0) {

    try {

        // Inicia una transacción (para asegurar que todo se guarde correctamente)
        $pdo->beginTransaction();

        // Inserta la venta en la tabla ventas
        $stmt = $pdo->prepare("INSERT INTO ventas (idcliente, fecha, total) VALUES (?, NOW(), ?)");

        // Ejecuta la consulta con el id del usuario y el total
        $stmt->execute([$_SESSION['idusuario'], $total]);

        // Obtiene el id de la venta recién creada
        $idventa = $pdo->lastInsertId();

        // Recorre cada producto del carrito
        foreach ($_SESSION['carrito'] as $idproducto => $item) {

            // Inserta cada producto en detalle_venta
            $stmt = $pdo->prepare("INSERT INTO detalle_venta (idventa, idproducto, cantidad, precio) VALUES (?, ?, ?, ?)");

            $stmt->execute([$idventa, $idproducto, $item['cantidad'], $item['precio']]);

            // Actualiza el stock restando la cantidad comprada
            $stmt = $pdo->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");
            $stmt->execute([$item['cantidad'], $idproducto]);
        }

        // Confirma los cambios en la base de datos
        $pdo->commit();

        // Vacía el carrito después de la compra
        $_SESSION['carrito'] = [];

        // Muestra mensaje de éxito y redirige
        echo "<script>
                alert('Compra realizada con éxito');
                window.location='cliente.php';
              </script>";
        exit();

    } catch (Exception $e) {

        // Si ocurre un error, cancela todos los cambios
        $pdo->rollBack();

        // Muestra el error
        echo "Error en la compra: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Carrito de Compras</title>

    <!-- Archivo de estilos CSS -->
    <link rel="stylesheet" href="css/carrito.css">
</head>

<body>

<!-- Título principal -->
<h2 align="center">Carrito - Finalizar Compra</h2>

<!-- Tabla donde se muestran los productos -->
<table border="0" align="center" cellpadding="10">
<tr>
    <th>Producto</th>
    <th>Precio</th>
    <th>Cantidad</th>
    <th>Subtotal</th>
    <th>Acción</th>
</tr>

<?php if (count($_SESSION['carrito']) > 0): ?>
<!-- Verifica si el carrito tiene productos -->

<?php foreach ($_SESSION['carrito'] as $id => $item): 
    // Calcula el subtotal de cada producto
    $subtotal = $item['precio'] * $item['cantidad'];
?>

<tr align="center">

    <!-- Muestra el nombre del producto de forma segura -->
    <td><?= htmlspecialchars($item['nombre']) ?></td>

    <!-- Muestra el precio con formato -->
    <td>$ <?= number_format($item['precio'],2) ?></td>

    <!-- Muestra la cantidad -->
    <td><?= $item['cantidad'] ?></td>

    <!-- Muestra el subtotal -->
    <td>$ <?= number_format($subtotal,2) ?></td>

    <!-- Link para eliminar el producto -->
    <td>
        <a href="carrito.php?eliminar=<?= $id ?>">Eliminar</a>
    </td>
</tr>

<?php endforeach; ?>

<!-- Fila donde se muestra el total -->
<tr align="center">
    <td colspan="3"><strong>Total</strong></td>
    <td colspan="2"><strong>$ <?= number_format($total,2) ?></strong></td>
</tr>

</table>

<div align="center">

<!-- Contenedor del botón de PayPal -->
<div style="display:flex; justify-content:center;">
    <div id="paypal-button-container"></div>
</div>

<!-- Script de PayPal -->
<script src="https://www.paypal.com/sdk/js?client-id=AetV2ByTQkAbyhN4OlYk9tWJAmjGwtQ-I5fcBfFOgaa63YHAsTQB8PMQT9ImTsieb5D3iJUtEmNzg4sO&currency=USD"></script>

<script>
// Configuración del botón de PayPal
paypal.Buttons({

createOrder: function(data, actions) {
    // Crea la orden con el total del carrito
    return actions.order.create({
        purchase_units: [{
            amount: {
                value: '<?php echo $total; ?>'
            }
        }]
    });
},

onApprove: function(data, actions) {
    // Se ejecuta cuando el pago es aprobado
    return actions.order.capture().then(function(details) {

        // Muestra mensaje con el nombre del pagador
        alert("Pago completado por " + details.payer.name.given_name);

        // Redirige para procesar el pago
        window.location.href = "procesar_pago.php";
    });
}

}).render('#paypal-button-container'); // Renderiza el botón
</script>

</div>

<br>

<?php else: ?>

<!-- Si el carrito está vacío -->
<tr align="center">
    <td colspan="5">El carrito está vacío</td>
</tr>
</table>

<?php endif; ?>

<br><br>

<div align="center">

    <!-- Link para seguir comprando -->
    <a href="productos_cliente.php" style=" text-decoration: none; " >Seguir Comprando</a>

    <br><br>

    <!-- Link para volver al panel del cliente -->
    <a href="cliente.php" style=" text-decoration: none; ">Volver al Panel Cliente</a>

</div>

</body>
</html>
