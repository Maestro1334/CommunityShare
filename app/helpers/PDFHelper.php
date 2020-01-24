<?php
/**
 * Return PDF string file for email
 *
 * @param $location string invoice PDF file location
 * @param $tickets array all the ticket items
 * @param $total int total price of order
 * @param $order object order details
 */
function createPDFInvoice(string $location, array $tickets, int $total, $order){
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',20);
    $pdf->Cell(0,10,'Haarlem Festival', 0, 1, 'C');
    $pdf->SetFont('Arial','',11);

    $pdf->Cell(0,60,'', '0', '1');

    // Create QR code and insert it into the PDF
    $qrcode = new QRcode($order->qrcode, 'H'); // error level : L, M, Q, H
    $qrcode->displayFPDF($pdf, 80, 25, 50);

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(0,10,'Tickets', 0, 1, 'C');
    foreach($tickets as $ticket){
        $ticketString = $ticket['quantity'] . 'x ';

        if (isset($ticket['attributes']['date']) && strtotime($ticket['attributes']['date']) != null) {
            $ticketString .= date('l d F', strtotime($ticket['attributes']['date']));
        }

        if (isset($ticket['attributes']['startTime'])) {
            $ticketString .= ' at ' . date('H:i', strtotime($ticket['attributes']['startTime']));
        }

        $ticketString .= ' Haarlem ' . $ticket['attributes']['category'] . ' - ' . $ticket['attributes']['name'];
        $ticketString .= ' ' . chr(128) . ' ' .  number_format($ticket['totalPrice'], 2, ",", ".");
        $pdf->MultiCell(0,10, $ticketString);
        $pdf->Ln();
    }

    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(122,10,'');
    $pdf->Cell(50,10,'Total price: ' . chr(128) . ' ' . number_format($total, 2, ",", "."));

    $pdf->Output('F', $location);
}