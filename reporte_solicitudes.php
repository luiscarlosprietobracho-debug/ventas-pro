<?php
// ==========================================================
// 1. IMPORTACIONES Y CONEXIÓN
// ==========================================================

// Se importa la librería FPDF que permite generar archivos PDF desde PHP
require('fpdf/fpdf.php');

// Se incluye el archivo de conexión a base de datos (PDO)
include_once("conexionpdo.php");

// Se crea la conexión a la base de datos usando la clase Database
$pdo = (new Database())->conectar();


// ==========================================================
// 2. VALIDACIÓN DE DATOS DEL FORMULARIO
// ==========================================================

// Se valida que el formulario haya enviado ambas fechas
// empty() verifica si el valor está vacío o no existe
if(empty($_POST['fecha_inicio']) || empty($_POST['fecha_fin'])){
    
    // die() detiene completamente la ejecución del script
    // Se usa aquí porque sin fechas no tiene sentido generar el reporte
    die("Error: Debe seleccionar un rango de fechas");
}

// Se reciben las fechas enviadas desde el formulario HTML (POST)
$fecha_inicio = $_POST['fecha_inicio'];
$fecha_fin = $_POST['fecha_fin'];


// ==========================================================
// 3. CONSULTA A LA BASE DE DATOS
// ==========================================================

// Se prepara la consulta SQL usando prepared statements (más seguro contra inyección SQL)
$consulta = $pdo->prepare("
SELECT * FROM solicitudes 
WHERE (fecha BETWEEN ? AND ? OR fecha IS NULL)
ORDER BY fecha DESC
");

/*
fecha BETWEEN ? AND ? traen registros dentro del rango seleccionado

OR fecha IS NULL, también incluye solicitudes que NO tienen fecha (ej: pendientes sin procesar)

ORDER BY fecha DESC, ordena de más reciente a más antiguo

asi se permite ver solicitudes sin fecha (casos no gestionados) y mantiene un orden lógico para análisis
*/

// Se ejecuta la consulta enviando las fechas como parámetros
$consulta->execute([$fecha_inicio, $fecha_fin]);


// ==========================================================
// 4. CONTADORES PARA EL RESUMEN
// ==========================================================

$total = 0;         // Contador total de solicitudes
$respondidas = 0;   // Contador de solicitudes respondidas
$pendientes = 0;    // Contador de solicitudes pendientes


// ==========================================================
// 5. CREACIÓN DEL PDF
// ==========================================================

// Se crea el objeto principal de FPDF
$pdf = new FPDF();

// Se añade una nueva página al documento
$pdf->AddPage();


// ==========================================================
// 6. TÍTULO DEL DOCUMENTO
// ==========================================================

// Se define la fuente: Arial, Negrita, tamaño 16
$pdf->SetFont('Arial','B',16);

// Cell(ancho, alto, texto, borde, salto_linea, alineación)
$pdf->Cell(0,10,'Reporte de Solicitudes',0,1,'C'); 
// 0 = ocupa todo el ancho
// 1 = salto de línea automático después

$pdf->Ln(5); // Espacio vertical


// ==========================================================
// 7. RANGO DE FECHAS MOSTRADO EN EL PDF
// ==========================================================

$pdf->SetFont('Arial','',11);

// Se concatenan las fechas dentro del texto
$pdf->Cell(0,8,'Desde: '.$fecha_inicio.'  Hasta: '.$fecha_fin,0,1);

$pdf->Ln(5);


// ==========================================================
// 8. ENCABEZADOS DE LA TABLA
// ==========================================================

// Fuente para encabezados (negrita)
$pdf->SetFont('Arial','B',10);

/*
IMPORTANTE:
Cada Cell tiene un ancho fijo.
La suma de todos los anchos debe caber dentro de la página (~190mm en A4)

Si un texto es muy largo → se va a "salir"
*/

$pdf->Cell(10,10,'ID',1);
$pdf->Cell(40,10,'Asunto',1);
$pdf->Cell(60,10,'Mensaje',1);
$pdf->Cell(30,10,'Estado',1);
$pdf->Cell(40,10,'Fecha',1);

$pdf->Ln(); // salto de línea


// ==========================================================
// 9. DATOS DE LA TABLA
// ==========================================================

// Fuente normal para contenido
$pdf->SetFont('Arial','',9);

// Se recorren todos los resultados de la consulta
while($fila = $consulta->fetch(PDO::FETCH_ASSOC)){

    // Se incrementa el contador total por cada fila
    $total++;

    // ======================================================
    // CONTADORES SEGÚN ESTADO
    // ======================================================

    if($fila['estado'] == 'Respondido'){
        $respondidas++;
    } else {
        // lo que no sea "Respondido" se considera pendiente
        $pendientes++;
    }

    // ======================================================
    // IMPRESIÓN DE DATOS EN CELDAS
    // ======================================================

    /*
    NOTA IMPORTANTE:
    FPDF NO hace saltos de línea automáticos dentro de una celda.
    Si el texto es muy largo, se va a desbordar.

    Solución profesional:
    → usar MultiCell (pero eso ya es nivel avanzado)
    */

    $pdf->Cell(10,10,$fila['id'],1);
    $pdf->Cell(40,10,$fila['asunto'],1);
    $pdf->Cell(60,10,$fila['mensaje'],1);
    $pdf->Cell(30,10,$fila['estado'],1);

    // ======================================================
    // VALIDACIÓN DE FECHA NULA
    // ======================================================

    if($fila['fecha'] == NULL){

        // Si la fecha es NULL en la BD, mostramos un texto amigable
        $pdf->Cell(40,10,'Sin fecha',1);

    } else {

        // Si existe fecha, se muestra normalmente
        $pdf->Cell(40,10,$fila['fecha'],1);
    }

    // Salto de línea para la siguiente fila
    $pdf->Ln();
}


// ==========================================================
// 10. RESUMEN FINAL DEL REPORTE
// ==========================================================

$pdf->Ln(10); // Espacio antes del resumen

$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,'Resumen del Reporte',0,1);

$pdf->SetFont('Arial','',11);

// Se muestran los totales calculados anteriormente
$pdf->Cell(0,8,'Total de solicitudes: '.$total,0,1);
$pdf->Cell(0,8,'Solicitudes respondidas: '.$respondidas,0,1);
$pdf->Cell(0,8,'Solicitudes pendientes: '.$pendientes,0,1);


// ==========================================================
// 11. LÍNEA DECORATIVA
// ==========================================================

$pdf->Ln(5);

// Línea visual simple para separar contenido
$pdf->Cell(0,0,'---------------------------------------------',0,1);


// ==========================================================
// 12. SALIDA DEL PDF
// ==========================================================

// Output() genera el PDF y lo envía al navegador
// Por defecto:
// - Se abre en el navegador
// - También permite descargarlo

$pdf->Output();

?>