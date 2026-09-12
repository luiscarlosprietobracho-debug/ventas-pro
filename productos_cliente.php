<?php
// ==========================================================
// 1. INICIAR SESIÓN Y CONEXIÓN
// ==========================================================

// Se incluye el archivo de conexión PDO a la base de datos
include_once("conexionpdo.php");

// Se inicia la sesión para poder acceder a variables $_SESSION
session_start();

// ==========================================================
// 2. VALIDAR QUE SEA CLIENTE
// ==========================================================

// Protección de acceso:
// - Se verifica que exista la variable de sesión 'rol'
// - Solo se permite acceso a rol = 2 (Cliente)
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 2) {
    header("location: login.php");
    exit(); // Detener ejecución si no es cliente
}

// ==========================================================
// 3. CONEXIÓN A BASE DE DATOS
// ==========================================================

// Se instancia la clase Database y se conecta usando PDO
$pdo = (new Database())->conectar();

// ==========================================================
// 4. TRAER TODOS LOS PRODUCTOS
// ==========================================================

// Se ejecuta una consulta simple para traer todos los productos
// fetchAll(PDO::FETCH_ASSOC) devuelve un array asociativo
$productos = $pdo->query("SELECT * FROM productos")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Ventas-Pro - Catálogo</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Iconos Bootstrap -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<!-- Estilos personalizados -->
<link rel="stylesheet" href="css/cliente.css">
<link rel="stylesheet" href="css/clientes.css">
<link rel="stylesheet" href="css/productos_cliente.css">

</head>

<body class="bg-light">

<!-- ========================================================== -->
<!-- HEADER CON LOGO -->
<!-- ========================================================== -->
<header class="fondo py-3">
    <div class="container text-center">
        <img class="ContIMG" src="img/VENTAS_PRO_remove.png"
             class="img-fluid"
             style="max-height:140px;">
    </div>
</header>

<!-- ========================================================== -->
<!-- BARRA SUPERIOR CON INFO Y LINKS -->
<!-- ========================================================== -->
<div class="text-white py-2 px-4 d-flex justify-content-between Superior">
    <div>
        <i class="bi bi-geo-alt"></i> Envios gratis
    </div>

    <div>
        <a href="nosotros.php" class="text-white me-3 text-decoration-none">Sobre nosotros</a>
        <a href="ayuda.php" class="text-white text-decoration-none">Ayuda</a>
    </div>
</div>

<!-- ========================================================== -->
<!-- NAVBAR PRINCIPAL -->
<!-- ========================================================== -->
<nav class="navbar navbar-expand-lg navbar-dark colorNavbar">
    <div class="container-fluid px-4">

        <!-- Logo y nombre de la tienda -->
        <a class="navbar-brand fw-bold d-flex align-items-center" href="cliente.php">
            <i class="bi bi-box-seam fs-3 me-2"></i>
            <h4 class="h2Ventas">Ventas-Pro</h4>
        </a>

        <!-- BUSCADOR DE PRODUCTOS -->
        <form class="d-flex w-50 mx-4">
            <input id="buscador" class="form-control me-2" type="search" placeholder="Buscar productos, marcas y más...">
            <button class="btn btn-light">
                <i class="bi bi-search"></i>
            </button>
        </form>

        <!-- ICONOS FUNCIONALES: VOLVER, PERFIL, CERRAR SESIÓN -->
        <div class="d-flex align-items-center gap-4">
            <!-- VOLVER AL PANEL DEL CLIENTE -->
            <div class="text-center eleven">
                <i class="bi bi-arrow-left-circle fs-5"></i>
                <div style="font-size:12px;">
                    <a class="colorLinks" href="cliente.php">Volver</a>
                </div>
            </div>

            <!-- PERFIL DEL CLIENTE -->
            <div class="text-center eleven">
                <i class="bi bi-person fs-5"></i>
                <div style="font-size:12px;">
                    <a class="colorLinks" href="perfil.php">Perfil</a>
                </div>
            </div>

            <!-- CERRAR SESIÓN -->
            <div class="text-center eleven">
                <form action="login.php" method="post" style="display:inline;">
                    <button type="submit" name="cerrar_sesion" class=" p-0 text-white border-0 bg-transparent">
                        <i class="bi bi-box-arrow-right fs-5"></i>
                        <div style="font-size:12px;" class="colorLinks">
                            Cerrar sesión
                        </div>
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<!-- ========================================================== -->
<!-- MENÚ DE CATEGORÍAS / ACCIONES DEL CLIENTE -->
<!-- ========================================================== -->
<div class="bg-white shadow-sm">
    <div class="container py-2">
        <div class="d-flex gap-4 fw-semibold align-items-center">

            <!-- Carrito -->
            <a href="carrito.php" class="d-flex justify-content-between align-items-center text-dark text-decoration-none">
                <span class="Mcategorias">Carrito</span>
                <i class="bi bi-cart" style="color: orange;"></i>
            </a>

            <!-- Compras -->
            <a href="mis_compras.php" class="d-flex justify-content-between align-items-center text-dark text-decoration-none">
                <span class="Mcategorias">Compras</span>
                <i class="bi bi-bag-check" style="color: orange;"></i>
            </a>

            <!-- Crear solicitud -->
            <a href="crear_solicitud.php" class="d-flex justify-content-between align-items-center text-dark text-decoration-none">
                <span class="Mcategorias">Enviar solicitud</span>
                <i class="bi bi-send" style="color: orange;"></i>
            </a>

            <!-- Mis solicitudes -->
            <a href="mis_solicitudes.php" class="d-flex justify-content-between align-items-center text-dark text-decoration-none">
                <span class="Mcategorias">Mis solicitudes</span>
                <i class="bi bi-file-earmark-text" style="color: orange;"></i>
            </a>

            <!-- Mis envíos -->
            <a href="mis_envios.php" class="d-flex justify-content-between align-items-center text-dark text-decoration-none">
                <span class="Mcategorias">Mis envios</span>
                <i class="bi bi-box-seam" style="color: orange;"></i>
            </a>
        </div>
    </div>
