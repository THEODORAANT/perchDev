<?php
// include autoloader
require_once 'dompdf/autoload.inc.php';

// reference the Dompdf namespace
use Dompdf\Dompdf;

// instantiate and use the dompdf class
$dompdf = new Dompdf();

//$dompdf->loadHtml('hello world');
   $output2=trim($_POST['output2'],'"');

    $dompdf->loadHtml($output2);
    $dompdf->set_option('isRemoteEnabled', true);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream();

?>
