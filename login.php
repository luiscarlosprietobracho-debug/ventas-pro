```php id="6kq9nv"
<?php
// Incluye la conexión a la base de datos
include("conexionpdo.php");

// Inicia la sesión
session_start();

// Si se presionó el botón de cerrar sesión
if (isset($_POST['cerrar_sesion'])) {
    include_once("cerrar.php"); // Ejecuta el archivo que destruye la sesión
}

// Si el usuario ya tiene sesión iniciada
if(isset($_SESSION['rol'])){
    // Redirige según el rol
    switch($_SESSION['rol']){
        case 2: header("location: cliente.php"); break;      // Cliente
        case 3: header("location: solicitudes.php"); break;  // Soporte
        case 4: header("location: envios.php"); break;       // Envíos
        default: break;
    }
}

// Variable para mostrar errores en pantalla
$errorIngreso ="";

// Verifica que los campos no estén vacíos
if(!empty($_POST['nombreusuario']) && !empty($_POST['contrasena'])){

    // Guarda los datos ingresados
    $username=$_POST['nombreusuario'];
    $password=$_POST['contrasena'];

    // Crea instancia de la base de datos
    $db=new Database();

    // Consulta para buscar el usuario
    $query=$db->conectar()->prepare("SELECT * FROM usuarios WHERE nomusuario=:nombreusuario LIMIT 1");

    // Ejecuta la consulta
    $query->execute(['nombreusuario'=>$username]);

    // Obtiene el resultado
    $arregloFilas=$query->fetch(PDO::FETCH_ASSOC);

    // Si el usuario existe
    if($arregloFilas){

        // Obtiene la contraseña almacenada en la BD
        $claveBD = $arregloFilas['clave'];

        // Verifica contraseña (hash o texto plano)
        if(password_verify($password, $claveBD) || $password == $claveBD){

    // BLOQUEAR ADMIN EN LOGIN NORMAL
    if($arregloFilas['idrol'] == 1){

        // No permite que admin entre por este login
        $errorIngreso = "Acceso no permitido";

    } else {

        // Guarda datos en sesión
        $_SESSION['idusuario'] = $arregloFilas['id'];
        $_SESSION['nomusuario'] = $arregloFilas['nomusuario'];
        $_SESSION['rol'] = $arregloFilas['idrol'];
        $_SESSION['Foto'] = $arregloFilas['Foto'];

        // Redirige según el rol
        switch($arregloFilas['idrol']){
            case 2: header("location: cliente.php"); exit();
            case 3: header("location: solicitudes.php"); exit();
            case 4: header("location: envios.php"); exit();
        }
    }

} else {

    // Contraseña incorrecta
    $errorIngreso = "Contraseña incorrecta";
}

    } else {

        // Usuario no existe
        $errorIngreso = "Usuario o contraseña incorrecta";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>VentasPro | Login</title>

<!-- Responsive -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Iconos -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<!-- Estilos personalizados -->
<link rel="stylesheet" href="css/stylos_login/stylos.css">
<link rel="stylesheet" href="css/stylos_login/styloss.css">
<link rel="stylesheet" href="css/login.css">

</head>

<body class="Nbg">

<!-- HEADER (comentado) -->

<!-- NAV -->
<nav class="nav-secundario sup">
    <div class="container d-flex justify-content-between align-items-center">

        <!-- Logo -->
        <div class="logo-box">
            <img src="img/VENTAS_PRO_remove.png" class="logo-img">
        </div>

        <!-- Links de navegación -->
        <div class="nav-links">
            <a class="efecto"  style="text-decoration: none;" href="index.php">Inicio</a>
            <a class="efecto" style="text-decoration: none;" href="nosotros.php">Sobre nosotros</a>
        </div>

    </div>
</nav>

<!-- CONTENIDO PRINCIPAL -->
<div class="container-fluid flex-grow-1">
<div class="row align-items-center">

<!-- CARRUSEL -->
<div class="col-lg-7 d-none d-lg-block p-0">
    <div id="loginCarousel" class="carousel slide vh-100 mt-5 ms-5" data-bs-ride="carousel">

        <div class="carousel-inner h-100">

            <!-- Imagen 1 -->
            <div class="carousel-item active h-100">
                <img src="img/pagodigital.jpg" class="carousel-img">

                <!-- Texto encima -->
                <div class="carousel-caption-custom">
                    <h5>Pagos digitales seguros</h5>
                    <p>Acepta pagos con tarjeta, transferencias y más.</p>
                </div>
            </div>

            <!-- Imagen 2 -->
            <div class="carousel-item h-100">
                <img src="img/productos.jpg" class="carousel-img">

                <div class="carousel-caption-custom">
                    <h5>Compra fácil y rápido</h5>
                    <p>Encuentra todo lo que necesitas en un solo lugar.</p>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- FORMULARIO LOGIN -->
<div class="col-lg-5 d-flex justify-content-center">
<div class="login-card">

    <!-- Icono -->
    <div class="icon-circle-container">
        <div class="icon-circle">
            <i class="bi bi-lock"></i>
        </div>
    </div>

    <!-- Título -->
    <h2 class="title mt-3">Ventas-Pro</h2>
    <p class="subtitle mb-4">Sistema de Gestión</p>

    <!-- Mostrar error si existe -->
    <?php if(!empty($errorIngreso)){ ?>
        <p class="text-danger text-center"><?php echo $errorIngreso ?></p>
    <?php } ?>

    <!-- FORMULARIO -->
    <form method="post">

        <!-- Campo usuario -->
        <div class="form-floating mb-3 position-relative">
            <i class="bi bi-person floating-icon"></i>
            <input type="text" name="nombreusuario" class="form-control fondo ps-5" placeholder="Usuario" required>
            <label>Usuario</label>
        </div>

        <!-- Campo contraseña -->
        <div class="form-floating mb-3 position-relative">
            <i class="bi bi-lock-fill floating-icon"></i>

            <input id="floatingPassword" type="password" name="contrasena"
                   class="form-control fondo ps-5 pe-5" placeholder="Contraseña" required>

            <label>Contraseña</label>

            <!-- Icono para mostrar/ocultar contraseña -->
            <i id="togglePassword" class="bi bi-eye-slash position-absolute toggle-icon"></i>
        </div>

        <!-- Botón login -->
        <button type="submit" class="btn btn-grad w-100 fw-bold mb-3">
            Iniciar Sesión
        </button>

    </form>

    <hr class="custom-hr">

    <!-- Links adicionales -->
    <div class="text-center">
        <a href="registro.php" class="highlight d-block mb-2 text-decoration-none">
            <span class="cambio">Crear cuenta</span>
        </a>

        <a href="recuperarContrasena.php" class="highlight d-block mb-2 text-decoration-none">
            <span class="cambio">¿Olvidaste tu contraseña?</span>
        </a>
    </div>

</div>
</div>

</div>
</div>

<!-- FOOTER -->
<footer class="footer-pro mt-auto text-center py-3">

    © 2026 VentasPro - Todos los derechos reservados

    <!-- Acceso especial para admin -->
    <a href="admin_login.php" class="highlight d-block mb-2 text-decoration-none">
        <span class="cambio">Adminsitrador</span>
    </a>

</footer>

<script>
// Script para mostrar/ocultar contraseña
const toggle = document.getElementById("togglePassword");
const input = document.getElementById("floatingPassword");

if(toggle && input){
    toggle.addEventListener("click", function () {

        // Si está oculta, la muestra
        if(input.type === "password"){
            input.type = "text";
            this.classList.remove("bi-eye-slash");
            this.classList.add("bi-eye");

        } else {
            // Si está visible, la oculta
            input.type = "password";
            this.classList.remove("bi-eye");
            this.classList.add("bi-eye-slash");
        }

    });
}
</script>

<!-- Script de Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
```
