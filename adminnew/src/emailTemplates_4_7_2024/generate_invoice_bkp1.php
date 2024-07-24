<?php
require_once __DIR__ . '/vendor/autoload.php';

use Mpdf\Mpdf;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $html = $data['html'];

    $mpdf = new Mpdf();
    $mpdf->WriteHTML($html);

    // Specify the path where you want to save the PDF file
    $pdfFilePath = 'invoice/'.$data['invoice_number'].'.pdf';

    // Save the PDF file to the specified path
    $mpdf->Output($pdfFilePath, 'F');

    // Respond with a success message or the path to the saved PDF
    header('Content-Type: application/json');
    echo json_encode(['message' => 'PDF generated and saved successfully.', 'pdfPath' => $pdfFilePath]);

    // sleep 15 seconds
    // sleep(15);

    // // delete the invoice
    // unlink('invoice/'.$data['invoice_number'].'.pdf');
}
?>