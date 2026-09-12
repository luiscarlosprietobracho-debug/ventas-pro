<?php
session_start();
if (!isset($_SESSION['Usuario'])){
    header("location: login.php");
    exit();
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina Principal</title>
</head>
<body>
    <center>
<br><br><br><br><br>

    <H1>Pagina Principal</H1><br><br>

    <p>Bienvenido usuario</p><br><br>

<div align="center">
    <form action="login.php" method="post">
        <input type="submit" name="cerrar_sesion" value="CERRAR SESION"
        style="background-color: red; 
        padding: 8px 20px;
        color:white;">
    </form>
    
</div>
     </center>   
</body>
</html>