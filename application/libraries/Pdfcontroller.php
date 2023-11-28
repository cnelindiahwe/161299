<?php

// Load mPDF library (assuming you've placed it in your project)
require_once(dirname(dirname(__FILE__)).'/third_party/vendor_pdf/autoload.php');

class Pdfcontroller extends CI_Controller {

    public function generate_pdf($htmltable,$filename) {
        $mpdf = new \Mpdf\Mpdf(['setAutoTopMargin' => 'pad','margin_left' => 0,'margin_right' => 0,'margin_header' => 0,'margin_top'=> 0 ,'padding'=> 80]);

        $mpdf->WriteHTML($htmltable);



        $file_name = $filename.'.pdf';
        
        
        
        $mpdf->Output($file_name, 'I');
        $mpdf->debug = true;
        
        
        $mpdf->Output();
    }
    public function download_pdf($htmltable,$invoicenumber) {


        // Create an instance of Mpdf
        $mpdf = new \Mpdf\Mpdf([
            'setAutoTopMargin' => 'pad',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_header' => 0,
            'margin_top' => 0,
            'padding' => 80
        ]);

        // Write HTML content to the PDF
        $mpdf->WriteHTML($htmltable);

        // Define the folder where you want to save the PDF files
        $pdfFolder = FCPATH . 'pdfs/';

        // Check if the folder exists, and if not, create it
        if (!is_dir($pdfFolder)) {
            mkdir($pdfFolder, 0777, true);
        }

        // Generate a unique file name for the PDF
        $file_name = 'ZOW-Invoice-'.$invoicenumber.'.pdf';

        // Define the full path to save the PDF file
        $pdfPath = $pdfFolder . $file_name;

        // Output the PDF to the file
        // $mpdf->Output($pdfPath, 'F');

        $mpdf->Output($pdfPath, \Mpdf\Output\Destination::FILE);

        // Now, you can force a download of the generated PDF
        // header("Content-Type: application/pdf");
        // header("Content-Disposition: attachment; filename=\"$file_name\"");
        return $file_name;
    }
}