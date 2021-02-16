<?php
use \Mpdf\Mpdf;
include_once "IteratorExport.php";

class IteratorExportPDF extends IteratorExportHtml
{
	function export()
	{
        $pdf = new mPDF(array(
            'format' => 'A4',
            'orientation' => 'L',
            'margin_left' => 3,
            'margin_right' => 3,
            'margin_top' => 3,
            'margin_bottom' => 3,
            'margin_header' => 3,
            'margin_footer' => 3
        ));
        $pdf->WriteHTML($this->buildHtml(), 2);

        $file_name = preg_replace('/[\.\,\+\)\(\)\:\;]/i', '_', html_entity_decode($this->getName(), ENT_QUOTES | ENT_HTML401, APP_ENCODING)).'.pdf';
        $file_name = EnvironmentSettings::getBrowserIE() ? rawurlencode($file_name) : $file_name;
        $pdf->Output($file_name, 'D');
 	}
}