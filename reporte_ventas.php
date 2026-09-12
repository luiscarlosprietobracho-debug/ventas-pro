<?php
// ==========================================================
// 1. IMPORTACIONES Y CONEXIÓN
// ==========================================================

// Se incluye la librería FPDF para generar archivos PDF
require('fpdf/fpdf.php');

// Se incluye el archivo de conexión a la base de datos (PDO)
include_once("conexionpdo.php");

// Se crea la conexión a la base de datos usando la clase Database
$pdo = (new Database())->conectar();


// ==========================================================
// 2. RECEPCIÓN DE DATOS DEL FORMULARIO
// ==========================================================

// Se reciben las fechas enviadas por el formulario HTML (POST)
// Estas fechas se usan para filtrar las ventas
$fecha_inicio = $_POST['fecha_inicio']; // Fecha inicial
$fecha_fin = $_POST['fecha_fin'];       // Fecha final

// se está validando si vienen vacías (como en solicitudes).

if(empty($fecha_inicio) || empty($fecha_fin)){
    die("Debe seleccionar fechas");
}

// ==========================================================
// 3. CONSULTA A LA BASE DE DATOS
// ==========================================================

// Se prepara la consulta SQL con INNER JOIN para traer el nombre del cliente
$consulta = $pdo->prepare("
SELECT ventas.*, usuarios.nomusuario 
FROM ventas
INNER JOIN usuarios ON ventas.idcliente = usuarios.id
WHERE ventas.fecha BETWEEN ? AND ?
");

/*
EXPLICACIÓN:

- ventas.* 
  → trae todos los campos de la tabla ventas

- INNER JOIN usuarios
  → permite obtener información del cliente (nombre)

- ventas.idcliente = usuarios.id
  → relación entre la venta y el usuario

- BETWEEN ? AND ?
  → filtra las ventas dentro del rango de fechas

IMPORTANTE:
Si una venta no tiene usuario asociado, NO aparecerá
porque INNER JOIN exige coincidencia.
*/

// Se ejecuta la consulta pasando las fechas como parámetros
$consulta->execute([$fecha_inicio, $fecha_fin]);


// ==========================================================
// 4. VARIABLES PARA EL RESUMEN
// ==========================================================

$totalVentas = 0;    // Contador total de ventas
$totalDinero = 0;    // Acumulador del dinero total vendido


// ==========================================================
// 5. CREACIÓN DEL PDF
// ==========================================================

// Se crea el objeto PDF
$pdf = new FPDF();

// Se agrega una nueva página
$pdf->AddPage();


// ==========================================================
// 6. TÍTULO DEL REPORTE
// ==========================================================

// Fuente: Arial, Negrita, tamaño 16
$pdf->SetFont('Arial','B',16);

// Título centrado
$pdf->Cell(0,10,'Reporte de Ventas',0,1,'C');

// Fuente normal para el rango de fechas
$pdf->SetFont('Arial','',10);

// Se muestra el rango de fechas seleccionado
$pdf->Cell(0,10,"Desde: $fecha_inicio  Hasta: $fecha_fin",0,1,'C');

$pdf->Ln(5); // Espacio


// ==========================================================
// 7. ENCABEZADOS DE LA TABLA
// ==========================================================

$pdf->SetFont('Arial','B',10);

/*
IMPORTANTE SOBRE LOS ANCHOS:

15 + 40 + 30 + 40 = 125

Esto cabe perfectamente en la hoja (aprox 190 de ancho útil).
Si se agregan más columnas, se debe ajustar estos valores.
*/

$pdf->Cell(15,10,'ID',1);
$pdf->Cell(40,10,'Cliente',1);
$pdf->Cell(30,10,'Total',1);
$pdf->Cell(40,10,'Fecha',1);

$pdf->Ln(); // Salto de línea


// ==========================================================
// 8. DATOS DE LA TABLA
// ==========================================================

$pdf->SetFont('Arial','',9);

// Se recorren todos los registros de la consulta
while($fila = $consulta->fetch(PDO::FETCH_ASSOC)){

    /*
    IMPORTANTE:
    Cada iteración representa UNA venta
    */

    // Se imprimen los datos en celdas
    $pdf->Cell(15,10,$fila['id'],1);
    $pdf->Cell(40,10,$fila['nomusuario'],1);

    $pdf->Cell(30,10,$fila['total'],1);

    $pdf->Cell(40,10,$fila['fecha'],1);

    $pdf->Ln(); // Salto de línea para la siguiente fila


    // ======================================================
    // ACUMULADORES PARA EL RESUMEN
    // ======================================================

    $totalVentas++;                 // Cuenta cuántas ventas hay
    $totalDinero += $fila['total']; // Suma el total de cada venta
}

// ==========================================================
// 9. ESPACIO ANTES DEL RESUMEN
// ==========================================================

$pdf->Ln(10);


// ==========================================================
// 10. RESUMEN FINAL
// ==========================================================

// Título del resumen
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,'Resumen',0,1);

// Texto normal
$pdf->SetFont('Arial','',10);

// Total de ventas
$pdf->Cell(0,10,"Total de ventas: $totalVentas",0,1);

// Total de dinero acumulado
$pdf->Cell(0,10,"Total vendido: $totalDinero COP",0,1);

// ==========================================================
// 11. SALIDA DEL PDF
// ==========================================================

// Output() genera el PDF y lo envía al navegador
// Opciones:
// - $pdf->Output('I'); → ver en navegador
// - $pdf->Output('D'); → descargar
// - $pdf->Output('F','archivo.pdf'); → guardar en servidor

$pdf->Output();

?>