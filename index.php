<?php
// Incluye el archivo de conexión a la base de datos
include_once("conexionpdo.php");

// Inicia la sesión para usar variables como $_SESSION
session_start();
?>

<!DOCTYPE html>
<html lang="es">

<head>
<meta charset="UTF-8"> <!-- Define el tipo de caracteres -->
<title>VentasPro</title> <!-- Título de la página -->

<meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Hace la web responsive -->

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Iconos de Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<!-- Estilos personalizados -->
<link rel="stylesheet" href="css/cliente.css">
<link rel="stylesheet" href="css/clientes.css">

<style>
    h1 {
        color: yellow; /* Cambia el color de los títulos h1 */
    }
</style>

</head>

<body class="bg-light"> <!-- Fondo claro -->

<!-- HEADER -->
<header class="bg-black py-3"> <!-- Fondo negro con padding -->
<div class="container text-center">

<!-- Imagen del logo -->
<img src="img/VENTAS_PRO.png" class="img-fluid" style="max-height:140px;">

</div>
</header>

<!-- BARRA SUPERIOR (comentada, no se usa)
<div class="text-white py-2 px-4 d-flex justify-content-between navSuperior">

<p>HOLA</p>
<P>MUNDO</P>
<P>PARAFOS</P>
<P>PROFESIONAL</P>
</div>-->

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark colorNavbar">
<div class="container-fluid px-4">

<!-- Logo + nombre -->
<a class="navbar-brand fw-bold d-flex align-items-center" href="index.php">
<i class="bi bi-box-seam fs-3 me-2"></i> <!-- Icono -->
<h4 class="h2Ventas">Ventas-Pro</h4>
</a>

<!-- Texto promocional -->
<div class="mx-4 text-white">
    <span class="fw-bold">Calidad y confianza en cada compra -</span> 
    <span> Envíos rápidos -</span>
    <span> Compra segura -</span>
    <span> Pagos protegidos</span>
</div>

<!-- Zona derecha -->
<div class="d-flex align-items-center text-white gap-4">

<div class="text-center">
<!-- Icono carrito (comentado) -->
<!--<i class="bi bi-cart fs-5"></i>-->
<!--<div style="font-size:12px;">-->
<!--<a class="colorLinks" href="#">Carrito</a>-->
</div>
</div>

<div class="text-center">
<!--<i class="bi bi-person fs-5"></i>-->
<div style="font-size:12px;">

<!-- Verifica si el usuario está logueado -->
<?php if(isset($_SESSION['nomusuario'])){ ?>

<!-- Muestra el nombre del usuario -->
<a class="colorLinks" href="perfil.php"><?php echo $_SESSION['nomusuario']; ?></a>

<?php } else { ?>
<!--<a class="colorLinks" href="login.php">Perfil</a>-->
<div>

<!-- Verifica si tiene rol -->
<?php if(isset($_SESSION['rol'])){ ?>

    <!-- Enlace al panel del cliente -->
    <a href="cliente.php" class="text-white me-3 text-decoration-none">Mi cuenta</a>

<?php } else { ?>
    <div class="d-flex gap-3 justify-content-center">

    <!-- Botón iniciar sesión -->
    <a href="login.php" class="btn btn-grad fw-bold">
        Iniciar sesión
    </a>

    <!-- Botón registrarse -->
    <a href="registro.php" class="btn btn-grad fw-bold">
        Registrarse
    </a>

</div>
<?php } ?>

</div>
<?php } ?>
</div>
</div>

</div>

</div>

</div>

</div>
</nav>

<!-- CATEGORÍAS -->
<div class="bg-white shadow-sm">
<div class="container py-2">
<div class="d-flex gap-4 fw-semibold">

<!-- Enlaces principales -->
<a href="ayuda.php" class="text-dark text-decoration-none">Ayuda</a>
<a href="nosotros.php" class="text-dark text-decoration-none">Nosotros</a>
<a href="productos_cliente.php" class="text-dark text-decoration-none">Catalogo</a>

</div>
</div>
</div>

<!-- BIENVENIDA -->
<div class="container text-center my-4">

<!-- Si el usuario está logueado -->
<?php if(isset($_SESSION['nomusuario'])){ ?>
<h3 class="fw-bold">Bienvenido <?php echo $_SESSION['nomusuario']; ?></h3>

<!-- Muestra la foto del usuario -->
<img src="imagenes/<?php echo $_SESSION['Foto']; ?>" 
class="rounded-circle shadow mt-2"
width="120"
height="120"
style="object-fit:cover;">

<?php } else { ?>

<!-- Mensaje si no está logueado -->
<h3 class="fw-bold">Bienvenido a VentasPro</h3>
<p>Inicia sesión para una mejor experiencia</p>
<!--<a href="login.php" class="btn btn-warning">Iniciar Sesión</a>-->
<?php } ?>

</div>

