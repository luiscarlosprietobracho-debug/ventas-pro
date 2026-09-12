<?php
// ============================================
// 1. INICIAR CONFIGURACION Y SESION
// ============================================

// Se incluye el archivo de conexión a la base de datos (PDO)
include_once("conexionpdo.php");

// Se inicia la sesión para poder acceder a variables como $_SESSION
session_start();


// ============================================
// 2. VALIDAR QUE SEA ADMIN
// ============================================

// Se verifica que:
// - Exista la variable de sesión 'rol'
// - Y que el rol sea 1 (administrador)
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 1) {

    // Si no cumple, se redirige al login (protección de acceso)
    header("location: login.php");

    // Se detiene completamente la ejecución del script
    exit();
}


// ============================================
// 3. CONEXION A LA BASE DE DATOS
// ============================================

// Se crea la conexión usando la clase Database (PDO)
$contenedorPDO = (new Database())->conectar();


// ============================================
// 5. ELIMINAR PRODUCTO (CORREGIDO CON RELACION)
// ============================================

// Se verifica si viene el parámetro eliminar por GET
if (isset($_GET['eliminar'])) {

    // Se obtiene el ID del producto a eliminar y se convierte a entero (seguridad)
    $idEliminar = intval($_GET['eliminar']);

    try {
        // 🔥 IMPORTANTE:
        // Primero se eliminan los registros relacionados en detalle_venta
        // para evitar errores de clave foránea (FOREIGN KEY)
        $sql = $contenedorPDO->prepare("DELETE FROM detalle_venta WHERE idproducto=?");
        $sql->execute([$idEliminar]);

        // Luego se elimina el producto de la tabla productos
        $sql = $contenedorPDO->prepare("DELETE FROM productos WHERE id=?");
        $sql->execute([$idEliminar]);

    } catch (PDOException $e) {

        // En caso de error se muestra el mensaje
        echo "Error al eliminar: " . $e->getMessage();

        // Se detiene la ejecución
        exit();
    }

    // Se recarga la página para actualizar la lista
    header("location: administrador.php");
    exit();
}


// ============================================
// 6. PAGINACION
// ============================================

// Cantidad de productos por página
$porPagina = 5;

// Página actual (por defecto 1)
$pagina = 1;

// Se verifica si se envió la página por GET
if (isset($_GET['pagina'])) {

    // Se convierte a entero para evitar errores
    $pagina = intval($_GET['pagina']);

    // Se evita que sea menor a 1
    if ($pagina < 1) {
        $pagina = 1;
    }
}

// Se calcula desde qué registro iniciar la consulta
$inicio = ($pagina - 1) * $porPagina;


// ============================================
// 7. CONSULTA DE PRODUCTOS CON LIMITE
// ============================================

// Consulta SQL con LIMIT para paginación
$sql = $contenedorPDO->prepare("SELECT * FROM productos LIMIT :inicio, :cantidad");

// Se enlazan los parámetros como enteros (IMPORTANTE para LIMIT)
$sql->bindParam(':inicio', $inicio, PDO::PARAM_INT);
$sql->bindParam(':cantidad', $porPagina, PDO::PARAM_INT);

// Se ejecuta la consulta
$sql->execute();

// Se obtienen todos los productos como arreglo asociativo
$productos = $sql->fetchAll(PDO::FETCH_ASSOC);


// ============================================
// 8. CALCULO DE PAGINAS
// ============================================

// Se obtiene el total de registros en la tabla productos
$totalRegistros = $contenedorPDO->query("SELECT COUNT(*) FROM productos")->fetchColumn();

// Se calcula el total de páginas necesarias
$totalPaginas = ceil($totalRegistros / $porPagina);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel Administrador</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<!-- Estilos personalizados -->
<link rel="stylesheet" href="css/administrador.css">
</head>

<body>
 
<!-- HEADER -->
<header class="estilosheader">

<!-- Logo del sistema -->
<div class="container d-flex justify-content-center">
<img class="ContIMG text-center" src="img/VENTAS_PRO_remove.png"
class="img-fluid">
</div>
</header>


<!-- NAVBAR PRINCIPAL -->
<nav class="navbar navbar-expand-lg navbar-dark colorNavbar">
<div class="container-fluid px-4 justify-content-between">

<!-- TEXTO DE BIENVENIDA -->
<div class="d-flex flex-column">
<h1 class="titulo-navbar">
Bienvenido 
<span class="subtitulo-navbar">
Panel administrador, gestiona usuarios y productos fácilmente
</span>
</h1>
</div>

<a class="navbar-brand fw-bold d-flex align-items-center" href="cliente.php"></a>

<!-- ICONOS FUNCIONALES -->
<div class="d-flex align-items-center gap-4">

