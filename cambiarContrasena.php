<?php
session_start();
require 'conexionpdo.php';   //se coloca con dos puntos para que se regrese una carpeta atras, luego se le dice a que carpeta ingresar, y despues cual archivo llamar
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
    $nueva_Password = $_POST['password'];
    $confirmar_Password = $_POST['confirmarPassword'];        // se trae el dato del formulario con el name confirmarPassword
    // VALIDACIÓN DE CONTRASEÑA SEGURA
if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/', $nueva_Password)) {
    die("<script>alert('La contraseña debe tener mínimo 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial');</script>");
}
    
    if($nueva_Password===""|| $confirmar_Password===""){    //validando que no esten vacios los campos del formulario (aunque igual ambos tienen required asi que es medio innecesaria esta validacion)
    echo "<script>alert('Por favor complete todos los campos')</script>";  //mensaje diciendole al usuario final que los campos debe completarlos todos
    }
    else{
        $passwordHash = password_hash($nueva_Password, PASSWORD_DEFAULT);
       // $consulta="SELECT token_request FROM registro WHERE Id=:id";  // aqui creamos una variable para guardar una consulta para trabajar cuando los dos campos del formulario se envien completos
        $seleccionTokenId=$conexion->prepare("SELECT token_request FROM usuarios WHERE Id=:id");          //aqui se crea una nueva variable con la consulta ya preparada para trabajar, tambien habriamos podido poner la variable que almacena la consulta en caso de haberlo trabajado asi
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
            $actualizarClave=$conexion->prepare('UPDATE usuarios SET clave=:contrasena, token_request=0 WHERE id=:id');   //se crea una variable, en donde se actualizan los registros de contraseña del usuario en la base de datos
            $actualizarClave->BindParam(':contrasena',$passwordHash, PDO::PARAM_STR);                                       //se estan blindado los parametros con los que trabaja la consulta
            $actualizarClave->BindParam(':id',$usuarioID, PDO::PARAM_INT);                                                    //se estan blindado los parametros con los que trabaja la consulta
            $actualizarClave->execute();                                                                                //se esta ejecutando la consulta
            if($actualizarClave->rowCount()>0){                                                                     // el cosito de -> es funcion flecha. el rowCount es para que cuente la cantidad de filas que hay en la variable, la consulta preparada ya ejecutada
                    echo "<script>alert('Contraseña cambiada con éxito'); window.close();</script>";         //mensaje que se muestra cuando todo salio bien
                    header("location: login.php");
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
<html lang="es">

<head>
    <!-- Define la codificación de caracteres -->
    <meta charset="UTF-8">

    <!-- Hace que la página sea responsive (adaptable a celulares) -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Título de la pestaña -->
    <title>Cambiar Contraseña</title>

    <!-- ICONOS de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="css/cambiarContrasena.css">
</head>

<body>

    <!-- SCRIPT para validar contraseña segura antes de enviar -->
    <script>
function validarClaveSegura() {

    // Obtiene el valor del input de contraseña
    const clave = document.getElementById("password").value;

    // Expresión regular:
    // mínimo 8 caracteres, mayúscula, minúscula, número y símbolo
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/;

    // Si no cumple las condiciones
    if (!regex.test(clave)) {

        // Muestra alerta
        alert("La contraseña debe tener mínimo 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial.");

        return false; // Evita enviar el formulario
    }

    return true; // Permite enviar el formulario
}
</script>

    <!-- Contenedor principal -->
    <div class="contenedor">

        <!-- Formulario -->
        <form class="card" method="POST" onsubmit="return validarClaveSegura()">

            <!-- Título -->
            <h2 class="titulo">Cambio de Contraseña</h2>

            <!-- Campo oculto que guarda el ID del usuario -->
            <input type="hidden" name="usuario_Id_Formulario" value="<?php echo $usuarioID; ?>">

            <!-- INPUT NUEVA CONTRASEÑA -->
            <div class="input-group">

                <!-- Campo donde el usuario escribe la nueva contraseña -->
                <input type="password" id="password" name="password" required placeholder="Nueva Contraseña">

                <!-- Icono para mostrar/ocultar contraseña -->
                <i class="bi bi-eye toggle-password" onclick="togglePassword('password', this)"></i>
            </div>

            <!-- INPUT CONFIRMAR CONTRASEÑA -->
            <div class="input-group">

                <!-- Campo para confirmar la contraseña -->
                <input type="password" id="confirmarPassword" name="confirmarPassword" required placeholder="Confirmar Contraseña">

                <!-- Icono para mostrar/ocultar contraseña -->
                <i class="bi bi-eye toggle-password" onclick="togglePassword('confirmarPassword', this)"></i>
            </div>

            <!-- Botón para enviar el formulario -->
            <button type="submit" class="btn">Confirmar</button>

        </form>
    </div>

    <!-- SCRIPT para mostrar/ocultar contraseña -->
    <script>
        function togglePassword(id, icon){

            // Obtiene el input según el id
            let input = document.getElementById(id);

            // Si está oculta
            if(input.type === "password"){

                input.type = "text"; // La muestra

                // Cambia el icono
                icon.classList.remove("bi-eye");
                icon.classList.add("bi-eye-slash");

            } else {

                input.type = "password"; // La oculta

                // Cambia el icono
                icon.classList.remove("bi-eye-slash");
                icon.classList.add("bi-eye");
            }
        }
    </script>

</body>
</html>