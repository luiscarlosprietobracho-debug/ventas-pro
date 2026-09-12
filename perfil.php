<?php
include_once("conexionpdo.php");
session_start();
$contenedorPDO = (new Database())->conectar();

// Validar que el usuario esté logueado
if(!isset($_SESSION['idusuario'])){
    header("location: login.php");
    exit();
}

// Lógica para actualizar datos
if(isset($_POST['actualizar'])) {
    $editar_id = $_SESSION['idusuario'];

    // Obtener datos actuales
    $contenedor = $contenedorPDO->prepare("SELECT Foto, clave FROM usuarios WHERE id=?");
    $contenedor->execute([$editar_id]);
    $fila = $contenedor->fetch(PDO::FETCH_ASSOC);

    $fotoActual = $fila['Foto'];

    // Subir nueva foto si hay
    if(isset($_FILES['imagenXSubir']) && $_FILES['imagenXSubir']['error'] === 0){
        $extension = strtolower(pathinfo($_FILES['imagenXSubir']['name'], PATHINFO_EXTENSION));
        $permitidos = ['jpg','jpeg','png','gif','webp'];

        if(!in_array($extension, $permitidos)){
            die("Formato de imagen no permitido");
        }

        $fecha = date("d-m-Y_H-i-s");
        $nombreArchivo = "usuario_" . $editar_id . "_" . $fecha . "." . $extension;
        $ruta = "imagenes/" . $nombreArchivo;

        if(move_uploaded_file($_FILES['imagenXSubir']['tmp_name'], $ruta)){
            if(!empty($fotoActual) && file_exists("imagenes/".$fotoActual)){
                unlink("imagenes/".$fotoActual);
            }
            $fotoActual = $nombreArchivo;
        }
    }

    // Contraseña
if(empty($_POST['clave'])){
    // No cambia la contraseña
    $claveFinal = $fila['clave'];
} else {

    $claveNueva = $_POST['clave'];

    // VALIDAR SEGURIDAD DE LA CONTRASEÑA
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/', $claveNueva)) {
        die("<script>alert('La contraseña debe tener mínimo 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial'); window.history.back();</script>");
    }

    // Si pasa la validación → encriptar
    $claveFinal = password_hash($claveNueva, PASSWORD_DEFAULT);
}

// VALIDACIÓN DE CORREO
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) { //el FILTER_VALIDATE_es una funcion de php que valida el contenido dentro de la variable
    echo "<script>alert('Correo no válido'); window.history.back();</script>"; //el window.history.back() refresca la pagina, manteniendo los datos del formulario
    exit();
}

    // Actualizar BD
    $contenedor = $contenedorPDO->prepare("UPDATE usuarios SET nomusuario=?, clave=?, email=?, direccion=?, Foto=? WHERE id=?");
    $contenedor->execute([
        $_POST['usuario'],
        $claveFinal,
        $_POST['email'],
        $_POST['direccion'],
        $fotoActual,
        $editar_id
    ]);

    // Actualizar sesión
    $_SESSION['nomusuario'] = $_POST['usuario'];
    $_SESSION['Foto'] = $fotoActual;

    header("location: perfil.php");
    exit();
}

// Obtener datos para mostrar en el formulario
$editar_id = $_SESSION['idusuario'];
$contenedor = $contenedorPDO->prepare("SELECT * FROM usuarios WHERE id=?");
$contenedor->execute([$editar_id]);
$datosEdicion = $contenedor->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Perfil de Usuario</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/perfil.css">
</head>
<body>

<div class="container my-5">
    <div class="card fondoo p-4">
        <h3 style=" background: linear-gradient(to right, #FF512F 0%, #F09819 51%, #FF512F 100%);
                    background-clip: text;              /* estándar */
                    -webkit-background-clip: text;      /* Chrome / Edge */
                    color: transparent;  ;" 
                    class="mb-4 text-center">Editar Perfil</h3>

        <div class="row">
            <div class="col-md-6">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label style=" color: white;"  class="form-label">Nombre</label>
                        <input type="text" name="usuario" class="form-control" value="<?= $datosEdicion['nomusuario'] ?>" required>
                    </div>

                    <div class="mb-3">
                        <label style=" color: white;"  class="form-label">Contraseña</label>
                        <input type="password" name="clave" class="form-control" placeholder="Deje vacío para no cambiar">
                    </div>

                    <div class="mb-3">
                        <label style=" color: white;"  class="form-label">Correo</label>
                        <input type="email" name="email" class="form-control" value="<?= $datosEdicion['email'] ?>" required>
                    </div>

                    <div class="mb-3">
                        <label style=" color: white;"  class="form-label">Dirección</label>
                        <input type="text" name="direccion" class="form-control" value="<?= $datosEdicion['direccion'] ?>">
                    </div>

                    <div class="mb-3">
                        <label style=" color: white;"  class="form-label">Foto</label>
                        <input type="file" name="imagenXSubir" class="form-control">
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" name="actualizar" class="btn1">Actualizar datos</button>
                        <a class="btn" href="cliente.php">Volver al Panel</a>
                    </div>
                </form>
            </div>

            <div class="col-md-6 d-flex justify-content-center align-items-center">
                <img src="imagenes/<?= !empty($datosEdicion['Foto']) ? $datosEdicion['Foto'] : 'incognito.png' ?>" class="profile-img">
            </div>
        </div>
    </div>
</div>

</body>
</html>