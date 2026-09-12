<?php
session_start();
require '../controller/conexionpdo.php';   //el require es para que sea obligatorio. se coloca con dos puntos para que se regrese una carpeta atras, luego se le dice a que carpeta ingresar, y despues cual archivo llamar
use PHPMailer\PHPMailer\PHPMailer;        // esta es la configuracion minima para que funcione el php mailer
use PHPMailer\PHPMailer\Exception;        // esto es para cuando no coinciden los correos
use PHPMailer\PHPMailer\SMTP;             //esto es protocolo de transferencia de emails simple (eseta en ingles)
require '../view/PHPMailer-master/src/Exception.php';
require '../view/PHPMailer-master/src/PHPMailer.php';
require '../view/PHPMailer-master/src/SMTP.php';

$db = new Database();                      //una variable para almacenar la nueva instancia de la base de datos
$conexion= $db->conectar();               //una variable que ejecuta la conexcion a la base de datos
    if ($conexion==null){                 //validacion de si no se logra conectar a la base de datos muestre un mensaje de error
        die ("Error de la conexion");    //este es el mensaje de error, die es para decirle que ahi muere la funcion
    }

if(isset($_SESSION['Usuario'])){                //si el uusario esta logeado lo redirija
    header('location: paginaPrincipal.php');    //esto para que se rediriga a la pagina principal
    exit();
    }
