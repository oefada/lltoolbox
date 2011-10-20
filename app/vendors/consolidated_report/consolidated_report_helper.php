<?php
require_once('PHPExcel-1.7.6' . DS . 'PHPExcel.php');
require_once('PHPExcel-1.7.6' . DS . 'PHPExcel' . DS . 'Writer' . DS . 'Excel2007.php');
require_once('PHPExcel-1.7.6' . DS . 'PHPExcel' . DS . 'Reader' . DS . 'Excel2007.php');

class ConsolidatedReportHelper
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
	 * @access	private
	 * @param	object
	 */
	private $ConsolidatedReport;

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
	 * @access	private
	 * @param	array
	 */
	private $dataToPopulate;
	

	/**
	 * Class constructer
	 * 
	 * @access	public
	 * @param	string template_path
	 * @param	string newFile_path
	 * @param	string outputFile_path
	 */
	public function __construct($template_path, $newFile_path, $outputFile_path, &$consolidated_report_model)
	{
		$this->template = $template_path;
		$this->newFile = $newFile_path;
		$this->outputFile = $outputFile_path;
		$this->ConsolidatedReport = $consolidated_report_model;
		
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
	 * Set data in the dataToPopulate array
	 * 
	 * @access	public
	 * @param	string worksheet_name
	 * @param	string label
	 * @param	string cell
	 * @param	string value
	 * @param	string format
	 */
	public function setDataToPopulate($worksheet_name, $cell, $value, $format = null)
	{
		$this->dataToPopulate[$worksheet_name][] = array(
			'cell' => $cell,
			'value' => $value,
			'format' => $format
		);
	}
	
	/**
	 * Get the dataToPopulate array
	 * 
	 * @access	public
	 * @return	array
	 */
	public function getDataToPopulate()
	{
		return $this->dataToPopulate;
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
	 * Utility method to set the formatting of a cell
	 *  
	 * @access	public
	 * @param	string cell
	 * @param	string format
	 * @return	object this
	 */
	public function setCellFormat($cell, $format)
	{
		$this->phpExcel->getActiveSheet()->getStyle($cell)->getNumberFormat()->setFormatCode($format);
		return $this;
	}
	
	/**
	 * Utility method to set worksheet/cell data
	 *  
	 * @access	public
	 * @param	array $spreadsheet_data
	 */
	public function populateFromArray($spreadsheet_data)
	{
		foreach($spreadsheet_data as $worksheet_name => $worksheet_data) {
			$this->setActiveWorksheet($worksheet_name);
			foreach($worksheet_data as $cell_data) {
				if (!is_null($cell_data['format'])) {
					$this->setCellFormat($cell_data['cell'], $cell_data['format']);
				}
				$this->setCellValue($cell_data['cell'], $cell_data['value']);
			}
		}
	}

	/**
	 * Writes the spreadsheet object from memory to a new file
	 * 
	 * @access	public
	 * @param	boolean inject_into_chart
	 */
	public function writeSpreadsheetObjectToFile($inject_into_chart = true)
	{
		$this->writer = new PHPExcel_Writer_Excel2007($this->phpExcel);
		$this->writer->save($this->newFile);
		if ($inject_into_chart) {
			// Output file will contain chart data from template
			file_put_contents($this->outputFile, file_get_contents($this->template));
			
			// Inject modified cell data into file with charts
			$this->injectDataIntoChartFile($this->outputFile, $this->newFile);
		} else {
			// Output file will not contain chart data
			file_put_contents($this->outputFile, file_get_contents($this->newFile));
		}
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
	
	/*
	 * Methods used to actually populate report
	 */
	
	/**
	 *
	 */
	public function populateDashboard($client_name, $membership_fee, $start_date, $end_date)
	{
		$sheet_name = 'Dashboard';
		
		$this->setDataToPopulate($sheet_name, 'J4', $client_name);
		$this->setDataToPopulate($sheet_name, 'J5', date('M j, Y', strtotime($start_date)) . ' - ' . date('M j, Y', strtotime($end_date)));
		$this->setDataToPopulate($sheet_name, 'J7', $membership_fee);
	}
	
	/**
	 *
	 */
	public function populateActivitySummary($start_date, $end_date, $call_cpm, $email_cpm)
	{
		$sheet_name = 'Activity Summary';
		
		// Fill in CPM Values
		$this->setDataToPopulate('Key - Legend', 'C8', $email_cpm);
		$this->setDataToPopulate('Key - Legend', 'C10', $call_cpm);

		// Fill in Data values
		$this->setDataToPopulate($sheet_name, 'A4', date('M j, Y', strtotime($start_date)));
		$this->setDataToPopulate($sheet_name, 'A10', date('F-y', strtotime($this->ConsolidatedReport->getMonthStartDate())));
		$this->setDataToPopulate($sheet_name, 'A25', date('M Y', strtotime($start_date)) . ' - ' . date('M Y', strtotime($end_date)));
		
		// Impression & Click Data
		$ll_impression_data = $this->ConsolidatedReport->getImpressionDataBySiteForCurrentMonth(1);
		$fg_impression_data = $this->ConsolidatedReport->getImpressionDataBySiteForCurrentMonth(2);
		$this->setDataToPopulate($sheet_name, 'B11', $ll_impression_data['impressions']);
		$this->setDataToPopulate($sheet_name, 'D11', $fg_impression_data['impressions']);
		$this->setDataToPopulate($sheet_name, 'F11', '0');
		$this->setDataToPopulate($sheet_name, 'B12', $ll_impression_data['clicks']);
		$this->setDataToPopulate($sheet_name, 'D12', $fg_impression_data['clicks']);
		$this->setDataToPopulate($sheet_name, 'F12', '0');
		
		$ll_impression_data = $this->ConsolidatedReport->getImpressionDataBySiteForYearToDate(1);
		$fg_impression_data = $this->ConsolidatedReport->getImpressionDataBySiteForYearToDate(2);		
		$this->setDataToPopulate($sheet_name, 'B26', $ll_impression_data['impressions']);
		$this->setDataToPopulate($sheet_name, 'D26', $fg_impression_data['impressions']);
		$this->setDataToPopulate($sheet_name, 'F26', '0');
		$this->setDataToPopulate($sheet_name, 'B27', $ll_impression_data['clicks']);
		$this->setDataToPopulate($sheet_name, 'D27', $fg_impression_data['clicks']);
		$this->setDataToPopulate($sheet_name, 'F27', '0');			
		
		// Call Data
		$this->setDataToPopulate($sheet_name, 'B15', $this->ConsolidatedReport->getCallCountBySiteForCurrentMonth(1));
		$this->setDataToPopulate($sheet_name, 'D15', $this->ConsolidatedReport->getCallCountBySiteForCurrentMonth(2));
		$this->setDataToPopulate($sheet_name, 'F15', $this->ConsolidatedReport->getCallCountBySiteForCurrentMonth(3));
		$this->setDataToPopulate($sheet_name, 'B30', $this->ConsolidatedReport->getCallCountBySiteForYearToDate(1));
		$this->setDataToPopulate($sheet_name, 'D30', $this->ConsolidatedReport->getCallCountBySiteForYearToDate(2));
		$this->setDataToPopulate($sheet_name, 'F30', $this->ConsolidatedReport->getCallCountBySiteForYearToDate(3));

		// Email Data
		$this->setDataToPopulate($sheet_name, 'B13', $this->ConsolidatedReport->getEmailCountBySiteForCurrentMonth(1));
		$this->setDataToPopulate($sheet_name, 'B28', $this->ConsolidatedReport->getEmailCountBySiteForYearToDate(1));
		$this->setDataToPopulate($sheet_name, 'D13', $this->ConsolidatedReport->getEmailCountBySiteForCurrentMonth(2));
		$this->setDataToPopulate($sheet_name, 'D28', $this->ConsolidatedReport->getEmailCountBySiteForYearToDate(2));
	}
	
	/**
	 *
	 */
	public function populateBookings()
	{
		$sheet_name = 'Bookings';
		$booking_information = $this->ConsolidatedReport->getBookingInformation();
		
		// Luxury Link, current month
		$this->setDataToPopulate($sheet_name, 'C7', $booking_information['Luxury Link']['current_month']['bookings']);
		$this->setDataToPopulate($sheet_name, 'D7', $booking_information['Luxury Link']['current_month']['room_nights']);
		$this->setDataToPopulate($sheet_name, 'E7', $booking_information['Luxury Link']['current_month']['gross_bookings']);
		
		// Family Getaway, current month
		$this->setDataToPopulate($sheet_name, 'C8', $booking_information['Family Getaway']['current_month']['bookings']);
		$this->setDataToPopulate($sheet_name, 'D8', $booking_information['Family Getaway']['current_month']['room_nights']);
		$this->setDataToPopulate($sheet_name, 'E8', $booking_information['Family Getaway']['current_month']['gross_bookings']);
		
		// Vacationist, current month
		$this->setDataToPopulate($sheet_name, 'C9', $booking_information['Vacationist']['current_month']['bookings']);
		$this->setDataToPopulate($sheet_name, 'D9', $booking_information['Vacationist']['current_month']['room_nights']);
		$this->setDataToPopulate($sheet_name, 'E9', $booking_information['Vacationist']['current_month']['gross_bookings']);		
		
		// Luxury Link, YTD
		$this->setDataToPopulate($sheet_name, 'C18', $booking_information['Luxury Link']['year_to_date']['bookings']);
		$this->setDataToPopulate($sheet_name, 'D18', $booking_information['Luxury Link']['year_to_date']['room_nights']);
		$this->setDataToPopulate($sheet_name, 'E18', $booking_information['Luxury Link']['year_to_date']['gross_bookings']);
		
		// Family Getaway, YTD
		$this->setDataToPopulate($sheet_name, 'C19', $booking_information['Family Getaway']['year_to_date']['bookings']);
		$this->setDataToPopulate($sheet_name, 'D19', $booking_information['Family Getaway']['year_to_date']['room_nights']);
		$this->setDataToPopulate($sheet_name, 'E19', $booking_information['Family Getaway']['year_to_date']['gross_bookings']);
		
		// Vacationist, YTD
		$this->setDataToPopulate($sheet_name, 'C20', $booking_information['Vacationist']['year_to_date']['bookings']);
		$this->setDataToPopulate($sheet_name, 'D20', $booking_information['Vacationist']['year_to_date']['room_nights']);
		$this->setDataToPopulate($sheet_name, 'E20', $booking_information['Vacationist']['year_to_date']['gross_bookings']);		
		
		// Refunds, current month
		$this->setDataToPopulate($sheet_name, 'G12', 0);
		
		// Refunds, YTD
		$this->setDataToPopulate($sheet_name, 'G23', 0);	
	}
	
	/**
	 *
	 */
	public function populateImpressions()
	{
		$sheet_name = 'Impressions';

		$month_column_map = array(
			1 => 'B',
			2 => 'C',
			3 => 'D',
			4 => 'E',
			5 => 'F',
			6 => 'G',
			7 => 'H',
			8 => 'I',
			9 => 'J',
			10 => 'K',
			11 => 'L',
			12 => 'M',
			13 => 'N'
			
		);
		
		$month_name_map = array(
			'1' => 'Jan',
			'2' => 'Feb',
			'3' => 'Mar',
			'4' => 'Apr',
			'5' => 'May',
			'6' => 'Jun',
			'7' => 'Jul',
			'8' => 'Aug',
			'9' => 'Sep',
			'10' => 'Oct',
			'11' => 'Nov',
			'12' => 'Dec'			
		);
		
		$current_year = $start_year = date('Y', strtotime($this->ConsolidatedReport->getLoaStartDate()));
		$start_month = (int) date('n', strtotime($this->ConsolidatedReport->getLoaStartDate()));
		$end_month = date('n', strtotime($this->ConsolidatedReport->getMonthEndDate()));
		
		for($i = 1; $i <= 13; $i++) {
			$this->setDataToPopulate($sheet_name, $month_column_map[$i] . '7', $month_name_map[$start_month] . '-' . substr($current_year, 2, 4));
			$new_month_column_map[$start_month] = $month_column_map[$i];
			$column_map[$current_year . '-' . $start_month] = $month_column_map[$i];
			$start_month++;
			if($start_month > 12) {
				$start_month = 1;
				$current_year++;
			}			
		}

		$impression_details = $this->ConsolidatedReport->getImpressions();

		// Populate Impressions by Site
		foreach($impression_details as $key => $impression_detail) {
			if ($key == 'Luxury Link') {
				$spreadsheet_row = 8;
			} else if ($key == 'Family Getaway') {
				$spreadsheet_row = 9;
			} else {
				$spreadsheet_row = 10;
			}
			foreach($impression_detail as $impression_data) {
				$impression_data = array_shift($impression_data);
				$key = $impression_data['year'] . '-' . $impression_data['month'];
				$cell = $column_map[$key] . $spreadsheet_row;

				$impressions_by_type[$key]['portfolio_microsite'] += $impression_data['productview'];
				$impressions_by_type[$key]['destination'] += $impression_data['destinationview'];
				$impressions_by_type[$key]['search'] += $impression_data['searchview'];
				$impressions_by_type[$key]['email'] += $impression_data['email'];
				
				if (isset($column_map[$key])) {
					$this->setDataToPopulate($sheet_name, $cell, $impression_data['total_impressions']);
				}
			}
		}

		//Populate Impressions by Type
		foreach($impressions_by_type as $key => $impression_data) {
			$row = $column_map[$key];
			if (isset($column_map[$key])) {
				$this->setDataToPopulate($sheet_name, $row . 18, $impression_data['portfolio_microsite']);
				$this->setDataToPopulate($sheet_name, $row . 19, $impression_data['destination']);
				$this->setDataToPopulate($sheet_name, $row . 20, $impression_data['search']);
				$this->setDataToPopulate($sheet_name, $row . 21, $impression_data['email']);
			}
		}
	}

	/**
	 *
	 */
	public function populateContactDetails()
	{
		$sheet_name = 'Traveler Details';
		$contact_details = $this->ConsolidatedReport->getContactDetails();
		
		foreach($contact_details as $key => $contact_detail) {
			$spreadsheet_row = $key + 10;
			$this->setDataToPopulate($sheet_name, "A$spreadsheet_row", $contact_detail['Lead Type']);
			$this->setDataToPopulate($sheet_name, "B$spreadsheet_row", $contact_detail['Site']);
			$this->setDataToPopulate($sheet_name, "C$spreadsheet_row", $contact_detail['Activity Date']);
			$this->setDataToPopulate($sheet_name, "D$spreadsheet_row", $contact_detail['Arrival']);
			$this->setDataToPopulate($sheet_name, "E$spreadsheet_row", $contact_detail['Departure']);
			$this->setDataToPopulate($sheet_name, "F$spreadsheet_row", $contact_detail['Room Nights']);
			$this->setDataToPopulate($sheet_name, "G$spreadsheet_row", $contact_detail['Booking Amount']);
			$this->setDataToPopulate($sheet_name, "H$spreadsheet_row", $contact_detail['Call Duration']);
			$this->setDataToPopulate($sheet_name, "I$spreadsheet_row", $contact_detail['Booking Type']);
			$this->setDataToPopulate($sheet_name, "J$spreadsheet_row", $contact_detail['Phone']);
			$this->setDataToPopulate($sheet_name, "K$spreadsheet_row", $contact_detail['Firstname']);
			$this->setDataToPopulate($sheet_name, "L$spreadsheet_row", $contact_detail['Lastname']);
			$this->setDataToPopulate($sheet_name, "M$spreadsheet_row", $contact_detail['Email']);
			$this->setDataToPopulate($sheet_name, "N$spreadsheet_row", $contact_detail['Opt-in']);
			$this->setDataToPopulate($sheet_name, "O$spreadsheet_row", $contact_detail['Address']);
			$this->setDataToPopulate($sheet_name, "P$spreadsheet_row", $contact_detail['City']);
			$this->setDataToPopulate($sheet_name, "Q$spreadsheet_row", $contact_detail['State']);
			$this->setDataToPopulate($sheet_name, "R$spreadsheet_row", $contact_detail['Zip']);
			$this->setDataToPopulate($sheet_name, "S$spreadsheet_row", $contact_detail['Country']);
			$this->setDataToPopulate($sheet_name, "T$spreadsheet_row", $contact_detail['Median Household Income']);
			$this->setDataToPopulate($sheet_name, "U$spreadsheet_row", $contact_detail['Per Capita Income']);
			$this->setDataToPopulate($sheet_name, "V$spreadsheet_row", $contact_detail['Median Earnings']);
		}
	}
}
?>