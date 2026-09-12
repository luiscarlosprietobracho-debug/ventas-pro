<?php
include_once("conexionpdo.php");
session_start();

// ============================================
// VALIDAR ADMIN
// ============================================
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 1) {
    header("location: login.php");
    exit();
}

$pdo = (new Database())->conectar();

// ============================================
// VALIDAR ID
// ============================================
if (!isset($_GET['id'])) {
    echo "ID no recibido";
    exit();
}

$id = intval($_GET['id']);

// ============================================
// OBTENER PRODUCTO
// ============================================
$sql = $pdo->prepare("SELECT * FROM productos WHERE id=?");
$sql->execute([$id]);
$producto = $sql->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    echo "Producto no encontrado";
    exit();
}

// ============================================
// ACTUALIZAR PRODUCTO
// ============================================
if (isset($_POST['actualizar'])) {

    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $descripcion = $_POST['descripcion'];

    // ============================================
// ACTUALIZAR PRODUCTO
// ============================================
if (isset($_POST['actualizar'])) {

    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $descripcion = $_POST['descripcion'];

    // Obtener imagen actual
    $consulta = $pdo->prepare("SELECT foto FROM productos WHERE id=?");
    $consulta->execute([$id]);
    $datos = $consulta->fetch(PDO::FETCH_ASSOC);
    $imagenActual = $datos['foto'];

    // Verificar si se sube nueva imagen
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {

        $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nombreArchivo = "producto_" . time() . "." . $extension;
        $ruta = "imagenes/" . $nombreArchivo;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $ruta)) {

            // Eliminar imagen anterior (opcional)
            if (!empty($imagenActual) && file_exists("imagenes/" . $imagenActual)) {
                unlink("imagenes/" . $imagenActual);
            }

            $imagenActual = $nombreArchivo;
        }
    }

    // Actualizar con imagen incluida
    $sql = $pdo->prepare("UPDATE productos SET nombre=?, precio=?, stock=?, descripcion=?, foto=? WHERE id=?");

    $sql->execute([$nombre, $precio, $stock, $descripcion, $imagenActual, $id]);

    header("location: administrador.php");
    exit();
}
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Editar Producto</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/editar_producto.css">

</head>

<body>

<div class="contenedor-producto">

    <div class="card-producto">

        <h2 class="titulo-card">Editar Producto</h2>

        <form class="form-producto" method="POST" enctype="multipart/form-data">

            <label>Nombre:</label>
            <input type="text" name="nombre" value="<?= $producto['nombre'] ?>" required>

            <label>Precio:</label>
            <input type="number" step="0.01" name="precio" value="<?= $producto['precio'] ?>" required>

            <label>Stock:</label>
            <input type="number" name="stock" value="<?= $producto['stock'] ?>" required>

            <label>Descripción:</label>
            <textarea name="descripcion" required><?= $producto['descripcion'] ?></textarea>

            <input type="submit" name="actualizar" value="Actualizar Producto" class="btn-submit">

            <label>Imagen actual:</label><br>
            <img src="imagenes/<?= $producto['foto'] ?>" width="100"><br><br>

            <label>Nueva imagen:</label>
            <input type="file" name="foto">

        </form>

        <div class="text-center">
        <a href="administrador.php" class="link-login highlight d-block mb-2 text-decoration-none ">
            <span class="cambio">Volver al panel admin</span>
        </a>
    </div>

    </div>

</div>

</body>
</html>