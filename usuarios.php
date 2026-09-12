<?php
// ==========================================================
// 1. INICIAR SESIÓN Y CONEXIÓN
// ==========================================================

// Se incluye el archivo de conexión PDO a la base de datos
include_once("conexionpdo.php");

// Se inicia la sesión PHP para poder usar variables como $_SESSION
session_start();


// ==========================================================
// 2. VALIDAR QUE SEA ADMIN
// ==========================================================

// Protección de acceso:
// - Verifica que la sesión tenga la variable 'rol'
// - Que el rol sea 1 (Administrador)
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 1) {
    
    // Si no cumple, redirige al login
    header("location: login.php");

    // Termina la ejecución para evitar que un usuario no autorizado vea el contenido
    exit(); 
}


// ==========================================================
// 3. CONEXIÓN A BASE DE DATOS
// ==========================================================

// Se instancia la clase Database y se conecta usando PDO
$contenedorPDO = (new Database())->conectar();


// ==========================================================
// 4. CREAR USUARIO
// ==========================================================

// Se valida si se envió el formulario para crear usuario
if (isset($_POST['crear_usuario'])) {

    $claveNueva = $_POST['clave'];

    // Validación de contraseña segura:
    // - Al menos una letra minúscula
    // - Al menos una letra mayúscula
    // - Al menos un número
    // - Al menos un carácter especial
    // - Mínimo 8 caracteres
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/', $claveNueva)) {
        die("<script>alert('La contraseña debe tener mínimo 8 caracteres, mayúscula, minúscula, número y símbolo'); window.history.back();</script>");
    }

    // Encriptación de la contraseña con password_hash
    $claveHash = password_hash($claveNueva, PASSWORD_DEFAULT);

    // ------------------------------------------------------
    // FOTO POR DEFECTO
    // ------------------------------------------------------
    // Todos los usuarios nuevos reciben una imagen por defecto
    $foto = "incognito.jpg";

    // ------------------------------------------------------
    // INSERTAR EN LA BASE DE DATOS
    // ------------------------------------------------------
    $sql = $contenedorPDO->prepare("
        INSERT INTO usuarios 
        (nomusuario, clave, idrol, email, Foto, direccion) 
        VALUES (?,?,?,?,?,?)
    ");

    $sql->execute([
        $_POST['usuario'],
        $claveHash,
        $_POST['rol'],
        $_POST['email'],
        $foto,
        $_POST['direccion']
    ]);

    // Feedback al usuario y recarga de la página
    echo "<script>alert('Usuario creado');window.location='usuarios.php';</script>";
    exit();
}


// ==========================================================
// 5. ELIMINAR USUARIO
// ==========================================================

// Se verifica si se recibe un ID por GET para eliminar
if (isset($_GET['eliminar'])) {

    /*
    IMPORTANTE:
    - Se debería validar si el usuario realmente existe
    - Actualmente se elimina directamente, pero es buena práctica protegerlo
    */

    $sql = $contenedorPDO->prepare("DELETE FROM usuarios WHERE id=?");
    $sql->execute([$_GET['eliminar']]);

    // Recarga de página después de la eliminación
    header("location: usuarios.php");
    exit();
}


// ==========================================================
// 6. ACTUALIZAR USUARIO
// ==========================================================

if (isset($_POST['actualizar_usuario'])) {

    $id = $_POST['id'];

    // ------------------------------------------------------
    // TRAER CONTRASEÑA ACTUAL
    // ------------------------------------------------------
    // Necesario para mantener la contraseña si el admin no la cambia
    $sql = $contenedorPDO->prepare("SELECT clave FROM usuarios WHERE id=?");
    $sql->execute([$id]);
    $actual = $sql->fetch(PDO::FETCH_ASSOC);

    // ------------------------------------------------------
    // MANEJO INTELIGENTE DE CONTRASEÑA
    // ------------------------------------------------------
    // Lógica:
    // - Si el campo clave está vacío → NO se cambia
    // - Si tiene valor → validar y encriptar
    if (empty($_POST['clave'])) {
        $claveFinal = $actual['clave'];
    } else {

        $claveNueva = $_POST['clave'];

        // Validación de contraseña segura
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/', $claveNueva)) {
            die("<script>alert('La contraseña debe tener mínimo 8 caracteres, mayúscula, minúscula, número y símbolo'); window.history.back();</script>");
        }

        // Encriptación de la nueva contraseña
        $claveFinal = password_hash($claveNueva, PASSWORD_DEFAULT);
    }

    // ------------------------------------------------------
    // ACTUALIZAR DATOS EN LA BASE
    // ------------------------------------------------------
    $sql = $contenedorPDO->prepare("
        UPDATE usuarios 
        SET nomusuario=?, clave=?, idrol=?, email=?, direccion=? 
        WHERE id=?
    ");

// Obtener el correo
    $correo = $_POST['email'];

    // VALIDACIÓN DE CORREO
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) { //el FILTER_VALIDATE_es una funcion de php que valida el contenido dentro de la variable
    echo "<script>alert('Correo no válido'); window.history.back();</script>"; //el window.history.back() refresca la pagina, manteniendo los datos del formulario
    exit();
}

    $sql->execute([
        $_POST['usuario'],
        $claveFinal,
        $_POST['rol'],
        $_POST['email'],
        $_POST['direccion'],
        $id
    ]);

    // Feedback al usuario y recarga
    echo "<script>alert('Usuario actualizado');window.location='usuarios.php';</script>";
    exit();
}


