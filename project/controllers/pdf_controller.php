<?php
    use Fpdf\Fpdf;

    require_once('vendor/autoload.php');
    function createPDF($name) {
        // size of a card
        $docWidth = 85 / 0.35;
        $docHeight = 54 / 0.35;

        $margin = 15;

        $photoSize = 20 / 0.35; // size of a photo on a driver's license

        $pdf = new Fpdf('L', 'pt', array($docHeight, $docWidth));

        $pdf->setTitle('Legitimatie ' . $name);
        $pdf->setMargins($margin, $margin, $margin);
        $pdf->SetAutoPageBreak(false);

        // title
        $pdf->AddPage();
        $pdf->SetFont('Courier', '', 12);
        $pdf->MultiCell($docWidth - 2 * $margin, 14, 'Biblioteca Nationala a lui Vlad', 0, 'C', 0);

        // subtitle
        $pdf->SetFont('Courier', 'B', 11);
        $pdf->setY(3 * $margin);
        $pdf->Cell($docWidth - 2 * $margin, 10, 'LEGITIMATIE', 0, 0, 'C');

        // photo space
        $pdf->setY(5 * $margin);
        $pdf->setX($docWidth / 4 - $photoSize / 2);
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->setFillColor(100, 100, 100);
        $pdf->Cell($photoSize, $photoSize, 'POZA', 1, 0, 'C', true);

        // info fields
        $pdf->SetFont('Arial', '', 10);
        $pdf->setY(5 * $margin);
        $pdf->setX($docWidth / 2 + $margin);
        $pdf->Cell($docWidth / 2 - 2 * $margin, 10, 'Nume: ' . $name, 0, 0, 'L');

        $pdf->SetFont('Arial', '', 10);
        $pdf->setY(6 * $margin);
        $pdf->setX($docWidth / 2 + $margin);
        $pdf->Cell($docWidth / 2 - 2 * $margin, 10, 'An: ' . date("Y"), 0, 0, 'L');

        $pdf->Output('I', 'legitimatie-' . str_replace(' ', '-', strtolower($name)));
    }

    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false){
        header("location: /project/login");
        exit;
    }

    createPDF($_SESSION['name']);
?>