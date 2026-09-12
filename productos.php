<?php
// ======================= CONFIGURACIÓN INICIAL =======================

// Incluye el archivo de conexión a la base de datos
include_once("conexionpdo.php");

// Inicia o reanuda la sesión del usuario
session_start();

// Validar que el usuario tenga rol de administrador (rol = 1)
// Si no cumple, se redirige al login
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 1) {
    header("location: login.php");
    exit();
}

// Crea la conexión PDO a la base de datos
$pdo = (new Database())->conectar();


// ======================= INSERTAR PRODUCTO =======================

// Verifica si se envió el formulario de insertar producto
if(isset($_POST['insertar'])) {

    // Nombre por defecto de la imagen si no se sube ninguna
    $imagenNombre = "sinimagen.png";

    // Verifica si se envió un archivo y no hubo errores
    if(isset($_FILES['foto']) && $_FILES['foto']['error'] === 0){

        // Obtiene la extensión del archivo (jpg, png, etc.)
        $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);

        // Genera un nombre único para evitar conflictos
        $nombreArchivo = "producto_".time().".".$extension;

        // Define la ruta donde se guardará la imagen
        $ruta = "imagenes/".$nombreArchivo;

        // Mueve el archivo desde la carpeta temporal al servidor
        if(move_uploaded_file($_FILES['foto']['tmp_name'], $ruta)){
            $imagenNombre = $nombreArchivo; // Guarda el nombre si fue exitoso
        }
    }

    // Prepara la consulta SQL para insertar el producto
    $sql = $pdo->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, foto) VALUES (?, ?, ?, ?, ?)");

    // Ejecuta la consulta con los datos del formulario
    $sql->execute([
        $_POST['nombre'],
        $_POST['descripcion'],
        $_POST['precio'],
        $_POST['stock'],
        $imagenNombre
    ]);

    // Redirige para evitar reenvío del formulario
    header("location: productos.php");
    exit();
}


// ======================= ELIMINAR PRODUCTO =======================

// Verifica si se recibe un ID por GET para eliminar
if(isset($_GET['eliminar'])){
    $sql = $pdo->prepare("DELETE FROM productos WHERE id=?");
    $sql->execute([$_GET['eliminar']]);

    header("location: productos.php");
    exit();
}


// ======================= PAGINACIÓN =======================

// Cantidad de productos por página
$porPagina = 10;

// Página actual (por defecto 1)
$pagina = 1;

// Verifica si viene el número de página por GET
if(isset($_GET['pagina'])){
    $pagina = intval($_GET['pagina']); // Convierte a entero

    // Valida que no sea menor a 1
    if($pagina < 1){
        $pagina = 1;
    }
}

// Calcula el offset (desde qué registro empezar)
$inicio = ($pagina - 1) * $porPagina;


// ======================= CONSULTA PRODUCTOS =======================

// Prepara consulta con límite para paginación
$sql = $pdo->prepare("SELECT * FROM productos LIMIT :inicio, :cantidad");

// Asocia parámetros como enteros (IMPORTANTE para LIMIT)
$sql->bindParam(':inicio', $inicio, PDO::PARAM_INT);
$sql->bindParam(':cantidad', $porPagina, PDO::PARAM_INT);

// Ejecuta la consulta
$sql->execute();

// Obtiene todos los productos como array asociativo
$productos = $sql->fetchAll(PDO::FETCH_ASSOC);


// ======================= TOTAL DE REGISTROS =======================

// Cuenta total de productos
$totalRegistros = $pdo->query("SELECT COUNT(*) FROM productos")->fetchColumn();

// Calcula número total de páginas
$totalPaginas = ceil($totalRegistros / $porPagina);


// ======================= EDITAR PRODUCTO =======================

// Variable para almacenar el producto a editar
$productoEditar = null;

// Si se recibe un ID para editar
if(isset($_GET['editar'])){
    $sql = $pdo->prepare("SELECT * FROM productos WHERE id=?");
    $sql->execute([$_GET['editar']]);

    // Obtiene los datos del producto
    $productoEditar = $sql->fetch(PDO::FETCH_ASSOC);
}


// ======================= ACTUALIZAR PRODUCTO =======================