</div>

<!-- ========================================================== -->
<!-- BIENVENIDA CLIENTE -->
<!-- ========================================================== -->
<div class="container my-1 mx-0">
    <div class="bienvenida-box conte d-flex align-items-center">

        <!-- FOTO PERFIL DEL CLIENTE -->
        <div class="foto-container me-3">
            <img src="imagenes/<?php echo $_SESSION['Foto']; ?>" class="foto-perfil">
        </div>

        <div>
            <h2 class="bienvenida-texto mb-1">
                Bienvenido, <span><?php echo $_SESSION['nomusuario']; ?></span> 👋
            </h2>
            <p class="subtexto mb-0">
                Nos alegra verte de nuevo en <strong>VentasPro</strong>
            </p>
        </div>
    </div>
</div>

<!-- ========================================================== -->
<!-- CATÁLOGO DE PRODUCTOS -->
<!-- ========================================================== -->
<h2 class="h2catalogo">Catálogo de Productos</h2>

<div class="container my-5">
<div class="row g-4">

<?php foreach($productos as $producto){ ?>

<!-- CARD DE CADA PRODUCTO -->
<div class="col-md-2 col-sm-6 producto">
    <div class="card shadow-sm border-0 position-relative CartaFlotar">

        <!-- BADGE DE DISPONIBILIDAD -->
        <?php if($producto['stock'] > 0){ ?>
            <span class="badge bg-success position-absolute m-2">Disponible</span>
        <?php } else { ?>
            <span class="badge bg-danger position-absolute m-2">No disponible</span>
        <?php } ?>

        <!-- IMAGEN DEL PRODUCTO -->
        <?php if(!empty($producto['foto'])){ ?>
            <img src="imagenes/<?= $producto['foto'] ?>" class="card-img-top">
        <?php } ?>

        <div class="card-body">
            <h6 style="text-align: center;" class="card-title hseis nombre-producto">
                <?= htmlspecialchars($producto['nombre']) ?>
            </h6>

            <small style="text-align: center;" class="card-title textoss descripcion-producto">
                <?= htmlspecialchars($producto['descripcion']) ?>
            </small>

            <h5 class="fw-bold mt-2 textoss">$ <?= number_format($producto['precio'],2) ?></h5>
            <small class="colorestxt d-block">Stock: <?= $producto['stock'] ?></small>
            <small class="colorestxt1">Envío gratis</small>

            <br>

            <!-- FORMULARIO AGREGAR AL CARRITO SI HAY STOCK -->
            <?php if($producto['stock'] > 0){ ?>
                <form method="POST" action="carrito.php">
                    <input type="hidden" name="idproducto" value="<?= $producto['id'] ?>">
                    <input type="number" name="cantidad"
                           value="1"
                           min="1"
                           max="<?= $producto['stock'] ?>"
                           class="form-control mb-2 text-center"
                           style="height:35px;"
                           required>
                    <button type="submit" name="agregar" class="btn1 btn-dark w-100">
                        <i class="bi bi-cart-plus"></i> Agregar
                    </button>
                </form>
            <?php } else { ?>
                <button class="btn btn-secondary w-100" disabled>Sin stock</button>
            <?php } ?>
        </div>
    </div>
</div>

<?php } ?>

</div>
</div>

<!-- ========================================================== -->
<!-- FOOTER -->
<!-- ========================================================== -->
<footer class="footer-pro mt-auto text-center py-3">
    <span class="cambio">© 2026 VentasPro - Todos los derechos reservados</span>
</footer>

<!-- ========================================================== -->
<!-- SCRIPT DE BUSCADOR EN TIEMPO REAL -->
<!-- ========================================================== -->
<script>
document.getElementById("buscador").addEventListener("keyup", function() {
    let filtro = this.value.toLowerCase();
    let productos = document.querySelectorAll(".producto");

    productos.forEach(producto => {
        let nombre = producto.querySelector(".nombre-producto").textContent.toLowerCase();
        let descripcion = producto.querySelector(".descripcion-producto").textContent.toLowerCase();

        // Mostrar producto si el nombre o descripción contiene el texto
        if (nombre.includes(filtro) || descripcion.includes(filtro)) {
            producto.style.display = "";
        } else {
            producto.style.display = "none";
        }
    });
});
</script>

</body>
</html>