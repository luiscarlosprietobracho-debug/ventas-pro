<?php
// ======================= CONFIGURACIÓN INICIAL =======================

// Incluye archivo de conexión a la base de datos (PDO)
include_once 'conexionpdo.php';

// Inicia o reanuda la sesión del usuario
session_start();

// Verifica que el usuario tenga rol de repartidor (rol = 4)
// Si no cumple, se redirige al login
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 4) {
    header('location: login.php');
    exit();
}

// Guarda el ID del repartidor logueado
$id_repartidor = $_SESSION['idusuario'];

// Crea instancia de la clase de conexión
$database = new Database();

// Establece conexión con la base de datos
$conexion = $database->conectar();


// ======================= MANEJO DE FORMULARIOS =======================

// Verifica si la petición es POST (envío de formularios)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Obtiene ID de la venta desde el formulario
    $id_venta = $_POST['id_venta'];

    // Obtiene la acción (tomar / actualizar)
    $accion = $_POST['accion'];

    // ------------------- TOMAR PEDIDO -------------------
    if ($accion == 'tomar') {

        // Inserta un nuevo registro en envios
        // Asigna el pedido al repartidor con estado "pendiente"
        $sql = "INSERT INTO envios (id_venta, estado, id_repartidor) 
                VALUES (:id_venta, 'pendiente', :id_repartidor)";

        // Prepara la consulta
        $stmt = $conexion->prepare($sql);

        // Ejecuta con parámetros seguros
        $stmt->execute([
            ':id_venta' => $id_venta,
            ':id_repartidor' => $id_repartidor
        ]);
    }

    // ------------------- ACTUALIZAR ESTADO -------------------
    if ($accion == 'actualizar') {

        // Obtiene el nuevo estado seleccionado
        $nuevo_estado = $_POST['nuevo_estado'];

        // Si el estado es "liberar" (quitar repartidor)
        if ($nuevo_estado == 'liberar') {

            // Inserta nuevo registro sin repartidor (NULL)
            $sql = "INSERT INTO envios (id_venta, estado, id_repartidor)
                    VALUES (:id_venta, 'pendiente', NULL)";

            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                ':id_venta' => $id_venta
            ]);

        } else {

            // Inserta nuevo estado del envío (historial)
            $sql = "INSERT INTO envios (id_venta, estado, id_repartidor)
                    VALUES (:id_venta, :estado, :id_repartidor)";

            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                ':id_venta' => $id_venta,
                ':estado' => $nuevo_estado,
                ':id_repartidor' => $id_repartidor
            ]);
        }
    }

    // Redirección para evitar reenvío del formulario
    header("Location: envios.php");
    exit();
}


// ======================= PAGINACIÓN =======================

// Cantidad de registros por página
$porPagina = 10;

// Página actual por defecto
$pagina = 1;

// Verifica si viene página por GET
if(isset($_GET['pagina'])){
    $pagina = intval($_GET['pagina']);

    // Evita valores inválidos
    if($pagina < 1){ $pagina = 1; }
}

// Calcula desde qué registro iniciar (OFFSET)
$empieza = ($pagina - 1) * $porPagina;


// ======================= CONSULTA PRINCIPAL =======================

// Consulta que obtiene:
// - ID de venta
// - Nombre del cliente
// - Dirección
// - Fecha de compra
$sql = "SELECT ventas.id, usuarios.nomusuario, usuarios.direccion, ventas.fecha
        FROM ventas
        JOIN usuarios ON ventas.idcliente = usuarios.id
        LIMIT :inicio, :cantidad";

// Prepara consulta
$stmt = $conexion->prepare($sql);

// Asocia parámetros de paginación
$stmt->bindParam(':inicio', $empieza, PDO::PARAM_INT);
$stmt->bindParam(':cantidad', $porPagina, PDO::PARAM_INT);

// Ejecuta consulta
$stmt->execute();

// Obtiene resultados como array asociativo
$ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtiene total de registros
$totalRegistros = $conexion->query("SELECT COUNT(*) FROM ventas")->fetchColumn();

// Calcula total de páginas
$totalPaginas = ceil($totalRegistros / $porPagina);
?>

<!DOCTYPE html>
<html>
<head>
<title>Panel Envíos</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="css/envios.css">

</head>

<body>

<!-- HEADER -->
<div class="text-center py-3">
    <img src="img/VENTAS_PRO_remove.png" style="max-height:80px;">
</div>


<!-- NAVBAR PRINCIPAL -->

