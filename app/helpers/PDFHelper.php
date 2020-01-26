<?php
/**
 * Return PDF string file for email
 *
 * @param $location string PDF file location
 * @param $name string name of the donator
 * @param $qrcodeMessage string qr code message
 */
function createPDFThankYou(string $name, string $qrcodeMessage, string $location){

    //// PDF location on server
  //      $location = 'pdf/pdf-' . $_SESSION['user_id'] . '-' . bin2hex(random_bytes(8)) . '.pdf';
  //      $qrMessage = 'What a wonderful person you are!';
  //      createPDFThankYou($_SESSION['user_name'], $qrMessage, $location);

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',20);
    $pdf->Cell(0,10,'Community Share', 0, 1, 'C');
    $pdf->SetFont('Arial','U',20);
    $thankString = 'Thank you for your contribution ' . $name;
    $pdf->Cell(0,20, $thankString, 0, 1, 'C');

    $pdf->SetFont('Arial','',11);

    $pdf->Cell(0,60,'', '0', '1');

    // Create QR code and insert it into the PDF
    $qrcodeMessage .= ' You are the best ' . $name . '!';
    $qrcode = new QRcode($qrcodeMessage, 'H'); // error level : L, M, Q, H
    $qrcode->displayFPDF($pdf, 80, 40, 50);

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(0,10,'If you have any tips or anything to say, send me an email at: ', 0, 1, 'C');
    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(0,10,'mattismeeuwesse@gmail.com', 1, 1, 'C');

    $pdf->Output('F', $location);
}