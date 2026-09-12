<?php
session_start();
require '../controller/conexionpdo.php';   //se coloca con dos puntos para que se regrese una carpeta atras, luego se le dice a que carpeta ingresar, y despues cual archivo llamar
$db = new Database();                      //una variable para almacenar la nueva instancia de la base de datos
$conexion= $db->conectar();               //una variable que ejecuta la conexcion a la base de datos
    if ($conexion==null){                 //validacion de si no se logra conectar a la base de datos muestre un mensaje de error
        die ("Error de la conexion");    //este es el mensaje de error, die es para decirle que ahi muere la funcion
    }

if(isset($_POST['cerrar_sesion'])){  //VALIDACION de que si le da a cerrar sesion, no solo se regrese, sino que tambien cierre la sesion
    session_unset();                 //esto es para cerrar la sesion
    session_destroy();               //esto es para destruir la sesion despues de cerrarla
    header('location: login.php');   //esto para que se rediriga al login
    exit();                          //esto es para terminar la funcion
}
$errorIngreso ="";                              //variable vacia para mostrar un error cuando no coincidan los datos del login con la DB
if(isset($_SESSION['Usuario'])){                //si el uusario esta logeado lo redirija
    header('location: paginaPrincipal.php');    //esto para que se rediriga a la pagina principal
    exit();
    }
if($_SERVER['REQUEST_METHOD']==='POST'&& isset($_POST['Usuario'])){                                                          //se valida si el metodo con el que se envia el dato al servidor desde el formulario es POST 
    $usuario=$_POST['Usuario'];                                                                                              //aca se trae el dato que se ingresa en el fomulario con el name de Usuario y se guarda en una nueva variable
    $contrasenia=$_POST['contrasenaf'];                                                                                      //aca se trae el dato que se ingresa en el fomulario con el name de contrasenaf y se guarda en una nueva variable
    $usuarioCredenciales ="SELECT * FROM registro WHERE (Usuario = :usuario OR Correo=:correo) AND Contrasena=:contrasena";  //aqui se esta creando la consulta preparada con la tabla de la base de datos
    $consultaCredenciales=$conexion->prepare($usuarioCredenciales);                                                          //creamos una variable donde se va a llamar la conexion para trabajar la consulta preparada
    $consultaCredenciales->bindParam(':usuario',$usuario);                                                                   //se blinda la variable en conjunto con la forma en que se esta usando en la consulta preparada
    $consultaCredenciales->bindParam(':correo',$usuario);                                                                    //se blinda la variable en conjunto con la forma en que se esta usando en la consulta preparada
    $consultaCredenciales->bindParam('contrasena',$contrasenia);                                                             //se blinda la variable en conjunto con la forma en que se esta usando en la consulta preparada
    $consultaCredenciales->execute();                                                                                        //se ejecuta la consulta preparada con la base de datos despues de haberse blindado los datos
        if($consultaCredenciales->rowCount()>0){                                                                             // el cosito de -> es funcion flecha. el rowCount es para que cuente la cantidad de filas que hay en la variable, la consulta preparada ya ejecutada
        $_SESSION['Usuario']=$usuario;                                                                                       //esto es para que coja el dato de $usuario y lo coloque dentro del usuairo de la sesion
        header("location: paginaPrincipal.php");                                                                             //esto es para que lo redirija a la pagina principal al cumplirse la condicion del if y hacer lo que viene antes
        exit();                                                                                                              //para cerrar la funcion
        }else{     
        $errorIngreso = "Usuario o contraseña incorrecta";                                                                   //sino se cumple el if, es decir, no hay registros en la tabla de la consulta preparada, o hubo algun problema y no se aplico la consulta preparada bien, muestre un mensaje
    }
}

?>
<!DOCTYPE html>                                                                                                             <!--apretura del html-->
<html lang="en">
<head>
    <meta charset="UTF-8">                                                                                                  <!--esto es para que el html reconozca los caracteres especiales (ñ!"#$%&/)-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>                                                                                                     <!--Titulo de la pagina en la barra de navegacion-->   
</head>

<body bgcolor="green">
    <br><br><br><br><br>
    <center>
        <table bgcolor="violet" cellpading="20" width="350">                                                                 <!--apertura de la tabla-->
            <tr>
                <td align="center">
                    <h1>                                                                                                    <!--apertura del titulo-->
                        Inicio de Sesion                                                                                     <!--contenido del titulo-->
                    </h1>                                                                                                   <!--cierre del titulo-->
                    <?php                                                                                                      //apertura php
                        if(!empty($errorIngreso)){                                                                             //validacion de que si no esta vacio la variable $errorIngreso, que haga algo
                    ?>                                                                                                         <!--cierre php para crear estilos con el html-->
                    <p style="color:black; font-weight: bold; font-size: 20px;"><?php echo $errorIngreso ?></p>                <!--estilos del html e imprimimos la variable error dentro de un parrafo del html-->
                    <?php                                                                                                   //volvemos a brir el php para cerrar la llave que falta del if y cerramos                                                                            
                    }
                    ?>
                    
                    <form action="login.php" method="POST">                                                                  <!--apertura del formulario-->
                        
                        Usuario    <input type="text" name="Usuario"><br><br>                                                    <!--campo del formualrio para ingresar la informacion del nombre del usuario-->
                        Contraseña <input type="password" name="contrasenaf"><br><br>                                        <!--campo del formualrio para ingresar la informacion de la contraseña del usuairo-->
                        <button type="submit" style="background-color:black; color:white;     
                        padding:9px 30px; border-radius:10px">Ingresar</button><br>                                         <!--como boton tambien funciona el submit, el resto son estilos-->
                        <br><a href="recuperarContrasena.php" 
                        style="text-decoration:none"
                        >Recuperar contraseña
                        </a>                                                                                                <!--direccionando al presionar este boton-->

                    </form>                                                                                                  <!--cierre del formulario-->
                </td>
            </tr>
        </table>                                                                                                            <!--cierre de la tabla-->
    </center>

</body>                                                                                                                     <!--cierre del cuerpo-->
</html>                                                                                                                     <!--cierre del html-->