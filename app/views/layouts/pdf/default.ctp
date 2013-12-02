<?
App::import('Vendor','dompdf', array('file' => 'dompdf_config.inc.php'));

if (empty($this->pageTitle )) {
	$this->pageTitle = date('Y-m-d');
}
?>
<style>
<? include('css/pdf.css'); ?>
</style>
<?
$dompdf = new DOMPDF();
$dompdf->load_html($content_for_layout);
$dompdf->render();
$dompdf->stream("{$this->pageTitle}.pdf");
?>