// Verifica si se envió el formulario de actualización
if(isset($_POST['actualizar'])){

    // ID del producto a actualizar
    $id = $_POST['id'];

    // Consulta para obtener la imagen actual
    $consulta = $pdo->prepare("SELECT foto FROM productos WHERE id=?");
    $consulta->execute([$id]);

    $datos = $consulta->fetch(PDO::FETCH_ASSOC);
    $imagenActual = $datos['foto'];

    // Verifica si se subió una nueva imagen
    if(isset($_FILES['foto']) && $_FILES['foto']['error'] === 0){

        $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nombreArchivo = "producto_".time().".".$extension;
        $ruta = "imagenes/".$nombreArchivo;

        // Si la nueva imagen se sube correctamente
        if(move_uploaded_file($_FILES['foto']['tmp_name'], $ruta)){

            // Elimina la imagen anterior del servidor si existe
            if(file_exists("imagenes/".$imagenActual)){
                unlink("imagenes/".$imagenActual);
            }

            // Actualiza el nombre de la imagen
            $imagenActual = $nombreArchivo;
        }
    }

    // Actualiza el producto en la base de datos
    $sql = $pdo->prepare("UPDATE productos SET nombre=?, descripcion=?, precio=?, stock=?, foto=? WHERE id=?");

    $sql->execute([
        $_POST['nombre'],
        $_POST['descripcion'],
        $_POST['precio'],
        $_POST['stock'],
        $imagenActual,
        $id
    ]);

    // Redirección para evitar duplicación
    header("location: productos.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestión de Productos</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="css/productos.css">
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
        <h1 class="titulo-navbar">Bienvenido <span class="subtitulo-navbar">Panel administrador, Aquí puedes ver, crear y eliminar productos.</span></h1>
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
                Panel administrador <strong>Ventas-Pro</strong>
            </p>
        </div>

    </div>

</div><!-- cierre de contenido -->

<div class="d-flex gap-4 mt-4 <?= $productoEditar ? 'justify-content-center' : 'justify-content-center' ?>">

    <!-- CREAR -->
    <div class="card-producto <?= $productoEditar ? 'ms-5 mt-4' : '' ?>">
        <h2 class="titulo-card">Crear Productos</h2>

        <form class="form-producto" method="POST" enctype="multipart/form-data">
            <label style="color: orange">Nombre: </label>
            <input type="text" name="nombre" required placeholder="Nombre del producto."><br>

            <label style="color: orange">Descripción: </label>
            <input type="text" name="descripcion" required placeholder="Descripción del producto."><br>

            <label style="color: orange">Precio: </label>
            <input type="number" step="0.01" name="precio" required placeholder="Precio del producto."><br>

            <label style="color: orange">Stock: </label>
            <input type="number" name="stock" required placeholder="Stock del producto."><br>

            <label style="color: orange">Imagen: </label>
            <input type="file" name="foto"><br>

            <input class="efectoBtn" type="submit" name="insertar" value="Crear Producto">
        </form>
    </div>

    <!-- EDITAR -->
    <?php if($productoEditar){ ?>
    <div class="card-producto ms-5 mt-4">
        <h3 class="titulo-card">Editar Producto</h3>

        <form class="form-producto" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $productoEditar['id'] ?>">

            <label style="color: orange">Nombre: </label>
            <input type="text" name="nombre" value="<?= $productoEditar['nombre'] ?>"><br>

            <label style="color: orange">Descripción: </label>
            <input type="text" name="descripcion" value="<?= $productoEditar['descripcion'] ?>"><br>

            <label style="color: orange">Precio: </label>
            <input type="number" step="0.01" name="precio" value="<?= $productoEditar['precio'] ?>"><br>

            <label style="color: orange">Stock: </label>
            <input type="number" name="stock" value="<?= $productoEditar['stock'] ?>"><br>

            <label style="color: orange">Imagen nueva: </label>
            <input type="file" name="foto"><br>

            <input class="efectoBtn" type="submit" name="actualizar" value="Actualizar">
        </form>
    </div>
    <?php } ?>

</div>


<br>

<table border="3" align="center">
<tr>
    <th>ID</th>
    <th>Nombre</th>
    <th>Descripción</th>
    <th>Precio</th>
    <th>Stock</th>
    <th>Imagen</th>
    <th>Editar</th>
    <th>Eliminar</th>
</tr>

<?php foreach($productos as $fila){ ?>
<tr align="center">
    <td style="color: orange"><?= $fila['id'] ?></td>
    <td><?= htmlspecialchars($fila['nombre']) ?></td>
    <td><?= htmlspecialchars($fila['descripcion']) ?></td>
    <td><?= $fila['precio'] ?></td>
    <td><?= $fila['stock'] ?></td>
    <td>
        <?php if(!empty($fila['foto'])){ ?>
<img style="
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: 50%; 
    border: 2px solid #ff7b00;
    box-shadow: 0 0 8px rgba(255,123,0,0.5);
    transition: 0.3s;
"   src="imagenes/<?= $fila['foto'] ?>" width="80">
        <?php } ?>
    </td>
    <td><a class="btn" href="productos.php?editar=<?= $fila['id'] ?>">Editar</a></td>
    <td><a class="btn2" href="productos.php?eliminar=<?= $fila['id'] ?>" onclick="return confirm('¿Desea eliminar producto?')">Eliminar</a></td>
</tr>
<?php } ?>
</table>

<br>

<div align="center" class="paginacion">

<?php
// BOTÓN ANTERIOR
if($pagina > 1){
    echo "<a href='productos.php?pagina=".($pagina-1)."'>Anterior</a> ";
}

// NÚMEROS DE PÁGINA
for($i = 1; $i <= $totalPaginas; $i++){

    if($i == $pagina){
        // página actual
        echo "<b>$i</b> ";
    } else {
        echo "<a href='productos.php?pagina=$i'>$i</a> ";
    }
}

// BOTÓN SIGUIENTE
if($pagina < $totalPaginas){
    echo "<a href='productos.php?pagina=".($pagina+1)."'>Siguiente</a>";
}
?>

</div>

<p align="center" class="paginastotal">Total de páginas: <?php echo $totalPaginas; ?></p>

</body>
</html>