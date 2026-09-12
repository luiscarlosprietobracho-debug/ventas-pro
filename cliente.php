<?php
include_once("conexionpdo.php");
session_start();
$contenedorPDO = (new Database())->conectar();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 2) {
    header("location: login.php");
    exit();
}

// ==========================================================
// 🔍 BUSCADOR DE PRODUCTOS
// ==========================================================

// Verificamos si el usuario escribió algo en el buscador
if (isset($_GET['buscar']) && !empty($_GET['buscar'])) {

    // Guardamos el texto buscado
    $busqueda = "%" . $_GET['buscar'] . "%";

    // Consulta preparada (segura contra inyección SQL)
    $sql = $contenedorPDO->prepare("
        SELECT * FROM productos 
        WHERE nombre LIKE ? 
        OR descripcion LIKE ?
    ");

    // Ejecutamos pasando la búsqueda
    $sql->execute([$busqueda, $busqueda]);

    // Guardamos resultados
    $productos = $sql->fetchAll(PDO::FETCH_ASSOC);

} else {

    // Si no hay búsqueda → mostramos productos normales (limitados)
    $productos = $contenedorPDO
        ->query("SELECT * FROM productos LIMIT 6")
        ->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!-- LOGICA EDITAR DATOS DEL USUARIO -->
 <?php
//5 Actualizar usuario

if(isset($_POST['actualizar'])) {
    $editar_id=$_POST['id_editar'];
    $contenedor=$contenedorPDO->prepare("SELECT Foto, clave FROM usuarios WHERE id=?");
    $contenedor->execute([$editar_id]);
    $fila=$contenedor->fetch(PDO::FETCH_ASSOC);
        if($fila){
            $fotoActual=$fila['Foto'];
            } else {
                $fotoActual=NULL;
            }
        if(isset($_FILES['imagenXSubir'])&& $_FILES['imagenXSubir']['error']===0){     
            $extension=pathinfo($_FILES['imagenXSubir']['name'], PATHINFO_EXTENSION);

        //    $permitidos=['jpg','png','gif','jpeg','webp''gif'];
        //    if (!in_array(strtolower($extension),$extension)){
        //   die("Formato de iamgen no permitido");
        //   }  
            $fecha=date("d-m-Y_H-i-s");
        //  $nombreArchivo=

            $nombreArchivo= "usuario_".$editar_id."_".$fecha.".".$extension;
            $ruta="imagenes/".$nombreArchivo;
            if (move_uploaded_file($_FILES['imagenXSubir']['tmp_name'], $ruta)){
                if(!empty($fotoActual)&& file_exists("imagenes/".$fotoActual)){
                        unlink("imagenes/".$fotoActual);
                    }
                    $fotoActual=$nombreArchivo;
                }
            }

            if(empty($_POST['clave'])){
                $claveFinal = $fila['clave'];
            }else{
    $claveFinal = password_hash($_POST['clave'], PASSWORD_DEFAULT);
}
    $contenedor=$contenedorPDO->prepare("UPDATE usuarios SET nomusuario=?,clave=?,idrol=?,email=?,Foto=?,direccion=? WHERE id=?");
    $contenedor->execute([
        $_POST['usuario'], 
        $claveFinal, 
        2, 
        $_POST['email'], 
        $fotoActual, 
        $_POST['direccion'], 
        $editar_id
    ]);
    header("location: cliente.php");
    exit();
    }
?>
<!-- CIERRE DE CONTENIDO -->

<!DOCTYPE html>
<html lang="es">

<head>
<meta charset="UTF-8">
<title>Ventas-Pro - Cliente</title>

<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<link rel="stylesheet" href="css/cliente.css">
<link rel="stylesheet" href="css/clientes.css">
<link rel="stylesheet" href="css/cliente1.css">

</head>

<body class="bg-light">

<!-- HEADER LOGO -->

<header class="fondo py-3">
<div class="container text-center">

<img class="ContIMG" src="img/VENTAS_PRO_remove.png"
class="img-fluid"
style="max-height:140px;">

</div>
</header>


<!-- BARRA SUPERIOR -->

<div class="text-white py-2 px-4 d-flex justify-content-between Superior">

<div>
<i class="bi bi-geo-alt"></i> Envios gratis
</div>

<div>

<a href="nosotros.php" class="text-white me-3 text-decoration-none">Sobre nosotros</a>

<a href="ayuda.php" class="text-white text-decoration-none">Ayuda</a>

</div>

</div>


<div class="contenido">
    
<!-- NAVBAR PRINCIPAL -->

<nav class="navbar navbar-expand-lg navbar-dark colorNavbar">

<div class="container-fluid px-4">

<a class="navbar-brand fw-bold d-flex align-items-center" href="cliente.php">

<i class="bi bi-box-seam fs-3 me-2"></i>

<h4 class="h2Ventas">Ventas-Pro</h4>

</a>


<!-- BUSCADOR -->

<!-- ========================================================== -->
<!-- 🔍 BUSCADOR -->
<!-- ========================================================== -->

<form class="d-flex w-50 mx-4" method="GET" action="cliente.php">

    <!-- Input de búsqueda -->
    <input 
    class="form-control me-2" 
    type="search" 
    name="buscar" 
    placeholder="Buscar productos, marcas y más..."
    value="<?php echo isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : ''; ?>"
/>

    <!-- Botón buscar -->
    <button class="btn btn-light" type="submit">
        <i class="bi bi-search"></i>
    </button>

</form>


<!-- ICONOS FUNCIONALES -->
<div class="d-flex align-items-center gap-4">

    <!--<div class="text-center eleven">
        <i class="bi bi-building fs-5"></i>
        <div style="font-size:12px;">
            <a class="colorLinks" href="nosotros.php">Nosotros</a>
        </div>
    </div>

    <div class="text-center eleven">
        <i class="bi bi-question-circle fs-5"></i>
        <div style="font-size:12px;">
            <a class="colorLinks" href="ayuda.php">Ayuda</a>
        </div>
    </div>-->

    <div class="text-center eleven">
        <i class="bi bi-person fs-5"></i>
        <div style="font-size:12px;">
            <a class="colorLinks" href="perfil.php">Perfil</a>
        </div>
    </div>

    <div class="text-center eleven">
        <i class="bi bi-bag fs-5"></i>
        <div style="font-size:12px;">
            <a class="colorLinks" href="productos_cliente.php">Productos</a>
        </div>
    </div>

    <div class="text-center eleven">
        <form action="login.php" method="post" style="display:inline;">
            <button type="submit" name="cerrar_sesion" class="btn p-0 text-white border-0 bg-transparent">
                <i class="bi bi-box-arrow-right fs-5"></i>
                <div style="font-size:12px;" class="colorLinks">
                    Cerrar sesión
                </div>
            </button>
        </form>
    </div>
  </div>
</div>

<!--<div class="d-flex align-items-center text-white gap-4">


<div class="me-4 text-center eleven">

<i class="bi bi-cart fs-5"></i>

<div style="font-size:12px;" >
<a class="colorLinks" href="carrito.php">Carrito</a>
</div>

</div>-->


<!--<div class="text-center eleven">

<i class="bi bi-person fs-5"></i>

<div style="font-size:12px;">
<a class="colorLinks" href="perfil.php">Perfil</a>
</div>

</div>

<div class="text-center eleven">

    <form action="login.php" method="post" style="display:inline;">
        <button type="submit" name="cerrar_sesion" class="btn p-0 text-white border-0 bg-transparent">
            <i class="bi bi-box-arrow-right fs-5"></i>
            <div style="font-size:12px;" class="colorLinks">
                Cerrar sesión
            </div>
        </button>
    </form>

</div>-->

<!--<div class="me-4 text-center eleven">

<i class="bi bi-cart-plus fs-5"></i>

<div style="font-size:12px;">
<a class="colorLinks" href="mis_compras.php">Compras</a>
</div>

</div>

<div class="me-4 text-center eleven">

<i class="bi bi-send fs-5"></i>

<div style="font-size:12px;">
<a class="colorLinks"  href="crear_solicitud.php">Envíar</a>
</div>

</div>


<div class="me-4 text-center eleven">

<i class="bi bi-send fs-5"></i>

<div style="font-size:12px;">
<a class="colorLinks" href="mis_solicitudes.php">Mis solicitudes</a>
</div>

</div>-->

</div>

</div>

</nav>

<!-- Menú de categorías -->
<div class="bg-white shadow-sm">
    <div class="container py-2">
        <div class="d-flex gap-4 fw-semibold align-items-center">


            <!-- Perfil 
            <a href="perfil.php" class="text-dark text-decoration-none text-center">
            
                <span class="Mcategorias">Perfil</span>
            </a>-->

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

            <!-- Enviar -->
                <a href="crear_solicitud.php" class="d-flex justify-content-between align-items-center text-dark text-decoration-none">
                    <span class="Mcategorias">Enviar solicitud</span>
                    <i class="bi bi-send" style="color: orange;"></i>
                </a>

            <!-- Mis solicitudes -->
                <a href="mis_solicitudes.php" class="d-flex justify-content-between align-items-center text-dark text-decoration-none">
                    <span class="Mcategorias">Mis solicitudes</span>
                    <i class="bi bi-file-earmark-text" style="color: orange;"></i>
                </a>

            <!-- Mis envios -->
                <a href="mis_envios.php" class="d-flex justify-content-between align-items-center text-dark text-decoration-none">
                    <span class="Mcategorias">Mis envios</span>
                    <i class="bi bi-box-seam" style="color: orange;"></i>
                </a>

        </div>
    </div>
</div><!-- CIERRE DE CONTENIDO -->



<!-- BIENVENIDA CLIENTE -->
<div class="container my-1 mx-0">

    <div class="bienvenida-box conte d-flex align-items-center">

        <div class="foto-container me-3">
            <img src="imagenes/<?php echo $_SESSION['Foto']; ?>" class="foto-perfil">
        </div>

        <div>
            <h2 class="bienvenida-texto mb-1">
                Bienvenido, <span><?php echo $_SESSION['nomusuario']; ?></span> 👋
            </h2>

            <p class="subtexto mb-0">
                Nos alegra verte en <strong>VentasPro</strong>
            </p>
        </div>

    </div>

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
          <a href="carrito.php" class="btn btn-warning fw-semibold">Ir</a>
        </div>
      </div>

      <!-- Slide 2 -->
      <div class="carousel-item">
        <img src="img/estilo.png" class="d-block w-100"
             style="height:400px; object-fit:cover; filter:brightness(60%);">

        <div class="carousel-caption text-start">
          <h1 class="fw-bold">Compra productos de tu estilo</h1>
          <p>En Ventas-Pro nos importa adaptarnos a tu estilo</p>
          <a href="productos_cliente.php" class="btn btn-warning fw-semibold">Ver más</a>
        </div>
      </div>

      <!-- Slide 3 -->
      <div class="carousel-item">
        <img src="solicitudes.jpg" class="d-block w-100"
             style="height:400px; object-fit:cover; filter:brightness(60%);">

        <div class="carousel-caption text-start">
          <h1 class="fw-bold">Envia solicitudes</h1>
          <p>En caso de haber problemas, puedes enviar solicitudes</p>
          <a href="crear_solicitud.php" class="btn btn-warning fw-semibold">ir</a>
        </div>
      </div>

      <!-- Slide 4 -->
      <div class="carousel-item">
        <img src="pagos.jpg" class="d-block w-100"
             style="height:400px; object-fit:cover; filter:brightness(60%);">

        <div class="carousel-caption text-start">
          <h1 class="fw-bold">Realiza tus pagos de manera segura con paypal.</h1>
          <a href="carrito.php" class="btn btn-warning fw-semibold">Ir</a>
        </div>
      </div>

    </div>

    <!-- CONTROLES LATERALES -->
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselPrincipal" data-bs-slide="prev">
      <span class="carousel-control-prev-icon"></span>
    </button>

    <button class="carousel-control-next" type="button" data-bs-target="#carouselPrincipal" data-bs-slide="next">
      <span class="carousel-control-next-icon"></span>
    </button>

  </div>
</div><!-- CIERRE DE CONTENIDO -->

<!-- BLOQUE BIENVENIDA -->

<div class="container my-5">

<div class="rounded p-5 d-flex justify-content-between align-items-center text-white"

style="background: linear-gradient(90deg,#000,#111);">

<div>

<h2 class="fw-bold">Bienvenido a VentasPro!</h2>

<p>Gracias por comprar con nosotros</p>

<a href="productos_cliente.php" class="btn btn-warning">

Ver catálogo

</a>

</div>

<i class="bi bi-box-seam display-1 text-warning opacity-50"></i>

</div>

</div>

<!-- Ofertas del día / CONTENEDOR -->
<div class="container my-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Ofertas del día</h3>
        <a href="productos_cliente.php" class="text-warning fw-semibold text-decoration-none">
            Ver todas <i class="bi bi-chevron-right"></i>
        </a>
    </div>


<div class="row g-4">

<?php 
// Si el usuario buscó y no hay resultados
if (isset($_GET['buscar']) && empty($productos)) { 
?>
    <div class="col-12 text-center text-white">
        <h5>No se encontraron productos 😢</h5>
    </div>
<?php } ?>

<?php foreach($productos as $producto){ ?>

    <div class="col-lg-2 col-md-4 col-sm-6 col-12">

        <div class="card shadow-sm border-0 position-relative CartaFlotar">

            <!-- BADGE -->
            <?php if($producto['stock'] > 0){ ?>
                <span class="badge bg-success position-absolute m-2">
                    Disponible
                </span>
            <?php } else { ?>
                <span class="badge bg-danger position-absolute m-2">
                    Sin stock
                </span>
            <?php } ?>

            <!-- IMAGEN -->
            <?php if(!empty($producto['foto'])){ ?>
                <img src="imagenes/<?= $producto['foto'] ?>" class="card-img-top">
            <?php } ?>

            <!-- CUERPO -->
            <div class="card-body">

                <!-- NOMBRE -->
                <h6 style=" text-align: center;" class="card-title hseis">
                    <?= htmlspecialchars($producto['nombre']) ?>
                </h6>

                <!-- DESCRIPCIÓN -->
                <small style=" text-align: center;" class="card-title textoss">
                    <?= htmlspecialchars($producto['descripcion']) ?>
                </small>

                <!-- PRECIO -->
                <h5 class="fw-bold mt-2 textoss">
                    $<?= number_format($producto['precio'],2) ?>
                </h5>

                <!-- STOCK -->
                <small class="colorestxt d-block">
                    Stock: <?= $producto['stock'] ?>
                </small>

                <small class="colorestxt1">Envío gratis</small>

                <br>

                <!-- FORMULARIO -->
                <?php if($producto['stock'] > 0){ ?>
                <form method="POST" action="carrito.php">

                    <input type="hidden" name="idproducto" value="<?= $producto['id'] ?>">

                    <input type="number" name="cantidad"
                        value="1"
                        min="1"
                        max="<?= $producto['stock'] ?>"
                        class="form-control mb-2 text-center"
                        required>

                    <button type="submit" name="agregar" class="btn1">
                        <i class="bi bi-cart-plus"></i> Agregar
                    </button>

                </form>
                <?php } else { ?>

                <button class="btn btn-secondary w-100" disabled>
                    Sin stock
                </button>

                <?php } ?>

            </div>

        </div>

    </div>

<?php } ?>

</div> <!-- CIERRE DEL CONTENIDO -->






</div>

<!-- CERRAR SESION

<div class="text-center my-5">
<form action="login.php" method="post">
<input class="btn btn-danger px-4" type="submit" name="cerrar_sesion" value="CERRAR SESION">
</form>
</div>-->




<footer class="footer-pro mt-auto text-center py-3">

    <span class="cambio">© 2026 VentasPro - Todos los derechos reservados</span>

</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>

