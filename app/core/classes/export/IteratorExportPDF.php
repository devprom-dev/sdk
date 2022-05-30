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
            'margin_left' => 9,
            'margin_right' => 3,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 3,
            'default_font_size' => 8
        ));
        $pdf->WriteHTML($this->buildHtml('printable-pdf'), 2);

        $file_name = preg_replace('/[\.\,\+\)\(\)\:\;]/i', '_', html_entity_decode($this->getName(), ENT_QUOTES | ENT_HTML401, APP_ENCODING)).'.pdf';
        $file_name = EnvironmentSettings::getBrowserIE() ? rawurlencode($file_name) : $file_name;
        $pdf->Output($file_name, 'D');
 	}
}