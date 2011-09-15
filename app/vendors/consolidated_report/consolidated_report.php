<?php
require_once('PHPExcel-1.7.6' . DS . 'PHPExcel.php');
require_once('PHPExcel-1.7.6' . DS . 'PHPExcel' . DS . 'Writer' . DS . 'Excel2007.php');
require_once('PHPExcel-1.7.6' . DS . 'PHPExcel' . DS . 'Reader' . DS . 'Excel2007.php');

class ConsolidatedReport
{
	/**
	 * Array of worksheets in the spreadsheet template
	 * 
	 * @access	private
	 * @param	array
	 */
	private $worksheets = array(
		1 => 'Dashboard',
		2 => 'Activity Summary',
		3 => 'Bookings',
		4 => 'Impressions',
		5 => 'Leads By Geo',
		6 => 'Contact Details',
		7 => 'Key - Legend'
	);
	
	/**
	 * Path to the spreadsheet template
	 * 
	 * @access	private
	 * @param	string
	 */
	private $template;
	
	/**
	 * Path to the new file that will be created from the spreadsheet object
	 * 
	 * @access	private
	 * @param	string
	 */
	private $newFile;
	
	/**
	 * Path to the file that will contain the data from $newFile and the chart
	 * data from $template
	 * 
	 * @access	private
	 * @param	string
	 */
	private $outputFile;
	
	/**
	 * Filename that will be generated for downloads
	 * 
	 * @access	private
	 * @param	string
	 */
	private $filename;
	
	/**
	 * @access	private
	 * @param	object
	 */
	private $phpExcel;
	
	/**
	 * @access	private
	 * @param	object
	 */
	private $writer;	
	
	/**
	 * Class constructer
	 */
	public function __construct($template_path, $newFile_path, $outputFile_path, $filename)
	{
		$this->template = $template_path;
		$this->newFile = $newFile_path;
		$this->outputFile = $outputFile_path;
		$this->filename = $filename;
		
		$this->phpExcel = PHPExcel_IOFactory::createReader('Excel2007')->load($this->template);
		
		// Create file that will contain new data and charts
		file_put_contents($this->outputFile, file_get_contents($this->template));
	}
	
	/**
	 * 
	 */
	public function __destruct()
	{
		
	}
	
	public function setActiveWorksheet($worksheet)
	{
		$worksheet_index = null;
		if (is_string($worksheet)) {
			$worksheet_index = array_search($worksheet, $this->worksheets);
		} else if (is_int($worksheet)) {
			$worksheet_index = $worksheet;
		}
 
		if ($worksheet_index && isset($this->worksheets[$worksheet_index])) {
			$this->phpExcel->setActiveSheetIndex($worksheet_index);
		} else {
			throw new Exception('Invalid Worksheet');
		}
	}
	
	/**
	 * 
	 */
	public function setCellValue($cell, $value)
	{
		$this->phpExcel->getActiveSheet()->setCellValue($cell, $value);
	}
	
	/**
	 * 
	 */
	public function getWorkSheets()
	{
		return $this->worksheets;
	}
	
	public function test()
	{
		$this->setActiveWorksheet('Bookings');
		$this->setCellValue('E18', '1');
		$this->setCellValue('E19', '5');
		$this->setCellValue('E20', '10');
	}	
	
	/**
	 * 
	 */
	public function writeSpreadsheetObjectToFile()
	{
		$this->writer = new PHPExcel_Writer_Excel2007($this->phpExcel);
		$this->writer->save($this->newFile);
		$this->injectDataIntoChartFile($this->outputFile, $this->newFile);
	}
	
	/**
	 * 
	 */
	public function getSpreadsheetData()
	{
		return file_get_contents($this->outputFile);
	}
	
	/**
	 * 
	 */
	private function injectDataIntoChartFile($originalFile, $updatedFile, $updateSheetData = false)
	{
		$zipUpdated= new ZipArchive();
		$zipUpdated->open($updatedFile);
		$zipOriginal = new ZipArchive();
		$zipOriginal->open($originalFile);		
		$xmlWorkbook = simplexml_load_string($zipUpdated->getFromName("xl/_rels/workbook.xml.rels"));
		foreach($xmlWorkbook->Relationship as $ele) {
			if ($ele["Type"] == "http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet") {
				$currentSheetName = "xl/". $ele["Target"];
				$updatedSheet = simplexml_load_string($zipUpdated->getFromName($currentSheetName));
				$origSheet = simplexml_load_string($zipOriginal->getFromName($currentSheetName));
				$origSheet->sheetData ="";
				$str = str_replace("<sheetData></sheetData>", $updatedSheet->sheetData->asXml(), $origSheet->asXml());
				$zipOriginal->addFromString($currentSheetName, $str);
			}
		}

		if ($updateSheetData) {
			$workbookXML = "xl/workbook.xml";
  			$origNames = simplexml_load_string($zipOriginal->getFromName($workbookXML));
  			$updatedNames = simplexml_load_string($zipUpdated->getFromName($workbookXML));

	  		$origNames->definedNames = "";
  			$nRanges = str_replace("<definedNames></definedNames>", $updatedNames->definedNames->asXML(), $origNames->asXML());
  			$zipOriginal->addFromString($workbookXML, $nRanges);
		}
		
		$updatedStrings = simplexml_load_string($zipUpdated->getFromName( "xl/sharedStrings.xml"));
		$zipOriginal->addFromString("xl/sharedStrings.xml", $updatedStrings->asXML());
		$zipOriginal->close();
		$zipUpdated->close();
	}
}
?>