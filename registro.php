
<?php
// Incluye la conexión a la base de datos
include_once("conexionpdo.php");

// Crea la conexión usando PDO
$contenedorPDO = (new Database())->conectar();

// Verifica si se presionó el botón "insertar" (enviar formulario)
if(isset($_POST['insertar'])) {

// Obtener el correo
    $correo = $_POST['email'];

    // VALIDACIÓN DE CORREO
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) { //el FILTER_VALIDATE_es una funcion de php que valida el contenido dentro de la variable
    echo "<script>alert('Correo no válido'); window.history.back();</script>"; //el window.history.back() refresca la pagina, manteniendo los datos del formulario
    exit();
}

    // Foto por defecto del usuario
    $foto="incognito.jpg";

    // Rol por defecto (2 = cliente)
    $idrol = 2;

    // Prepara la consulta para insertar un nuevo usuario
    $contenedor=$contenedorPDO->prepare("
        INSERT INTO usuarios (nomusuario,clave,idrol,email,foto,direccion)
        VALUES (?,?,?,?,?,?)
    ");

    // Ejecuta la consulta con los datos del formulario
    $contenedor->execute([
        $_POST['usuario'], // Nombre de usuario

        // Encripta la contraseña antes de guardarla
        password_hash($_POST['clave'], PASSWORD_DEFAULT),

        $idrol, // Rol (cliente)

        $_POST['email'], // Correo

        $foto, // Foto por defecto

        $_POST['direccion'] // Dirección
    ]);

    // Muestra mensaje de éxito y redirige al login
    echo "<script>alert('Usuario creado correctamente'); window.location='login.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8"> <!-- Codificación -->
<meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive -->

<title>Registro | VentasPro</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Iconos -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<!-- Estilos personalizados -->
<link rel="stylesheet" href="css/registro.css">

<script>
// Función para validar la contraseña antes de enviar el formulario
function validarClave() {

    // Obtiene el valor del input de contraseña
    const clave = document.getElementById("clave").value;

    // Expresión regular que valida:
    // mínimo 8 caracteres, una mayúscula, una minúscula, un número y un símbolo
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/;

    // Si NO cumple la condición
    if (!regex.test(clave)) {

        // Muestra alerta
        alert("La contraseña debe tener mínimo 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial.");

        return false; // Evita que el formulario se envíe
    }

    return true; // Permite enviar el formulario
}
</script>

</head>

<body>

<!-- Contenedor principal centrado -->
<div class="container d-flex justify-content-center align-items-center vh-100">

<div class="registro-card">

    <!-- Título -->
    <h2 class="title">VentasPro</h2>
    <p class="subtitle mb-4">Crear cuenta</p>

    <!-- Formulario -->
    <form method="POST" onsubmit="return validarClave()">

        <!-- Campo usuario -->
        <div class="mb-3 position-relative">
            <i class="bi bi-person input-icon"></i>
            <input type="text" id="usuario" name="usuario" class="form-control ps-5" placeholder="Usuario" required>
        </div>

        <!-- CONTRASEÑA -->
        <div class="mb-3 position-relative">
            <i class="bi bi-lock input-icon"></i>

            <!-- Input contraseña -->
            <input type="password" id="clave" name="clave"
              class="form-control ps-5 pe-5" placeholder="Contraseña" required>

            <!-- Icono para mostrar/ocultar contraseña -->
            <i style="color: orange" id="toggleClave" class="bi bi-eye-slash position-absolute toggle-icon"></i>
        </div>

        <!-- Campo correo -->
        <div class="mb-3 position-relative">
            <i class="bi bi-envelope input-icon"></i>
            <input type="email" id="email" name="email" class="form-control ps-5" placeholder="Correo" required>
        </div>

        <!-- Campo dirección -->
        <div class="mb-3 position-relative">
            <i class="bi bi-geo-alt input-icon"></i>
            <input type="text" id="direccion" name="direccion" class="form-control ps-5" placeholder="Dirección" required>
        </div>

        <!-- Botón enviar -->
        <button type="submit" name="insertar" class="btn btn-pro w-100">
            Crear cuenta
        </button>

    </form>

    <hr class="my-4 text-light">

    <!-- Link para volver al login -->
    <div class="text-center">
        <a href="login.php" class="link-login highlight d-block mb-2 text-decoration-none ">
            <span class="cambio">Volver al login</span>
        </a>
    </div>

</div>

</div>

<script>
// Script para mostrar/ocultar contraseña
const toggle = document.getElementById("toggleClave");
const input = document.getElementById("clave");

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

</body>
</html>