// ==========================================================
// 7. PAGINACIÓN
// ==========================================================

// Número de registros por página
$porPagina = 15;

// Página actual por defecto
$pagina = 1;

// Se obtiene el número de página desde GET
if(isset($_GET['pagina'])){
    $pagina = intval($_GET['pagina']);

    // Evitar valores negativos o inválidos
    if($pagina < 1){
        $pagina = 1;
    }
}

// Calcular OFFSET para la consulta LIMIT
$inicio = ($pagina - 1) * $porPagina;


// ==========================================================
// 8. TRAER USUARIOS LIMITADOS
// ==========================================================

$sql = $contenedorPDO->prepare("
    SELECT * FROM usuarios 
    LIMIT :inicio, :cantidad
");

// IMPORTANTE:
// - bindParam con PDO::PARAM_INT porque LIMIT no acepta strings
$sql->bindParam(':inicio', $inicio, PDO::PARAM_INT);
$sql->bindParam(':cantidad', $porPagina, PDO::PARAM_INT);

$sql->execute();

// Array de usuarios a mostrar en la tabla
$usuarios = $sql->fetchAll(PDO::FETCH_ASSOC);


// ==========================================================
// 9. TOTAL DE REGISTROS
// ==========================================================

// Para calcular la paginación
$totalRegistros = $contenedorPDO
    ->query("SELECT COUNT(*) FROM usuarios")
    ->fetchColumn();

// Número total de páginas (ceil redondea hacia arriba)
$totalPaginas = ceil($totalRegistros / $porPagina);


// ==========================================================
// 10. USUARIO A EDITAR
// ==========================================================

$usuarioEditar = null;

// Si se recibe un ID por GET para editar
if (isset($_GET['editar'])) {

    $sql = $contenedorPDO->prepare("SELECT * FROM usuarios WHERE id=?");
    $sql->execute([$_GET['editar']]);

    $usuarioEditar = $sql->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Gestión de Usuarios</title>
</head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="css/usuarios.css">
</head>

<body>


<!-- HEADER -->
<header class="estilosheader">

<div class="container d-flex justify-content-center">
<img class="ContIMG text-center" src="img/VENTAS_PRO_remove.png"
class="img-fluid">
</div>
</header>


<!-- NAVBAR PRINCIPAL -->

<nav class="navbar navbar-expand-lg navbar-dark colorNavbar">
<div class="container-fluid px-4 justify-content-between">

<!-- TEXTO IZQUIERDA -->
    <div class="d-flex flex-column">
        <h1 class="titulo-navbar">Bienvenido <span class="subtitulo-navbar">Panel administrador, Aquí puedes ver, crear y eliminar usuarios.</span></h1>
    </div>

<a class="navbar-brand fw-bold d-flex align-items-center" href="cliente.php"></a>

<!-- ICONOS FUNCIONALES -->
<div class="d-flex align-items-center gap-4">

    <div class="text-center eleven">
        <i class="bi bi-person fs-5" style="color: orange;"></i>
        <div style="font-size:12px;">
            <a class="colorLinks" href="perfil_admin.php"><span class="Mcategorias">Perfil</span></a>
        </div>
    </div>

    <div class="text-center eleven">
        <i class="bi bi-arrow-left-circle fs-5" style="color: orange;"></i>
        <div style="font-size:12px;">
            <a class="colorLinks" href="administrador.php"><span class="Mcategorias">Volver al panel</span></a>
        </div>
    </div>


    <div class="text-center eleven">
        <form action="login.php" method="post" style="display:inline;">
            <button type="submit" name="cerrar_sesion" class=" p-0 text-white border-0 bg-transparent">
                <i class="bi bi-box-arrow-right fs-5" style="color: orange;"></i>
                <div style="font-size:12px;" class="colorLinks">
                    <span class="Mcategorias">Cerrar sesión</span>
                </div>
            </button>
        </form>
    </div>

</div>

</div>

</div>

</nav><br><br><!-- CIERRE DE CONTENIDO -->


<!-- BIENVENIDA ADMINISTRADOR -->
<div class="container my-1 mx-0">

    <div class="bienvenida-box conte d-flex align-items-center">

        <div class="foto-container me-3">
            <img src="imagenes/<?php echo $_SESSION['Foto']; ?>" class="foto-perfil">
        </div>

        <div>
            <h2 class="bienvenida-texto mb-1">
                Bienvenido, <span><?php echo $_SESSION['nomusuario']; ?></span> 👋
            </h2>

            <p class="subtexto mb-0">
                Panel administrador <strong>VentasPro</strong>
            </p>
        </div>

    </div>

</div><!-- cierre de contenido -->

<div>

<br>

<div class="d-flex gap-4 mt-4 <?= $usuarioEditar ? 'justify-content-center' : 'justify-content-center' ?>">
    <!-- FORMULARIO CREAR USUARIO -->

<div class="card-producto <?= $usuarioEditar ? 'ms-5 mt-4' : '' ?>">
    <h2 class="titulo-card">Crear Usuario</h2>

<form class="form-producto" method="POST">

<!-- Campos obligatorios -->
<label style="color: orange" for="usuario" >Nombre: </label>
<input type="text" name="usuario" placeholder="Nombre usuario" required><br><br>
<label style="color: orange" for="clave">Clave: </label>
<input type="password" name="clave" placeholder="Clave" required><br><br>
<label style="color: orange" for="email">Correo eléctronico: </label>
<input type="email" name="email" placeholder="Correo" required><br><br>
<label style="color: orange" for="direccion">Dirección: </label>
<input type="text" name="direccion" placeholder="Dirección" required><br><br>

<label style="color: orange" for="rol">Selecciona rol: </label>
<select name="rol" class="form-select" required>
    <option value="2">Cliente</option>
    <option value="3">Agente Solicitudes</option>
    <option value="4">Agente Envíos</option>
    
</select>

<br><br>

<input class="efectoBtn" type="submit" name="crear_usuario" value="Crear Usuario">
</form>
</div>

<!-- CIERRE DE CONTENIDO -->

<!-- FORMULARIO EDITAR -->

<?php if ($usuarioEditar) { ?>

<div class="card-producto ms-5 mt-4">
    <h3 class="titulo-card">Editar Usuario</h3>

<form class="form-producto" method="POST">

<input type="hidden" name="id" value="<?php echo $usuarioEditar['id']; ?>">

<label style="color: orange" for="usuario">Nombre: </label>
<input type="text" name="usuario" value="<?php echo $usuarioEditar['nomusuario']; ?>"><br><br>

<label style="color: orange" for="clave">clave: </label>
<input type="password" name="clave" placeholder="Dejar vacío para no cambiar"><br><br>

<label style="color: orange" for="email">email </label>
<input type="email" name="email" value="<?php echo $usuarioEditar['email']; ?>"><br><br>

<label style="color: orange" for="direccion">direccion </label>
<input type="text" name="direccion" value="<?php echo $usuarioEditar['direccion']; ?>"><br><br>

<label style="color: orange" for="rol">Selecciona rol: </label>
<div>
    <select name="rol" class="form-select" require>
    <option value="2" <?php if($usuarioEditar['idrol']==2) echo "selected"; ?>>Cliente</option>
    <option value="3" <?php if($usuarioEditar['idrol']==3) echo "selected"; ?>>Solicitudes</option>
    <option value="4" <?php if($usuarioEditar['idrol']==4) echo "selected"; ?>>Envíos</option>
</select>
</div>

<br><br>

<input class="efectoBtn" type="submit" name="actualizar_usuario" value="Actualizar">

</form>
</div>

<?php } ?> <!-- CIERRE DE CONTENIDO -->

</div>




<!-- TABLA DE USUARIOS -->

<table border="1" align="center">
<tr>
    <th>ID</th>
    <th>USUARIO</th>
    <th>ROL</th>
    <th>EMAIL</th>
    <th>ACCIONES</th>
</tr>

<?php foreach ($usuarios as $u) { ?>

<tr align="center">

<td style="color: orange"><?php echo $u['id']; ?></td>
<td><?php echo $u['nomusuario']; ?></td>

<td>
<?php
// Traducción de ID de rol a texto
if ($u['idrol'] == 2) {
    echo "Cliente";
} elseif ($u['idrol'] == 3) {
    echo "Solicitudes";
} elseif ($u['idrol'] == 4) {
    echo "Envíos";
} else {
    echo "Admin";
}
?>
</td>

<td><?php echo $u['email']; ?></td>

<td class="d-flex justify-content-center align-items-center gap-2">
<a class="btn" href="usuarios.php?editar=<?=$u['id']?>">Editar</a>

<a class="btn2" href="usuarios.php?eliminar=<?=$u['id']?>"
onclick="return confirm('¿Eliminar usuario?')">Eliminar</a>
</td>

</tr>

<?php } ?>

</table>

<br><br>

<!-- ====================================================== -->
<!-- PAGINACIÓN VISUAL -->
<!-- ====================================================== -->

<div class="paginacion" align="center">

<?php
// Botón anterior
if($pagina > 1){
    echo "<a href='usuarios.php?pagina=".($pagina-1)."'>Anterior</a> ";
}

// Números de páginas
for($i = 1; $i <= $totalPaginas; $i++){
    if($i == $pagina){
        echo "<b>$i</b> ";
    } else {
        echo "<a href='usuarios.php?pagina=$i'>$i</a> ";
    }
}

// Botón siguiente
if($pagina < $totalPaginas){
    echo "<a href='usuarios.php?pagina=".($pagina+1)."'>Siguiente</a>";
}
?>

</div><br>

<p class="paginastotal" align="center">Total de páginas: <?php echo $totalPaginas; ?></p>

</div>

</body>
</html>