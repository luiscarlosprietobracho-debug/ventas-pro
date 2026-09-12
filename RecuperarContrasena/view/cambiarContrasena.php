<?php
session_start();
require '../controller/conexionpdo.php';   //se coloca con dos puntos para que se regrese una carpeta atras, luego se le dice a que carpeta ingresar, y despues cual archivo llamar
$db = new Database();                      //una variable para almacenar la nueva instancia de la base de datos
$conexion= $db->conectar();               //una variable que ejecuta la conexcion a la base de datos
    if ($conexion==null){                 //validacion de si no se logra conectar a la base de datos muestre un mensaje de error
        die ("Error de la conexion");    //este es el mensaje de error, die es para decirle que ahi muere la funcion
    }

if(isset($_GET['Id'])){                  //validando que exista el id del usuario
    $usuarioID=$_GET['Id'];             //se mete el id del usurio en esta nueva variable
    }
else{ 
        if(isset($_POST['usuario_Id_Formulario'])){        
            $usuarioID = $_POST['usuario_Id_Formulario'];
            }

    }
if (!$usuarioID){                                                                       
    die("<script>alert('Id de usuario no valido')</script>");
    }

if($_SERVER['REQUEST_METHOD']==='POST'){                     //validamos que el servidor haya enviado el dato por el metodo post
    $nueva_Password=$_POST['password'];                      // se trae el dato del formulario con el name password
    $confirmar_Password=$_POST['confirmarPassword'];        // se trae el dato del formulario con el name confirmarPassword
    if($nueva_Password===""|| $confirmar_Password===""){    //validando que no esten vacios los campos del formulario (aunque igual ambos tienen required asi que es medio innecesaria esta validacion)
    echo "<script>alert('Por favor complete todos los campos')</script>";  //mensaje diciendole al usuario final que los campos debe completarlos todos
    }
    else{
       // $consulta="SELECT token_request FROM registro WHERE Id=:id";  // aqui creamos una variable para guardar una consulta para trabajar cuando los dos campos del formulario se envien completos
        $seleccionTokenId=$conexion->prepare("SELECT token_request FROM registro WHERE Id=:id");          //aqui se crea una nueva variable con la consulta ya preparada para trabajar, tambien habriamos podido poner la variable que almacena la consulta en caso de haberlo trabajado asi
        $seleccionTokenId->bindParam(':id',$usuarioID, PDO::PARAM_INT); //NO ES OBLIGATORIO TENER EL PDO, para blindarlo, pero al tenerlo lo obligamos a validarlo primero como numero
        $seleccionTokenId->execute();                                   //ejecuta la consulta ya preparada y blindada
        $datosToken=$seleccionTokenId->fetch(PDO::FETCH_ASSOC);
        if (!$datosToken){                                      //validacion de que si no encontro el id entonces muestre un mensaje
            die("<script>alert('Usuairo no encontrado')</script>");
        }
        if ($datosToken['token_request']!=1){                                              //validacion de que si ya utilizo el cambio de clave desde el correo, entonces ya no lo vuelva a dejar cambiarla hasta pedir un correo nuevo con cambio de clave
            die("<script>alert('Usted ya uso la oportunidad de cambiar la contraseña, su token ya expiro')</script>");  //mensaje de que ya cambio la clave usando este correo
        }
        if ($nueva_Password!==$confirmar_Password){                               //validacion de que la contraseña nueva, y su confirmacion, no son exactamente iguales
            die("<script>alert('Las contraseñas no son iguales')</script>");        //mensaje que se muestre cuando las contraseña, y su confirmacion, no sean iguales
        }
        else{ 
            $actualizarClave=$conexion->prepare('UPDATE registro SET Contrasena=:contrasena, token_request=0 WHERE Id=:id');   //se crea una variable, en donde se actualizan los registros de contraseña del usuario en la base de datos
            $actualizarClave->BindParam(':contrasena',$nueva_Password, PDO::PARAM_STR);                                       //se estan blindado los parametros con los que trabaja la consulta
            $actualizarClave->BindParam(':id',$usuarioID, PDO::PARAM_INT);                                                    //se estan blindado los parametros con los que trabaja la consulta
            $actualizarClave->execute();                                                                                //se esta ejecutando la consulta
            if($actualizarClave->rowCount()>0){                                                                     // el cosito de -> es funcion flecha. el rowCount es para que cuente la cantidad de filas que hay en la variable, la consulta preparada ya ejecutada
                    echo "<script>alert('Contraseña cambiada con éxito'); window.close();</script>";         //mensaje que se muestra cuando todo salio bien
                    exit();                  //cerrando la funcion/validacion
            }
            else{
                  echo "<script>alert('Contraseña NO fue cambiada');</script>";        //mensaje que se muestra cuando todo salio mal muy mal
            }
            }
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Constraseña</title>
</head>

<body>
    <br><br><br><br><br>
    <center>
        <form action="#" method="POST">
            <table border="2">
                <H1>Cambio de Contraseña</H1><br><br>
                <tr>
                    <td>Nueva Constraseña</td>
                    <td><input type="password" name="password" required placeholder="Nueva Contraseña"></td>
                </tr>
                <tr>
                    <td>Confirmar Constraseña</td>
                    <td><input type="password" name="confirmarPassword" required placeholder="Confirmar Contraseña">
                    </td>
                </tr>

            </table>
            <br>
            <button type="submit" style="background-color:red; color:white;
                    padding:9px 30px; border-radius:10px">Confirmar</button>
            <!--como boton tambien funciona el submit-->
        </form>
    </center>
</body>

</html>