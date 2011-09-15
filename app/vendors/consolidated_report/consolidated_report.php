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
	private $worksheets;
	
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
		
		// Create the phpExcel object from a Reader loading a template
		$this->phpExcel = PHPExcel_IOFactory::createReader('Excel2007')->load($this->template);
		
		// Get the worksheet names
		$this->worksheets = $this->get()->getSheetNames();
	}

	/**
	 * Return the phpExcel object in case the object needs to be used directly
	 * 
	 * @access	public
	 * @return	object
	 */
	public function get()
	{
		return $this->phpExcel;
	}
	
	/**
	 * Get the worksheets in the loaded template
	 * 
	 * @access	public
	 * @return	array worksheet names
	 */
	public function getWorkSheets()
	{
		return $this->worksheets;
	}

	/**
	 * Set the active worksheet by name or index
	 * 
	 * @access	public
	 * @param	int or string worksheet name
	 */
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
			return $this;
		} else {
			throw new Exception('Invalid Worksheet');
		}
	}
	
	/**
	 * Set the value of a given cell
	 * 
	 * @access	public
	 * @param	string cell
	 * @param	mixed value
	 * @return	object this
	 */
	public function setCellValue($cell, $value)
	{
		$this->phpExcel->getActiveSheet()->setCellValue($cell, $value);
		return $this;
	}

	/**
	 * Writes the spreadsheet object from memory to a new file
	 * 
	 * @access	public
	 * @param	boolean inject_into_chart
	 */
	public function writeSpreadsheetObjectToFile($inject_into_chart = true)
	{
		$file_data = null;
		$this->writer = new PHPExcel_Writer_Excel2007($this->phpExcel);
		$this->writer->save($this->newFile);
		if ($inject_into_chart) {
			// Output file will contain chart data from template
			$file_data = file_get_contents($this->template);
			
			// Inject modified cell data into file with charts
			$this->injectDataIntoChartFile($this->outputFile, $this->newFile);
		} else {
			// Output file will not contain chart data
			$file_data = file_get_contents($this->newFile);
		}
		
		file_put_contents($this->outputFile, $file_data);
	}

	/**
	 * Returns the file contents of output file on disk
	 * 
	 * @access	public
	 * @return	mixed file data 
	 */
	public function getSpreadsheetData()
	{
		return file_get_contents($this->outputFile);
	}
	
	/**
	 * Test function
	 * TODO: Remove before release
	 */
	public function test()
	{
		$this->setActiveWorksheet('Bookings')
			 ->setCellValue('E18', '1')
			 ->setCellValue('E19', '2')
			 ->setCellValue('E20', '3');
	}

	/**
	 * Chart support is non-existant as of PHPExcel-1.7.6
	 * 
	 * Workaround: After the template is read, object is manipulated, and the
	 * the object is saved to disk, the newly created but chartless spreadsheet
	 * cell data is 'injected' into a spreadsheet containing the charts.
	 * 
	 * @access	public
	 * @param	string filepath of the spreadsheet with charts
	 * @param	string filepath of the spreadsheet sans charts / with updated data
	 * @param	boolean updateSheetData flag
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