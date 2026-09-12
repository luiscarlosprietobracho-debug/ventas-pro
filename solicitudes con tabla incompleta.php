<?php
include_once("conexionpdo.php");
session_start();

echo "<h1><b><center>Bienvenida Agente de Solicitudes ". $_SESSION['usuario']."</b></h1></center>";


if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 3) {
	header('location: login.php');
	exit();
}
?>

<html>
<head>
  <style>
    body {
	  color: white;
      background-image: url("img/purpura.jpg");
      background-size: cover;       /* Ajusta la imagen a toda la pantalla */
      background-repeat: no-repeat; /* Evita que se repita */
      background-position: center;  /* La centra */
      background-attachment: fixed; /* Opcional: efecto fijo al hacer scroll */
    }
  </style>
</head>
<body>
<div align="center">
	<?php
	$usuario    = htmlspecialchars($_SESSION['nomusuario']); 
	$fotosesion = htmlspecialchars($_SESSION['foto']); 
	echo "<div align='center'><img src='img/" . htmlspecialchars($fotosesion) . "' width='200' height='160'></div>";
	?>
	<h1>Consulta de Usuarios</h1>
	<table border="3" align="center">
		<tr>	
			<th>ID</th>
			<th>NOMBRE</th>
			<th>EMAIL</th>
			<th>FOTO</th>
			<th>EDITAR</th>
		</tr>
		<?php
		$database = new Database();
		$conexion = $database->conectar();
		if (isset($_POST['actualizame'])) 
			{
				$editar_id = $_POST['id'];
				$actualizausuario = $_POST['usuario'];
				$actualizaemail = $_POST['email'];
				$update = "UPDATE usuarios SET nomusuario = :usuario, email = :email WHERE id = :id";
				$stmt = $conexion->prepare($update);
				$stmt->bindParam(':usuario', $actualizausuario, PDO::PARAM_STR);
				$stmt->bindParam(':email', $actualizaemail, PDO::PARAM_STR);
				$stmt->bindParam(':id', $editar_id, PDO::PARAM_INT);

				if ($stmt->execute()) 
					{
						header("Location: envios.php");
						exit();
					}
			}
		$consulta = "SELECT * FROM usuarios";
		try {
			$stmt = $conexion->prepare($consulta);
			$stmt->execute();
			$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
			foreach ($resultados as $fila) 
				{
				$id = $fila['id'];
				$usuario = $fila['nomusuario'];
				$email = $fila['email'];
				$foto=$fila['Foto'];
				echo "
				<tr align='center'>
					<td>$id</td>
					<td>" . htmlspecialchars($usuario) . "</td>
					<td>" . htmlspecialchars($email). "</td>
					<td><img src='img/$foto' width='100' height='80'></td>
					<td><a href='?editar=$id' style='
					display:inline-block;
					padding: 10px 14px;
					background-color: red;
					text-decoration: none; 
					color: white;
					border-radius: 6px;
					box-shadow: 0 2px 5px rgba(0,0,0,0.75);
					'>Editar</a></td>
				</tr>";
			}
		} 
		catch (PDOException $error) 
		{
			echo "Error al ejecutar la consulta: " . $error->getMessage();
		}
		?>
	</table>
</div>

<?php
// Sección para cargar datos del usuario a editar
if (isset($_GET['editar']) && is_numeric($_GET['editar']))
	{
		$editar_id = intval($_GET['editar']);
		echo "El id seleccionado es: ".$editar_id;
		$editar_id = $_GET['editar'];
		$consulta = "SELECT * FROM usuarios WHERE id = :id";
		$stmt = $conexion->prepare($consulta);
		$stmt->bindParam(':id', $editar_id, PDO::PARAM_INT);
		$stmt->execute();
		$filas = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($filas) 
			{
				$id = $filas['id'];
				$usuario = $filas['nomusuario'];
				$email = $filas['email'];
			}
	}
?>
<?php 
if (isset($filas)) 
	{ 
		?>
		<div align="center">
			<form method="POST" action="#">
						<input type="hidden" name="id" value="<?php echo $id; ?>">
				NOMBRE: <input type="text" name="usuario" value="<?php echo $usuario; ?>"><br>
				EMAIL:  <input type="email" name="email" value="<?php echo $email; ?>"><br>
				<input type="submit" name="actualizame" value="Actualizar Datos">
			</form>
		</div>
	<?php 
	} 
?>
<div align="center">
	<form action="login.php" method="POST">
		<input type="submit" name="cerrar_sesion" value="CERRAR SESIÓN">
	</form>
</div>
</body>
</html>
