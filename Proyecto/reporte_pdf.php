<?php
session_start();
if(!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}
// La librería FPDF para generar el PDF del reporte de empleados
require('fpdf.php'); 
class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'AgroVerduras S.A. - Reporte General de Empleados', 0, 1, 'C');
        $this->Ln(5);
        
        $this->SetFillColor(200, 230, 201); 
        $this->SetDrawColor(150, 150, 150); 
        $this->SetFont('Arial', 'B', 8); 
        $this->Cell(15, 10, 'Codigo', 1, 0, 'C', true);
        $this->Cell(35, 10, 'Nombres', 1, 0, 'C', true);
        $this->Cell(35, 10, 'Apellidos', 1, 0, 'C', true);
        $this->Cell(20, 10, 'Fecha', 1, 0, 'C', true);
        $this->Cell(15, 10, 'Salario', 1, 0, 'C', true);
        $this->Cell(25, 10, 'Area', 1, 0, 'C', true);
        $this->Cell(30, 10, 'Cultivo', 1, 0, 'C', true);
        $this->Cell(20, 10, 'Telefono', 1, 0, 'C', true);
        $this->Cell(12, 10, 'Edad', 1, 0, 'C', true);
        $this->Cell(20, 10, 'Turno', 1, 1, 'C', true); 
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}


$conexion = new PDO("mysql:host=localhost;dbname=sistema_verduras", "root", "");
$stmt = $conexion->query("SELECT * FROM empleados ORDER BY id DESC");

// Creación del objeto PDF
$pdf = new PDF('L', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Arial', '', 8);
$pdf->SetDrawColor(150, 150, 150);

// Imprimir los datos iterando la consulta
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $pdf->Cell(15, 10, $row['codigo_empleado'], 1, 0, 'C');
    $pdf->Cell(35, 10, utf8_decode($row['nombres']), 1, 0, 'L'); 
    $pdf->Cell(35, 10, utf8_decode($row['apellidos']), 1, 0, 'L');
    $pdf->Cell(20, 10, $row['fecha_contratacion'], 1, 0, 'C');
    $pdf->Cell(15, 10, '$' . number_format($row['salario_diario'], 2), 1, 0, 'C');
    $pdf->Cell(25, 10, utf8_decode($row['area_trabajo']), 1, 0, 'C');
    $pdf->Cell(30, 10, utf8_decode($row['cultivo_asignado']), 1, 0, 'C');
    $pdf->Cell(20, 10, $row['telefono'], 1, 0, 'C');
    $pdf->Cell(12, 10, $row['edad'], 1, 0, 'C');
    $pdf->Cell(20, 10, utf8_decode($row['turno']), 1, 1, 'C'); 
}

// Salida del PDF
$pdf->Output('I', 'Reporte_General_Empleados.pdf');
?>