<?php
// ============================================
// 1. CONEXIÓN Y SESIÓN
// ============================================
include_once("conexionpdo.php"); 
session_start();

// ============================================
// 2. PROCESAR LOGIN
// ============================================
if(isset($_POST['login'])){

    $correo = $_POST['email'];
    $clave  = $_POST['clave'];

    $pdo = (new Database())->conectar();

    // Buscar usuario por correo
    $sql = $pdo->prepare("SELECT * FROM usuarios WHERE email=?");
    $sql->execute([$correo]);

    $usuario = $sql->fetch(PDO::FETCH_ASSOC);

    if($usuario){

        // Verificar contraseña
        if(password_verify($clave, $usuario['clave'])){

            // VALIDAR QUE SEA ADMIN
            if($usuario['idrol'] == 1){

                // Crear sesión
                $_SESSION['idusuario'] = $usuario['id'];
                $_SESSION['nomusuario'] = $usuario['nomusuario'];
                $_SESSION['rol'] = $usuario['idrol'];
                $_SESSION['Foto'] = $usuario['Foto'];

                // Redirigir al panel admin
                header("location: administrador.php");
                exit();

            } else {
                $error = "No tienes permisos de administrador";
            }

        } else {
            $error = "Contraseña incorrecta";
        }

    } else {
        $error = "Usuario no encontrado";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Login Administrador</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- ICONOS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="css/admin_login.css">

<script>
function mostrarContraseña(){
    document.getElementById("clave").type="text";
}
function ocultarContraseña(){
    document.getElementById("clave").type="password";
}
</script>

</head>

<body>

<div class="login-card">

    <!-- ICONO -->
    <div class="icon-circle-container">
        <div class="icon-circle">
            <i class="bi bi-shield-lock"></i>
        </div>
    </div>

    <!-- TITULO -->
    <h2 class="title mt-3">Administrador</h2>
    <p class="subtitle mb-4">Acceso al sistema</p>

    <!-- ERROR -->
    <?php if(isset($error)){ ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php } ?>

    <!-- FORM -->
    <form method="POST">

        <!-- EMAIL -->
        <div class="form-floating mb-3 position-relative">
            <i class="bi bi-envelope floating-icon"></i>
            <input type="email" name="email" class="form-control fondo ps-5" placeholder="Correo" required>
            <label>Correo</label>
        </div>

        <!-- PASSWORD -->
     <div class="form-floating mb-3 position-relative">
         <i class="bi bi-lock floating-icon"></i>

         <input id="clave" type="password" name="clave"
                class="form-control fondo ps-5 pe-5"
                placeholder="Contraseña" required>

         <label>Contraseña</label>

         <i id="toggleClave" class="bi bi-eye-slash position-absolute toggle-icon"></i>
     </div>

        <!-- BOTON -->
        <button type="submit" name="login" class="btn-grad w-100">
            Ingresar
        </button>

    </form>

    <hr class="mt-4" style="border-color:#444;">

    <!-- VOLVER -->
    <div class="text-center">
        <a style="text-decoration: none" href="index.php" class="link-volver cambio">Volver</a>
    </div>

</div>

<script>
const toggleClave = document.getElementById("toggleClave");
const inputClave = document.getElementById("clave");

if(toggleClave && inputClave){
    toggleClave.addEventListener("click", function () {

        if(inputClave.type === "password"){
            inputClave.type = "text";
            this.classList.remove("bi-eye-slash");
            this.classList.add("bi-eye");
        } else {
            inputClave.type = "password";
            this.classList.remove("bi-eye");
            this.classList.add("bi-eye-slash");
        }

    });
}
</script>

</body>
</html>