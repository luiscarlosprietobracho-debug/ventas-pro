<?php
require('fpdf/fpdf.php');          // 1. Importar librería FPDF (para generar PDFs)
include_once("conexionpdo.php");   // 2. Conexión a la base de datos

$pdo = (new Database())->conectar(); // Crear conexión PDO

// ==========================================================
// 3. RECIBIR FECHAS DESDE FORMULARIO
// ==========================================================
$fecha_inicio = $_POST['fecha_inicio'];
$fecha_fin = $_POST['fecha_fin'];


// ==========================================================
// 4. CONSULTA PRINCIPAL
// ==========================================================
// IMPORTANTE:
// Esta consulta trae SOLO el último estado de cada venta
// usando MAX(fecha_actualizacion)
// Evita duplicados en el reporte

$consulta = $pdo->prepare("
SELECT envios.*, ventas.id AS id_venta, usuarios.nomusuario
FROM envios
INNER JOIN ventas ON envios.id_venta = ventas.id
INNER JOIN usuarios ON ventas.idcliente = usuarios.id
WHERE envios.fecha_actualizacion = (
    SELECT MAX(envios2.fecha_actualizacion)
    FROM envios AS envios2
    WHERE envios2.id_venta = envios.id_venta
)
AND DATE(envios.fecha_actualizacion) BETWEEN ? AND ?
ORDER BY envios.fecha_actualizacion DESC
");

$consulta->execute([$fecha_inicio, $fecha_fin]);


// ==========================================================
// 5. CONTADORES PARA RESUMEN
// ==========================================================
$total = 0;
$pendientes = 0;
$en_camino = 0;
$entregados = 0;


// ==========================================================
// 6. CREAR PDF
// ==========================================================
$pdf = new FPDF();   // Crear objeto PDF
$pdf->AddPage();    // Agregar página

// ----------------------------------------------------------
// TITULO
// ----------------------------------------------------------
$pdf->SetFont('Arial','B',16);  // Fuente: Arial, Negrita, tamaño 16

// Cell(ancho, alto, texto, borde, salto_linea, alineacion)
// ancho 0 = ocupa todo el ancho disponible
$pdf->Cell(0,10,'Reporte de Envios',0,1,'C');

$pdf->Ln(5); // Salto de línea


// ----------------------------------------------------------
// RANGO DE FECHAS
// ----------------------------------------------------------
$pdf->SetFont('Arial','',10);
$pdf->Cell(0,8,"Desde: $fecha_inicio  Hasta: $fecha_fin",0,1,'C');

$pdf->Ln(5);


// ==========================================================
// 7. ENCABEZADOS DE TABLA
// ==========================================================

// IMPORTANTE:
// El ancho de cada columna DEBE coincidir con los datos
// Si no, la tabla se desordena

// Consejo:
// El total de anchos debe ser aprox <= 190 (ancho de hoja A4)

$pdf->SetFont('Arial','B',9);

// Cell(ancho, alto, texto, borde)
// Aquí defines el tamaño de cada columna

$pdf->Cell(25,10,'ID VENTA',1);     
$pdf->Cell(35,10,'CLIENTE',1);
$pdf->Cell(30,10,'ESTADO',1);
$pdf->Cell(40,10,'REPARTIDOR',1);
$pdf->Cell(60,10,'FECHA',1);

$pdf->Ln(); // Salto de línea para empezar los datos


// ==========================================================
// 8. DATOS DE LA TABLA
// ==========================================================

$pdf->SetFont('Arial','',8);

while($fila = $consulta->fetch(PDO::FETCH_ASSOC)){

    $total++; // Contador total de registros

    // ------------------------------------------------------
    // CONTAR ESTADOS
    // ------------------------------------------------------
    if($fila['estado'] == 'pendiente'){
        $pendientes++;
    }
    if($fila['estado'] == 'en_camino'){
        $en_camino++;
    }
    if($fila['estado'] == 'entregado'){
        $entregados++;
    }

    // ------------------------------------------------------
    // MOSTRAR DATOS EN FILAS
    // ------------------------------------------------------

    // IMPORTANTE:
    // Los anchos deben ser EXACTAMENTE iguales a los encabezados

    // Puedes centrar con 'C' al final:
    // Cell(25,10,$fila['id_venta'],1,0,'C');

    $pdf->Cell(25,10,$fila['id_venta'],1);
    $pdf->Cell(35,10,$fila['nomusuario'],1);
    $pdf->Cell(30,10,$fila['estado'],1);
    // Validar si tiene repartidor asignado
    $repartidor = !empty($fila['id_repartidor']) 
    ? $fila['id_repartidor'] 
    : 'Sin agente asignado';

// Mostrar en PDF
$pdf->Cell(40,10,$repartidor,1);
    $pdf->Cell(60,10,$fila['fecha_actualizacion'],1);

    $pdf->Ln(); // Salto de línea (MUY IMPORTANTE para que no se monten las filas)
}


// ==========================================================
// 9. RESUMEN
// ==========================================================

$pdf->Ln(10); // Espacio antes del resumen

$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,'Resumen',0,1);

$pdf->SetFont('Arial','',10);

// ancho 0 = ocupa toda la línea
$pdf->Cell(0,8,"Total de envios: $total",0,1);
$pdf->Cell(0,8,"Pendientes: $pendientes",0,1);
$pdf->Cell(0,8,"En camino: $en_camino",0,1);
$pdf->Cell(0,8,"Entregados: $entregados",0,1);


// ==========================================================
// 10. SALIDA DEL PDF
// ==========================================================

// Output() genera y muestra el PDF en el navegador
$pdf->Output();