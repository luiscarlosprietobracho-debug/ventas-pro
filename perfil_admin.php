<?php
// ============================================
// 1. INICIAR SESION Y CONEXION
// ============================================

// Se incluye el archivo que contiene la clase Database (conexión PDO)
include_once("conexionpdo.php");

// Se inicia la sesión para poder acceder a variables globales del usuario
session_start();


// ============================================
// 2. VALIDAR QUE SEA ADMIN
// ============================================

// Se valida:
// - Que exista la variable de sesión 'rol'
// - Que el rol sea 1 (administrador)
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 1) {

    // Redirige al login si no tiene permisos
    header("location: login.php");

    // Finaliza la ejecución para evitar acceso no autorizado
    exit();
}


// ============================================
// 3. CONEXION A LA BD
// ============================================

// Se crea una instancia de la clase Database y se establece la conexión
$contenedorPDO = (new Database())->conectar();


// ============================================
// 4. OBTENER ID DEL USUARIO ACTUAL
// ============================================

// Se obtiene el ID del usuario desde la sesión
// Esto garantiza que el usuario solo pueda modificar SU propio perfil
$id = $_SESSION['idusuario'];


// ============================================
// 5. TRAER DATOS DEL USUARIO
// ============================================

// Se prepara la consulta para obtener todos los datos del usuario actual
$sql = $contenedorPDO->prepare("SELECT * FROM usuarios WHERE id=?");

// Se ejecuta la consulta enviando el ID como parámetro
$sql->execute([$id]);

// Se obtiene el resultado como arreglo asociativo
$usuario = $sql->fetch(PDO::FETCH_ASSOC);


// ============================================
// 6. ACTUALIZAR DATOS
// ============================================

