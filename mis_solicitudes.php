<?php
include_once("conexionpdo.php");
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 2) {
    header("location: login.php");
    exit();
}

$pdo = (new Database())->conectar();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Mis Solicitudes</title>
<link rel="stylesheet" href="css/mis_solicitudes.css">

</head>

<body>

<h1 class="titulo-pagina">Mis Solicitudes</h1>

<div class="contenedor-tabla">

<table class="tabla-solicitudes">

    <tr>
        <th>ID</th>
        <th>ASUNTO</th>
        <th>MENSAJE</th>
        <th>RESPUESTA</th>
        <th>ESTADO</th>
        <th>FECHA</th>
    </tr>

    <?php

    $consulta = $pdo->prepare("SELECT * FROM solicitudes 
    WHERE idusuario=?
    ORDER BY fecha DESC");

    $consulta->execute([$_SESSION['idusuario']]);

    while($fila=$consulta->fetch(PDO::FETCH_ASSOC)){
    ?>

<tr>

<td><?=$fila['id']?></td>

<td><?=$fila['asunto']?></td>

<td><?=$fila['mensaje']?></td>

<td>
<?php
if($fila['estado']=="Pendiente"){
    echo "<span class='pendiente'>Aún sin respuesta</span>";
}else{
    echo $fila['respuesta'];
}
?>
</td>

<td>
<span class="<?=$fila['estado']=='Pendiente' ? 'estado-pendiente' : 'estado-ok'?>">
    <?=$fila['estado']?>
</span>
</td>

<td><?=$fila['fecha']?></td>

</tr>

<?php } ?>

</table>

</div>

<div class="contenedor-boton">
    <a href="cliente.php" class="btn-volver">Volver al panel</a>
</div>

</body>
</html>