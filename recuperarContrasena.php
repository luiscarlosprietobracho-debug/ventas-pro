<?php
// Inicia la sesión
session_start();

// Incluye la conexión a la base de datos (obligatorio con require)
require 'conexionpdo.php';

// Importa las clases necesarias de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Incluye los archivos de PHPMailer
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

// Crea instancia de la base de datos
$db = new Database();

// Realiza la conexión
$conexion= $db->conectar();

// Verifica si la conexión falló
if ($conexion==null){
    die ("Error de la conexion"); // Detiene el programa si falla
}

// Si el usuario ya está logueado
if(isset($_SESSION['Usuario'])){
    header('location: paginaPrincipal.php'); // Lo redirige
    exit();
}

// Variables para mensajes
$errorCorreo="";
$errorCorrecto="";

// Si el formulario se envió por POST
if($_SERVER['REQUEST_METHOD']==='POST'){  

    // Obtiene el correo ingresado y elimina espacios
    $verificarCorreo = trim($_POST['verificacion']);

    try {    

    // Consulta para verificar si el correo existe
    $queryEmail= "SELECT id, email FROM usuarios WHERE email=:correo";

    // Prepara la consulta
    $consultaEmail=$conexion->prepare($queryEmail);

    // Asocia el parámetro
    $consultaEmail->bindParam(':correo',$verificarCorreo, PDO::PARAM_STR);

    // Ejecuta la consulta
    $consultaEmail->execute();

    // Obtiene el resultado
    $resultadoEmail= $consultaEmail->fetch(PDO::FETCH_ASSOC);      

        // Si el correo existe en la BD
        if ($resultadoEmail){  

            // Guarda el id del usuario
            $usuario_Id = $resultadoEmail['id'];

            // Actualiza el token_request (marca que solicitó cambio)
            $actualizarRegistroToken="UPDATE usuarios SET token_request=1 WHERE email=:correo";

            $actualizarToken=$conexion->prepare($actualizarRegistroToken);

            $actualizarToken->bindParam(':correo',$verificarCorreo, PDO::PARAM_STR);

            $actualizarToken->execute();

            // Si se obtuvo el id del usuario
            if ($usuario_Id){

                $errorCorrecto="Correo Correcto";

                // Crea instancia de PHPMailer
                $email = new PHPMailer(true);

                try{

                        // Configuración del servidor SMTP
                        $email->isSMTP();
                        $email->Host       = 'smtp.gmail.com';
                        $email->SMTPAuth   = true;

                        // Correo que envía
                        $email->Username   = 'luiscarlosprietobracho@gmail.com';

                        // Contraseña del correo (app password)
                        $email->Password   = 'kbcu gjiu kjzj sbbw';

                        // Seguridad
                        $email->SMTPSecure = PHPMailer ::ENCRYPTION_STARTTLS;
                        $email->Port       = 587;

                        // Codificación
                        $email->CharSet    = "utf8";

                        // Remitente
                        $email->setFrom('luiscarlosprietobracho@gmail.com','VentasPro');

                        // Destinatario
                        $email->addAddress($verificarCorreo);

                        // Formato HTML
                        $email->isHTML(true);

                        // Asunto del correo
                        $email->Subject = 'Recuperacion de Contraseña';

                        // Cuerpo del correo con enlace
                        $email->Body=
                        '<div style="max-width: 500px;margin: 40px auto; font-family: arial;">
                        <h2>🔒 Recuperar Contraseña</h2><hr style="border-top:3px solid #f37b18;">
                        <p style="font-size: 15px;">Hola, hemos recibido una solicitud para restablecer su contraseña, si fue usted de click en el siguiente enlace </p>
                        <a href="http://localhost/Ventas-Pro/cambiarContrasena.php?Id='.$usuario_Id.'">Restablecer Contraseña</a>';

                        // Envía el correo
                        $email->send();

                        // Mensaje de éxito
                        echo "<script> alert ('Correo enviado con exito');window.location.href='login.php';</script>";

                    }
                    catch(\Exception $error){

                        // Error al enviar correo
                        echo "<script> alert ('Error al enviar el correo".$email->$error."');</script>";
                    }
                }
                else{

                    // Error si no se obtiene el ID
                    echo "<script> alert ('Error al recuperar el ID del usuario');</script>";
                }
            }
             else{

                // Si el correo no existe
                $errorCorreo="Correo Invalido";
            }

    } 
    catch (PDOException $error){

        // Error en base de datos
        echo "<script> alert ('Error en la seleccion a base de datos".$error->getMessage()."');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Recuperar Contraseña</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Iconos -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<!-- Estilos -->
<link rel="stylesheet" href="css/recuperarContrasena.css">

<!-- Fuente -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

</head>
<body>

<div class="form-card">

<!-- Icono -->
<i class="bi bi-shield-lock-fill icon-top"></i>

<!-- Título -->
<h1>¿Olvidaste tu contraseña?</h1>

<p class="descripcion">
    Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
</p>

<!-- Formulario -->
<form action="#" method="POST">

<div class="mb-3 position-relative">

    <i class="bi bi-envelope-fill input-icon"></i>

    <!-- Input correo -->
    <input 
        type="email" class="form-control input-pro ps-5" name="verificacion" placeholder="Correo electrónico"required>

</div>

<!-- Botón enviar -->
<button type="submit">Enviar enlace</button><br><br>

<!-- Volver al login -->
<div class="text-center">
    <a href="login.php" class="highlight d-block mb-2 text-decoration-none ">
        <span class="cambio">Volver al login</span>
    </a>
</div>

</form>

<!-- Mostrar error -->
<?php
if(!empty($errorCorreo)){
?>
<p class="error"><?php echo $errorCorreo ?></p>
<?php
}
?>

<!-- Mostrar éxito -->
<?php
if(!empty($errorCorrecto)){
?>
<p class="success"><?php echo $errorCorrecto ?></p>
<?php
}
?>

</div>

</body>
</html>