// Este bloque solo se ejecuta si el formulario fue enviado
if (isset($_POST['actualizar'])) {

    // ============================================
    // 6.1 TRAER DATOS ACTUALES (CLAVE Y FOTO)
    // ============================================

    // Se consulta la contraseña y foto actuales del usuario
    // Esto evita sobrescribir datos si no se modifican
    $sql = $contenedorPDO->prepare("SELECT clave, Foto FROM usuarios WHERE id=?");
    $sql->execute([$id]);
    $datos = $sql->fetch(PDO::FETCH_ASSOC);


    // ----------------------------------------
    // 6.2 MANEJO DE CONTRASEÑA
    // ----------------------------------------

    // Si el campo contraseña está vacío:
    if (empty($_POST['clave'])) {

        // Se conserva la contraseña actual (no se modifica)
        $claveFinal = $datos['clave'];

    } else {

        // Si el usuario ingresó una nueva contraseña:
        // Se cifra usando password_hash (seguridad)
        $claveFinal = password_hash($_POST['clave'], PASSWORD_DEFAULT);
    }


    // ----------------------------------------
    // 6.3 MANEJO DE IMAGEN DE PERFIL
    // ----------------------------------------

    // Se toma como base la foto actual
    $fotoActual = $datos['Foto'];

    // Se valida si se subió una nueva imagen correctamente
    if (isset($_FILES['imagenXSubir']) && $_FILES['imagenXSubir']['error'] === 0) {

        // Se obtiene la extensión del archivo (jpg, png, etc.)
        $extension = pathinfo($_FILES['imagenXSubir']['name'], PATHINFO_EXTENSION);

        // Se genera una marca de tiempo para evitar nombres duplicados
        $fecha = date("d-m-Y_H-i-s");

        // Se construye un nombre único para el archivo
        $nombreArchivo = "usuario_" . $id . "_" . $fecha . "." . $extension;

        // Ruta destino donde se guardará la imagen
        $ruta = "imagenes/" . $nombreArchivo;

        // Se mueve el archivo desde la carpeta temporal al servidor
        if (move_uploaded_file($_FILES['imagenXSubir']['tmp_name'], $ruta)) {

            // ============================================
            // ELIMINAR IMAGEN ANTERIOR
            // ============================================

            // Se elimina la imagen anterior si existe
            if (!empty($fotoActual) && file_exists("imagenes/" . $fotoActual)) {

                unlink("imagenes/" . $fotoActual);
            }

            // Se actualiza la variable con el nuevo nombre
            $fotoActual = $nombreArchivo;
        }
    }


    // ----------------------------------------
    // 6.4 ACTUALIZAR EN BASE DE DATOS
    // ----------------------------------------

    // Se prepara la consulta UPDATE con los nuevos datos
    $sql = $contenedorPDO->prepare("UPDATE usuarios 
        SET nomusuario=?, clave=?, email=?, direccion=?, Foto=? 
        WHERE id=?");

// Obtener el correo
    $correo = $_POST['email'];

    // VALIDACIÓN DE CORREO
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) { //el FILTER_VALIDATE_es una funcion de php que valida el contenido dentro de la variable
    echo "<script>alert('Correo no válido'); window.history.back();</script>"; //el window.history.back() refresca la pagina, manteniendo los datos del formulario
    exit();
}

    // Se ejecuta la consulta con los valores correspondientes
    $sql->execute([
        $_POST['usuario'],   // nombre actualizado
        $claveFinal,         // contraseña (nueva o anterior)
        $_POST['email'],     // correo actualizado
        $_POST['direccion'], // dirección actualizada
        $fotoActual,         // foto nueva o existente
        $id                  // ID del usuario
    ]);


    // ============================================
    // 6.5 ACTUALIZAR DATOS EN SESIÓN
    // ============================================

    // Se actualizan los datos en sesión para reflejar cambios inmediatos
    $_SESSION['nomusuario'] = $_POST['usuario'];
    $_SESSION['Foto'] = $fotoActual;


    // ============================================
    // 6.6 MENSAJE Y REDIRECCION
    // ============================================

    // Muestra mensaje de éxito y recarga la página
    echo "<script>alert('Datos actualizados');window.location='perfil_admin.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Perfil Administrador</title>

<!-- Bootstrap para estilos y layout -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- CSS personalizado -->
<link rel="stylesheet" href="css/perfil_admin.css">

</head>

<body>

<!-- CONTENEDOR PRINCIPAL -->
<div class="container my-5">
    <div class="card fondoo p-4">

        <!-- TITULO -->
        <h3 class="mb-4 text-center"
        style="background: linear-gradient(to right, #FF512F 0%, #F09819 51%, #FF512F 100%);
               -webkit-background-clip: text;
               color: transparent;">
            Perfil de administrador
        </h3>

        <div class="row">

            <!-- FORMULARIO -->
            <div class="col-md-6">
                <form method="POST" enctype="multipart/form-data">

                    <!-- ID oculto del usuario -->
                    <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">

                    <!-- NOMBRE -->
                    <div class="mb-3">
                        <label class="form-label textoLabel">Nombre</label>
                        <input type="text" name="usuario"
                        value="<?php echo $usuario['nomusuario']; ?>"
                        class="form-control" required>
                    </div>

                    <!-- CONTRASEÑA -->
                    <div class="mb-3">
                        <label class="form-label textoLabel">Contraseña</label>
                        <input type="password" name="clave"
                        class="form-control"
                        placeholder="Dejar vacío para no cambiar">
                    </div>

                    <!-- EMAIL -->
                    <div class="mb-3">
                        <label class="form-label textoLabel">Correo</label>
                        <input type="email" name="email"
                        value="<?php echo $usuario['email']; ?>"
                        class="form-control" required>
                    </div>

                    <!-- DIRECCIÓN -->
                    <div class="mb-3">
                        <label class="form-label textoLabel">Dirección</label>
                        <input type="text" name="direccion"
                        value="<?php echo $usuario['direccion']; ?>"
                        class="form-control" required>
                    </div>

                    <!-- FOTO -->
                    <div class="mb-3">
                        <label class="form-label textoLabel">Foto</label>
                        <input type="file" name="imagenXSubir" class="form-control">
                    </div>

                    <!-- BOTONES -->
                    <div class="d-flex justify-content-between">
                        <button type="submit" name="actualizar" class="btn1">
                            Actualizar datos
                        </button>
                        <a href="administrador.php" class="btn">Volver al panel</a>
                    </div>
                </form>
            </div>

            <!-- IMAGEN DE PERFIL -->
            <div class="col-md-6 d-flex justify-content-center align-items-center">

                <!-- Muestra la imagen del usuario o una por defecto -->
                <img src="imagenes/<?php echo !empty($usuario['Foto']) ? $usuario['Foto'] : 'incognito.png'; ?>" 
                class="profile-img">

            </div>

        </div>
    </div>
</div>

</body>
</html>