$errorCorreo="";
$errorCorrecto="";
if($_SERVER['REQUEST_METHOD']==='POST'){                                                          //se valida si el metodo con el que se envia el dato al servidor desde el formulario es POST 
    $verificarCorreo =  $_POST['verificacion'];
    try {                                                                    //se trabaj con try catch, para seguridad del programador, ya que siempre entra en el try, pero si hay un error ahi se detiene y muestra en donde ocurrio el error
    $queryEmail= "SELECT Correo FROM registro WHERE Correo=:correo";         // verificamos que el correo existe
    $consultaEmail=$conexion->prepare($queryEmail);                          //creamos una variable donde se va a llamar la conexion para trabajar la consulta preparada
    $consultaEmail->bindParam(':correo',$verificarCorreo, PDO::PARAM_STR);   //se blinda la variable en conjunto con la forma en que se esta usando en la consulta preparada, EL pdo::str ES PARA INDICARLE QUE ESTAMOS TRABAJANDO CON UNA CADENA DE TEXTO Y Q LO BLINDE
    $consultaEmail->execute();                                               //se ejecuta la consulta preparada con la base de datos despues de haberse blindado los datos
    $resultadoEmail= $consultaEmail->fetch(PDO::FETCH_ASSOC);                //fetch 
        if ($resultadoEmail){                                                //validacion de si existe la variable $resultadoEmail haga lo isguiente
            $actualizarRegistroToken="UPDATE registro SET token_request=1 WHERE Correo=:correo";    //variable para actualizar con una consulta
            $actualizarToken=$conexion->prepare($actualizarRegistroToken);             //variable para preparar la consulta
            $actualizarToken->bindParam(':correo',$verificarCorreo, PDO::PARAM_STR);    //se blinda la variable en conjunto con la forma en que se esta usando en la consulta preparada, EL pdo::str ES PARA INDICARLE QUE ESTAMOS TRABAJANDO CON UNA CADENA DE TEXTO Y Q LO BLINDE
            $actualizarToken->execute();                                     //se ejecuta la consulta preparada con la base de datos despues de haberse blindado los datos
            
            $consultaIdCorreo="SELECT Id FROM registro WHERE Correo=:correo";                 //variable para actualizar con una consulta
            $preparaConsultaCorreo=$conexion->prepare($consultaIdCorreo);                               //variable para preparar la consulta
            $preparaConsultaCorreo->bindParam(':correo',$verificarCorreo, PDO::PARAM_STR);    //se blinda la variable en conjunto con la forma en que se esta usando en la consulta preparada, EL pdo::str ES PARA INDICARLE QUE ESTAMOS TRABAJANDO CON UNA CADENA DE TEXTO Y Q LO BLINDE
            $preparaConsultaCorreo->execute();                                                //se ejecuta la consulta preparada de la base de datos despues de haber blindados los parametros y preparadps
            $usuario_Id= $preparaConsultaCorreo->fetchColumn();                         //fetch 
            if ($usuario_Id){                                                           //validacion de que si todo salio bien y existe $usuario_Id haga lo siguiente
                $errorCorrecto="Correo Correcto";                                       //mensaje que se muestra si todo va fine
                $email = new PHPMailer(true);                                              //se crea una nueva instancia/objeto dentro de la clase del phpmailer
                    try{                                                                //configuracion del servidor SMTP
                        $email->isSMTP();                                               // para que el objeto trabaje como Simple Mail Transfer Protocol (SMTP)
                        $email->Host       = 'smtp.gmail.com';                          //aca se coloca la extension del correo que se esta usando para autenticar las contraseñas
                        $email->SMTPAuth   = true;                                      //autenticar el SMTP
                        $email->Username   = 'luiscarlosprietobracho@gmail.com';        //este es el correo del que sale la autenticacion del sistem (el que envia el correo para que el usuario recupere la contraseña)
                        $email->Password   = 'kbcu gjiu kjzj sbbw';                     //contraseña de aplicacion de google
                        $email->SMTPSecure = PHPMailer ::ENCRYPTION_STARTTLS;           //Hace que el correo se envíe de forma segura y encriptada.
                        $email->Port       = 587;                                       //TRADICIONALMENTE se usa el puerto 587
                        $email->CharSet    = "utf8";                                    //esto es para que reconozca los caracteres especiales de los correos              
                        //Destinatario y contenido del correo
                        $email->setFrom('luiscarlosprietobracho@gmail.com','Chayanne Torero');        //nombre de la persona del correo del que se esta enviando
                        $email->addAddress($verificarCorreo);
                        $email->isHTML(true);
                        $email->Subject = 'Recuperacion de Contraseña';   //asunto del correo que se esta enviando
                        $email->Body=
                        '<div style="max-width: 500px;margin: 40px auto; font-family: arial;">
                        <h2>🔒 Recuperar Contraseña</h2><hr style="border-top:3px solid #f37b18;">
                        <p style="font-size: 15px;">Hola, hemos recibido una solicitud para restablecer su contraseña, si fue usted de click aca </p>
                        <a href=http://localhost/proyecto/RecuperarContrasena/view/cambiarContrasena.php?Id='.$usuario_Id.'">Restablecer Contraseña</a>';                             //cuerpo del correo que se esta enviando
                        $email->send();                                                                                           //para enviar el correo
                        echo "<script> alert ('Correo enviado con exito');window.location.href='login.php';</script>";          //alert avisando que todo salio fine
                    }
                    catch(\Exception $error){               //mostrar un mensaje si hay algun error en el try
                                 echo "<script> alert ('Error al enviar el correo".$email->$error."');</script>";                    //mensaje de error si hay alguno en el try
                    }
                }
                else{
                    echo "<script> alert ('Error al recuperar el ID del usuario');</script>";     //error que se muestra si no coincide el ID del usuario
                    }
            }
             else{
                $errorCorreo="Correo Invalido";                                          //mensaje que se muestra si todo va wrong
            }
    } 
    catch (PDOException $error){
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
</head>
<body>
    <br><br><br><br><br>
    <center>
        <form action="#" method="POST">
            <H1>
                Recuperar contraseña
            </H1>
            Correo <input type="email" name="verificacion" placeholder="Correo Electronico"><br><br>
            <button type="submit">No enviar nada</button>
        </form>
        <?php                                                                                                      //apertura php
                        if(!empty($errorCorreo)){                                                                             //validacion de que si no esta vacio la variable $errorIngreso, que haga algo
                    ?>                                                                                                         <!--cierre php para crear estilos con el html-->
                    <p style="color:red; font-weight: bold; font-size: 20px;"><?php echo $errorCorreo ?></p>                <!--estilos del html e imprimimos la variable error dentro de un parrafo del html-->
                    <?php                                                                                                   //volvemos a brir el php para cerrar la llave que falta del if y cerramos                                                                            
                    }
                    ?>
        <?php
                     if(!empty($errorCorrecto)){                                                                             //validacion de que si no esta vacio la variable $errorIngreso, que haga algo
                    ?>                                                                                                         <!--cierre php para crear estilos con el html-->
                    <p style="color:green; font-weight: bold; font-size: 20px;"><?php echo $errorCorrecto ?></p>                <!--estilos del html e imprimimos la variable error dentro de un parrafo del html-->
                    <?php                                                                                                   //volvemos a brir el php para cerrar la llave que falta del if y cerramos                                                                            
                    }
                    ?>
    </center>
</body>
</html>