<!-- PERFIL -->
<div class="text-center eleven">
<i class="bi bi-person fs-5" style="color: orange;"></i>
<div style="font-size:12px;">
<a class="colorLinks" href="perfil_admin.php">
<span class="Mcategorias">Perfil</span>
</a>
</div>
</div>

<!-- PRODUCTOS -->
<div class="text-center eleven">
<i class="bi bi-box-seam fs-5" style="color: orange;"></i>
<div style="font-size:12px;">
<a class="colorLinks" href="productos.php">
<span class="Mcategorias">Gestión de Productos</span>
</a>
</div>
</div>

<!-- USUARIOS -->
<div class="text-center eleven">
<i class="bi bi-people-fill fs-5" style="color: orange;"></i>
<div style="font-size:12px;">
<a class="colorLinks" href="usuarios.php">
<span class="Mcategorias">Gestión de usuarios</span>
</a>
</div>
</div>

<!-- CERRAR SESION -->
<div class="text-center eleven">
<form action="login.php" method="post" style="display:inline;">
<button type="submit" name="cerrar_sesion" class="p-0 text-white border-0 bg-transparent">

<i class="bi bi-box-arrow-right fs-5" style="color: orange;"></i>

<div style="font-size:12px;" class="colorLinks">
<span class="Mcategorias">Cerrar sesión</span>
</div>

</button>
</form>
</div>

</div>
</div>
</div>
</nav>

<br><br>


<!-- BIENVENIDA ADMIN -->
<div class="container my-1 mx-0">

<div class="bienvenida-box conte d-flex align-items-center">

<!-- FOTO DEL USUARIO -->
<div class="foto-container me-3">
<img src="imagenes/<?php echo $_SESSION['Foto']; ?>" class="foto-perfil">
</div>

<!-- TEXTO -->
<div>
<h2 class="bienvenida-texto mb-1">
Bienvenido, <span><?php echo $_SESSION['nomusuario']; ?></span> 👋
</h2>

<p class="subtexto mb-0">
Panel administrador <strong>VentasPro</strong>
</p>
</div>

</div>
</div>


<div align="center">

<!-- TABLA DE PRODUCTOS -->
<table border="6">

<tr>
<th>ID</th>
<th>NOMBRE</th>
<th>PRECIO</th>
<th>STOCK</th>
<th>DESCRIPCION</th>
<th>FOTO</th>
<th>ACCIONES</th>
</tr>

<?php foreach ($productos as $p) { ?>
<tr align="center">

<!-- DATOS DEL PRODUCTO -->
<td style="color: orange"><?php echo $p['id']; ?></td>
<td><?php echo $p['nombre']; ?></td>
<td><?php echo $p['precio']; ?></td>
<td><?php echo $p['stock']; ?></td>
<td><?php echo $p['descripcion']; ?></td>

<!-- IMAGEN -->
<td>
<?php if (!empty($p['foto'])) { ?>
<img style="
width: 70px;
height: 70px;
object-fit: cover;
border-radius: 50%; 
border: 2px solid #ff7b00;
box-shadow: 0 0 8px rgba(255,123,0,0.5);
transition: 0.3s;
" src="imagenes/<?php echo $p['foto']; ?>">
<?php } ?>
</td>

<!-- ACCIONES -->
<td style="display:flex; gap:8px; justify-content:center; align-items:center;">

<!-- EDITAR -->
<a class="btn" href="editar_producto.php?id=<?=$p['id']?>">Editar</a>

<!-- ELIMINAR -->
<a class="btn2" href="administrador.php?eliminar=<?=$p['id']?>"
onclick="return confirm('¿Esta seguro de eliminar producto?')">
Eliminar
</a>

</td>

</tr>
<?php } ?>

</table>

<br>

<!-- PAGINACION -->
<div class="paginacion">
<?php
for ($i = 1; $i <= $totalPaginas; $i++) {

    if ($i == $pagina) {
        echo "<b>$i</b> ";
    } else {
        echo "<a href='administrador.php?pagina=$i'>$i</a> ";
    }
}
?>
</div>

<p class="paginastotal">Total de páginas: <?php echo $totalPaginas; ?></p>

<br><br>

<!-- REPORTE DE VENTAS -->
<h3 style="color: white">Generar reporte de ventas</h3>

<div class="reporte-container">

<form method="POST" action="reporte_ventas.php" target="_blank" class="reporte-form">

<!-- FECHA INICIO -->
<div class="grupo">
<label for="fecha_inicio">Fecha inicio:</label>
<input type="date" name="fecha_inicio" required>
</div>

<!-- FECHA FIN -->
<div class="grupo">
<label for="fecha_fin">Fecha fin:</label>
<input type="date" name="fecha_fin" required>
</div>

<!-- BOTON -->
<div class="grupo">
<input class="rpdf" type="submit" value="Generar Reporte PDF">
</div>

</form>
</div>

</div>

</body>
</html>