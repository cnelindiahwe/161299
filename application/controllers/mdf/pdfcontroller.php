<?php

// Load mPDF library (assuming you've placed it in your project)
require_once($_SERVER['DOCUMENT_ROOT'].'/application/third_party/mpdf/vendor/autoload.php');

use Mpdf\Mpdf;

class Pdfcontroller extends CI_Controller {

    public function generate_pdf($htmltable) {
        // Your HTML content stored in a variable
        $html = $htmltable;

        // Create a new mPDF document
        $mpdf = new Mpdf();

        // Set document properties
        $mpdf->SetTitle('Invoice PDF');
        $mpdf->SetAuthor('Zowtemp');
        $mpdf->SetCreator('Sunil');

        // Add a page
        $mpdf->WriteHTML($html);

        // Output the PDF as a downloadable file
        $mpdf->Output('invoice.pdf', 'D'); // 'D' prompts the user to download the file
    }
}