<!-- CARRUSEL -->
<div class="container my-4">
  <div id="carouselPrincipal" class="carousel slide" data-bs-ride="carousel">

    <!-- Indicadores -->
    <div class="carousel-indicators">
      <button type="button" data-bs-target="#carouselPrincipal" data-bs-slide-to="0" class="active"></button>
      <button type="button" data-bs-target="#carouselPrincipal" data-bs-slide-to="1"></button>
      <button type="button" data-bs-target="#carouselPrincipal" data-bs-slide-to="2"></button>
      <button type="button" data-bs-target="#carouselPrincipal" data-bs-slide-to="3"></button>
      <button type="button" data-bs-target="#carouselPrincipal" data-bs-slide-to="4"></button>
    </div>

    <div class="carousel-inner rounded shadow">

      <!-- Slide 1 -->
      <div class="carousel-item active">
        <img src="paypal.jpg" class="d-block w-100"
             style="height:400px; object-fit:cover; filter:brightness(60%);">

        <div class="carousel-caption text-start">
          <h1 class="fw-bold">Pagos con paypal</h1>
          <p>Pagos agiles</p>
          <a href="login.php" class="btn btn-warning fw-semibold">Ir</a>
        </div>
      </div>

      <!-- Slide 2 -->
      <div class="carousel-item">
        <img src="img/estilo.png" class="d-block w-100"
             style="height:400px; object-fit:cover; filter:brightness(60%);">

        <div class="carousel-caption text-start">
          <h1 class="fw-bold">Compra productos de tu estilo</h1>
          <p>En Ventas-Pro nos importa adaptarnos a tu estilo</p>
          <a href="login.php" class="btn btn-warning fw-semibold">Ver más</a>
        </div>
      </div>

      <!-- Slide 3 -->
      <div class="carousel-item">
        <img src="solicitudes.jpg" class="d-block w-100"
             style="height:400px; object-fit:cover; filter:brightness(60%);">

        <div class="carousel-caption text-start">
          <h1 class="fw-bold">Envia solicitudes</h1>
          <p>En caso de haber problemas, puedes enviar solicitudes</p>
          <a href="login.php" class="btn btn-warning fw-semibold">ir</a>
        </div>
      </div>

      <!-- Slide 4 -->
      <div class="carousel-item">
        <img src="pagos.jpg" class="d-block w-100"
             style="height:400px; object-fit:cover; filter:brightness(60%);">

        <div class="carousel-caption text-start">
          <h1 class="fw-bold">Realiza tus pagos de manera segura con paypal.</h1>
          <a href="login.php" class="btn btn-warning fw-semibold">Ir</a>
        </div>
      </div>

    </div>

    <!-- Controles del carrusel -->
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselPrincipal" data-bs-slide="prev">
      <span class="carousel-control-prev-icon"></span>
    </button>

    <button class="carousel-control-next" type="button" data-bs-target="#carouselPrincipal" data-bs-slide="next">
      <span class="carousel-control-next-icon"></span>
    </button>

  </div>
</div>

<!-- OFERTAS DEL DÍA -->
<div class="container my-5">

    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Ofertas del día</h3>
        <a href="login.php" class="text-warning fw-semibold text-decoration-none">
            Ver todas <i class="bi bi-chevron-right"></i>
        </a>
    </div>

    <div class="row g-4">

        <!-- PRODUCTO 1 -->
        <div class="col-md-4">
            <a href="login.php" class="text-decoration-none text-dark">
                <div class="card h-100 shadow">
                    <img src="img/pexels-grailify-2658558-4252950.jpg"
                         class="card-img-top imagen-producto"
                         alt="Producto">
                    <div class="card-body text-center">
                        <h5 class="card-title">Zapatos Supreme</h5>
                        <p class="card-text">$50.000</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- PRODUCTO 2 -->
        <div class="col-md-4">
            <a href="login.php" class="text-decoration-none text-dark">
                <div class="card h-100 shadow">
                    <img src="img/mac.jpg"
                         class="card-img-top imagen-producto"
                         alt="Producto">
                    <div class="card-body text-center">
                        <h5 class="card-title">MacBook Pro</h5>
                        <p class="card-text">$80.000</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- PRODUCTO 3 -->
        <div class="col-md-4">
            <a href="login.php" class="text-decoration-none text-dark">
                <div class="card h-100 shadow">
                    <img src="img/camara.jpg"
                         class="card-img-top imagen-producto"
                         alt="Producto">
                    <div class="card-body text-center">
                        <h5 class="card-title">Camara Profesional</h5>
                        <p class="card-text">$120.000</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- PRODUCTO 4 -->
        <div class="col-md-4">
            <a href="login.php" class="text-decoration-none text-dark">
                <div class="card h-100 shadow">
                    <img src="img/iphone.jpg"
                         class="card-img-top imagen-producto"
                         alt="Producto">
                    <div class="card-body text-center">
                        <h5 class="card-title">iphone Z</h5>
                        <p class="card-text">$120.000</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- PRODUCTO 5 -->
        <div class="col-md-4">
            <a href="login.php" class="text-decoration-none text-dark">
                <div class="card h-100 shadow">
                    <img src="img/pexels-soulful-pizza-2080276-6753220.jpg"
                         class="card-img-top imagen-producto"
                         alt="Producto">
                    <div class="card-body text-center">
                        <h5 class="card-title">iphone x</h5>
                        <p class="card-text">$120.000</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- PRODUCTO 6 -->
        <div class="col-md-4">
            <a href="login.php" class="text-decoration-none text-dark">
                <div class="card h-100 shadow">
                    <img src="img/buzo_supreme.jpg"
                         class="card-img-top imagen-producto"
                         alt="Producto">
                    <div class="card-body text-center">
                        <h5 class="card-title">Buzo de hombre</h5>
                        <p class="card-text">$120.000</p>
                    </div>
                </div>
            </a>
        </div>

    </div>

</div>

<!-- FOOTER -->
<footer class="bg-black text-white">

<div class="text-center py-3">
© 2026 VentasPro
</div>

</footer>

<!-- Script de Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
