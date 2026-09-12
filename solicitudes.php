<?php
// ======================= CONFIGURACIÓN INICIAL =======================

// Incluye el archivo de conexión a la base de datos usando PDO
include_once("conexionpdo.php");

// Inicia la sesión para acceder a variables de usuario logueado
session_start();

// ======================= VALIDACIÓN DE ACCESO =======================

// Verifica que exista sesión y que el usuario tenga rol 3 (agente de solicitudes)
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 3) {
    header('location: login.php');  // Redirige si no cumple
    exit();                         // Detiene ejecución del script
}

// ======================= CONEXIÓN A BASE DE DATOS =======================

// Crea instancia de la clase Database y establece conexión
$pdo=(new Database())->conectar();


// ======================= PROCESO PARA RESPONDER SOLICITUD =======================

// Verifica si el formulario de respuesta fue enviado
if(isset($_POST['responder'])){

    // Prepara consulta para actualizar:
    // - respuesta del agente
    // - estado a "Respondido"
    // - fecha de respuesta automática con NOW()
    $consulta=$pdo->prepare("UPDATE solicitudes 
    SET respuesta=?, estado='Respondido', fecha_respuesta=NOW()
    WHERE id=?");

    // Ejecuta la consulta con:
    // 1. respuesta escrita
    // 2. id de la solicitud
    $consulta->execute([
        $_POST['respuesta'],     // texto de respuesta
        $_POST['idsolicitud']    // ID de la solicitud
    ]);

    // Muestra mensaje y recarga la página
    echo "<script>alert('Respuesta enviada'); window.location='solicitudes.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Panel Solicitudes</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Iconos -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<!-- Estilos personalizados -->
<link rel="stylesheet" href="solicitudes.css">
</head>

<body>

<!-- ======================= HEADER ======================= -->
<header class="estilosheader">

<div class="container d-flex justify-content-center">
<img class="ContIMG text-center" src="img/VENTAS_PRO_remove.png"
class="img-fluid">
</div>

</header>

<!-- ======================= NAVBAR ======================= -->

<nav class="navbar navbar-expand-lg navbar-dark colorNavbar">
<div class="container-fluid px-4 justify-content-between">

<!-- TEXTO -->
<div class="d-flex flex-column">
<h1 class="titulo-navbar">
Bienvenido 
<span class="subtitulo-navbar">
Panel solicitudes, gestiona tus solicitudes fácilmente
</span>
</h1>
</div>

<a class="navbar-brand fw-bold d-flex align-items-center" href="cliente.php"></a>

<!-- ICONOS -->
<div class="d-flex align-items-center gap-4">

<!-- PERFIL -->
<div class="text-center eleven">
<i class="bi bi-person fs-5" style="color: orange;"></i>
<div style="font-size:12px;">
<a class="colorLinks" href="perfil.php">
<span class="Mcategorias">Perfil</span>
</a>
</div>
</div>

<!-- CERRAR SESIÓN -->
<div class="text-center eleven">
<form action="login.php" method="post" style="display:inline;">
<button type="submit" name="cerrar_sesion" class="p-0 text-white border-0 bg-transparent">
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

</nav><br><br>

<!-- ======================= BIENVENIDA ======================= -->
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
panel solicitudes <strong>VentasPro</strong>
</p>
</div>

</div>

</div>

<!-- ======================= TABLA DE SOLICITUDES ======================= -->
<center>
<table border="3" cellpadding="6">

<tr>
<th>ID</th>
<th>CLIENTE</th>
<th>ASUNTO</th>
<th>MENSAJE</th>
<th>ESTADO</th>
<th>FECHA</th>
<th>RESPONDER</th>
</tr>

<?php

// ======================= PAGINACIÓN =======================

// Cantidad de registros por página
$porPagina = 10;

// Página actual por defecto
$pagina = 1;

// Verifica si viene página por GET
if(isset($_GET['pagina'])){
    $pagina = intval($_GET['pagina']);

    // Evita valores inválidos
    if($pagina < 1){
        $pagina = 1;
    }
}

// Calcula el OFFSET (desde qué registro iniciar)
$empieza = ($pagina - 1) * $porPagina;


// ======================= CONSULTA CON LÍMITE =======================

// Consulta que une:
// - solicitudes
// - usuarios (para mostrar nombre del cliente)
$consulta = $pdo->prepare("SELECT solicitudes.*, usuarios.nomusuario 
FROM solicitudes
INNER JOIN usuarios ON solicitudes.idusuario = usuarios.id
ORDER BY fecha DESC
LIMIT :inicio, :cantidad");

// Bind de parámetros de paginación
$consulta->bindParam(':inicio', $empieza, PDO::PARAM_INT);
$consulta->bindParam(':cantidad', $porPagina, PDO::PARAM_INT);

// Ejecuta la consulta
$consulta->execute();

// Total de registros
$totalRegistros = $pdo->query("SELECT COUNT(*) FROM solicitudes")->fetchColumn();

// Total de páginas
$totalPaginas = ceil($totalRegistros / $porPagina);


// ======================= MOSTRAR RESULTADOS =======================

// Recorre cada solicitud
while($fila=$consulta->fetch(PDO::FETCH_ASSOC)){
?>

<tr>

<td><?=$fila['id']?></td>              <!-- ID de la solicitud -->
<td><?=$fila['nomusuario']?></td>     <!-- Nombre del cliente -->
<td><?=$fila['asunto']?></td>         <!-- Asunto -->
<td><?=$fila['mensaje']?></td>        <!-- Mensaje -->
<td><?=$fila['estado']?></td>         <!-- Estado -->
<td><?=$fila['fecha']?></td>          <!-- Fecha -->

<td>

<!-- BOTÓN RESPONDER -->
<a href="solicitudes.php?pagina=<?=$pagina?>&responder=<?=$fila['id']?>" 
class="btn">
Responder
</a>

</td>

</tr>

<?php
}
?>

</table>
</center>


<!-- ======================= PAGINACIÓN UI ======================= -->
<div class="paginacion" style="text-align:center; margin-top:20px;">
<?php
for ($i=1; $i<=$totalPaginas; $i++){

    // Botones primera y anterior
    if($i==1 && $pagina>1){
        echo "<a href='solicitudes.php?pagina=1'>Primera</a>&nbsp;";
        echo "<a href='solicitudes.php?pagina=".($pagina-1)."'>Anterior</a>&nbsp;";
    }

    // Página actual
    if($i==$pagina){
        echo "<b>$i</b>&nbsp;";
    } else {
        echo "<a href='solicitudes.php?pagina=$i'>$i</a>&nbsp;";
    }

    // Botones siguiente y última
    if($i==$totalPaginas && $pagina<$totalPaginas){
        echo "<a href='solicitudes.php?pagina=".($pagina+1)."'>Siguiente</a>&nbsp;";
        echo "<a href='solicitudes.php?pagina=".$totalPaginas."'>Última</a>&nbsp;";
    }
}
?>
</div><br>

<!-- Total de páginas -->
<p class="stilosp" style="text-align:center;">
Total de páginas: <?php echo $totalPaginas; ?>
</p>

</center>

<br><br>

<?php

// ======================= FORMULARIO DE RESPUESTA =======================

// Si se seleccionó responder una solicitud
if(isset($_GET['responder'])){

// Consulta la solicitud específica
$consulta=$pdo->prepare("SELECT solicitudes.*, usuarios.nomusuario
FROM solicitudes
INNER JOIN usuarios ON solicitudes.idusuario = usuarios.id
WHERE solicitudes.id=?");

$consulta->execute([$_GET['responder']]);

// Obtiene datos
$solicitud=$consulta->fetch(PDO::FETCH_ASSOC);

// Verifica que exista
if($solicitud){
?>

<!-- PANEL DE RESPUESTA -->
<div class="contenedor-respuesta">

<div class="card-respuesta">

<h3 class="titulo-card">Responder Solicitud</h3>

<!-- INFO DE LA SOLICITUD -->
<div class="info">
<p><span>Cliente:</span> <?=$solicitud['nomusuario']?></p>
<p><span>Asunto:</span> <?=$solicitud['asunto']?></p>
<p><span>Mensaje:</span> <?=$solicitud['mensaje']?></p>
</div>

<!-- FORMULARIO -->
<form method="POST" class="form-respuesta">

<input type="hidden" name="idsolicitud" value="<?=$solicitud['id']?>">

<textarea name="respuesta" placeholder="Escribe la respuesta..." required></textarea>

<button type="submit" name="responder">Enviar Respuesta</button>

</form>

</div>

</div>

<?php
}
}
?>

<br><br>

<!-- ======================= REPORTE ======================= -->
<div class="reporte-container">

<form method="POST" action="reporte_solicitudes.php" target="_blank" class="reporte-form">

<div class="grupo">
<label>Fecha inicio</label>
<input type="date" name="fecha_inicio" required>
</div>

<div class="grupo">
<label>Fecha fin</label>
<input type="date" name="fecha_fin" required>
</div>

<div class="grupo">
<button type="submit">Generar Reporte</button>
</div>

</form>

</div>

</body>
</html>