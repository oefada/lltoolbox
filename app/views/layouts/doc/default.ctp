<?
if (empty($this->pageTitle )) {
	$this->pageTitle = date('Y-m-d');
}

header("Content-type: application/vnd.ms-word");
header("Content-Disposition: attachment;Filename={$this->pageTitle}.doc");

echo "<html>";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">";
echo "<head>";
echo "<style>";
include('css/doc.css');
echo "</style>";
echo "</head>";
echo "<body>";
echo $content_for_layout;
echo "</body>";
echo "</html>";
?>