<nav class="navbar navbar-expand-lg navbar-dark colorNavbar">
<div class="container-fluid px-4 justify-content-between">

<!-- TEXTO IZQUIERDA -->
    <div class="d-flex flex-column">
        <h1 class="titulo-navbar">Bienvenido <span class="subtitulo-navbar">Panel envìos, gestiona estados de los envìos fácilmente</span></h1>
    </div>

<a class="navbar-brand fw-bold d-flex align-items-center" href="cliente.php"></a>

<!-- ICONOS FUNCIONALES -->
<div class="d-flex align-items-center gap-4">

    <div class="text-center eleven">
        <i class="bi bi-person fs-5" style="color: orange;"></i>
        <div style="font-size:12px;">
            <a style="text-decoration: none; color: white" class="colorLinks" href="perfil.php"><span class="Mcategorias">Perfil</span></a>
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

<!-- BIENVENIDA SOLICITUDES -->
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
                panel envìos <strong>VentasPro</strong>
            </p>
        </div>

    </div>

</div><!-- cierre de contenido -->

<!-- TABLA -->
<div class="container mt-4">

<table align="center">

<tr>
<th>ID VENTA</th>
<th>CLIENTE</th>
<th>DIRECCIÓN</th>
<th>FECHA COMPRA</th>
<th>ESTADO</th>
<th>ACCIÓN</th>
</tr>

<?php foreach ($ventas as $venta): ?>

<?php
// Consulta para obtener el último estado del envío de esa venta
$sqlEnvio = "SELECT * FROM envios 
WHERE envios.id_venta = :id_venta 
ORDER BY envios.fecha_actualizacion DESC 
LIMIT 1";

// Ejecuta consulta
$stmtEnvio = $conexion->prepare($sqlEnvio);
$stmtEnvio->execute([':id_venta' => $venta['id']]);

// Obtiene resultado
$envio = $stmtEnvio->fetch(PDO::FETCH_ASSOC);
?>

<tr>

<td style="color: orange"><?= $venta['id']; ?></td>
<td><?= $venta['nomusuario']; ?></td>
<td><?= $venta['direccion']; ?></td>
<td><?= $venta['fecha']; ?></td>

<td>
<?php
// Muestra estado o "sin asignar"
if ($envio) { echo $envio['estado']; }
else { echo "sin asignar"; }
?>
</td>

<td>

<?php 
// Si no tiene repartidor asignado
if (!$envio || $envio['id_repartidor'] == null) { ?>

<form method="POST">
<input type="hidden" name="id_venta" value="<?= $venta['id']; ?>">
<button class="btn btn-custom" name="accion" value="tomar">Tomar</button>
</form>

<?php 
// Si el pedido pertenece al repartidor actual
} elseif ($envio['id_repartidor'] == $id_repartidor) { ?>

<form method="POST" class="d-flex gap-2 justify-content-center">

<input type="hidden" name="id_venta" value="<?= $venta['id']; ?>">

<select name="nuevo_estado">
<option value="pendiente">Pendiente</option>
<option value="preparando">Preparando</option>
<option value="en_camino">En camino</option>
<option value="entregado">Entregado</option>
<option value="liberar">Liberar</option>
</select>

<button class="btn btn-custom" name="accion" value="actualizar">Actualizar</button>

</form>

<?php 
// Si está asignado a otro repartidor
} else { echo "Asignado a otro repartidor"; } ?>

</td>

</tr>

<?php endforeach; ?>

</table>
</div>

<!-- PAGINACION -->
<div class="paginacion text-center">
<?php
// Genera enlaces de paginación
for ($i=1; $i<=$totalPaginas; $i++){

if($i==$pagina){
echo "<b>$i</b>";
}else{
echo "<a href='envios.php?pagina=$i'>$i</a>";
}
}
?>
</div>

<p class="stilosp" style="text-align:center;">Total de páginas: <?= $totalPaginas; ?></p>

<!-- REPORTE -->
<div class= "reporte-container">

<form method="POST" action="reporte_envios.php" target="_blank" class="reporte-form">

<div class="grupo">
    <label for="fecha_inicio">Fecha inicio:</label>
<input type="date" name="fecha_inicio" required>
</div>

<div class="grupo">
    <label for="fecha_fin">Fecha fin:</label>
<input type="date" name="fecha_fin" required>

</div>

<div class="grupo">
    <button class="btn btn-custom">Generar Reporte PDF</button>
</div>

</form>

</div>

</body>
</html>