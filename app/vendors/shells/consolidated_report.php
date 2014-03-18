<?php
App::import('Core', 'Controller');
App::import('Component', 'Email');
App::import('Core', 'ConnectionManager');
App::Import('Model', 'Client');
App::Import('Model', 'Loa');
App::Import('Model', 'User');
App::Import('Model', 'ConsolidatedReport');
App::import(
    'Vendor',
    'ConsolidatedReportHelper',
    array('file' => 'consolidated_report' . DS . 'consolidated_report_helper.php')
);
class ConsolidatedReportShell extends Shell
{
    private $isProduction = false;

    private $Controller;
    private $Email;
    private $Client;
    private $Loa;
    private $User;
    private $ConsolidatedReport;

    private static $logfile = 'consolidated_report';
    private $errors = array();

    /**
     *
     */
    public function initialize()
    {
        self::log('Process Started.');
        Configure::write('Cache.disable', true);
        $this->Client = new Client();
        $this->Loa = new Loa();
        $this->User = new User();
        $this->Controller = new Controller();
        $this->Email = new EmailComponent();
        $this->Email->initialize($this->Controller);
        $this->ConsolidatedReport = new ConsolidatedReport();
    }

    /**
     *
     */
    public function __destruct()
    {
        self::log('Process Completed.');
    }

    /**
     * Main routine
     *
     * @access    public
     */
    public function main()
    {
        $this->isProduction = isset($this->params['production']) ? true : false;

        if (isset($this->params['useSkynetData'])) {
            $this->ConsolidatedReport->setUseSkynetData();
            self::log("Using Skynet Data for this report");
        } else {
            $this->ConsolidatedReport->setDoNotUseSkynetData();
            self::log("Not using Skynet Data for this report");
        }


        $report_date = isset($this->params['report_date']) ? $this->params['report_date'] : null;
        $client_id = isset($this->params['client_id']) ? $this->params['client_id'] : null;

        // Client & LOA Details
        $client_details = $this->ConsolidatedReport->getClientDetails($client_id);
        $loa_details = $this->ConsolidatedReport->getLoaDetails($client_id, $report_date);

        if ($loa_details !== false) {
            // The client has a current LOA around the report date, generate the report

            // Get contact details
            $contact_details = $this->Client->getClientContactDetails($client_id);
            if ($contact_details === false) {
                self::log("Error - There are no contact details for client_id: $client_id");
                return 1;
            }

            if (!$this->isProduction) {
                self::log("This is not a production run. Reports will not be sent to the client.");
            } else {
                self::log("This is a production run. Reports will be sent to the client.");
                $send_report_to = array();
                $property_name = $contact_details[0]['client_name'];
                $account_manager_email = $contact_details[0]['account_manager_email'];
                foreach ($contact_details as $contact_detail) {
                    $send_report_to[] = $contact_detail['contact_email'];
                }
            }

            // Set date, fee, and output file parameters
            $loa_start_date = date('Y-m-d', strtotime($loa_details['Loa']['startDate']));
            $loa_end_date = date('Y-m-d', strtotime($loa_details['Loa']['endDate']));
            $membership_fee = $loa_details['Loa']['membershipFee'];
            $outputDir = TMP . "consolidated_reports/" . $report_date;

            // Report initialization variables
            // If start date is after 2012-04-30, use the non-FG template
            if ($loa_start_date >= '2012-04-30 23:59:59') {
                $template = 'consolidated_report_revision-12-nonFG.xlsx';
            } else {
                $template = 'consolidated_report_revision-12.xlsx';
            }
            self::log("Template: $template");

            if (!is_dir($outputDir)) {
                try {
                    self::log("Output Directory $outputDir does not exist. Creating it.");
                    mkdir($outputDir);
                    self::log('Output Directory created successfully');
                } catch (Exception $e) {
                    self::log(
                        "Error - There was an issue creating the output directory. Message: '" . $e->getMessage() . "'"
                    );
                    return 1;
                }

            }
            $outputFile = $outputDir . '/' . $client_details['Client']['seoName'] . '_' . $loa_start_date . '_to_' . $report_date . '_consolidated_report.xlsx';

            // Log date and filename parameters
            self::log("Generating Report for client_id: $client_id, report_date: $report_date");
            self::log("LOA Period: $loa_start_date - $loa_end_date");
            self::log("Filepath: $outputFile");

            // Initialize the report Model
            self::log("Initializing the report model.");
            $this->ConsolidatedReport->create($client_id, $report_date, $loa_start_date, $loa_end_date);
            self::log("The report model has been initialized successfully.");

            // Create the report object
            self::log("Creating a blank report object in memory.");
            $report = new ConsolidatedReportHelper($template, $outputFile, $this->ConsolidatedReport);
            self::log("The blank report object has been created successfully.");

            // Populate the report
            self::log('Building the report data array.');
            $report->populateDashboard(
                $client_details['Client']['name'],
                $membership_fee,
                $loa_start_date,
                $this->ConsolidatedReport->getMonthEndDate()
            );
            $report->populateActivitySummary(
                $loa_start_date,
                $this->ConsolidatedReport->getMonthEndDate(),
                $client_details['AccountManager']['name'],
                $client_details['AccountManager']['email'],
                15,
                2
            );
            $report->populateBookings();
            $impression_details = $report->populateImpressions();
            $report->populateContactDetails();
            self::log('The report data array was built successfully.');

            $llImpressions = 0;
            $fgImpressions = 0;
            if (isset($impression_details['Luxury Link'])) {
                $llImpressions = array_shift(array_pop($impression_details['Luxury Link']));
                $llImpressions = $llImpressions['productview'];
            } else {
                $llImpressions = 0;
            }
            if (isset($impression_details['Family Getaway'])) {
                $fgImpressions = array_shift(array_pop($impression_details['Family Getaway']));
                $fgImpressions = $fgImpressions['productview'];
            } else {
                $fgImpressions = 0;
            }
            $emailReport = (($llImpressions + $fgImpressions) >= 300);
            if (!$emailReport) {
                self::log('This report will not be sent to the client as there are not enough impressions for last month.');
            }

            // Save array to spreadsheet object
            try {
                self::log('Saving the report data array to the report object.');
                $report->populateFromArray($report->getDataToPopulate());
                self::log('The save was successful.');

                // Write the report object to disk
                try {
                    self::log('Writing the report object to disk.');
                    $report->writeSpreadsheetObjectToFile();
                    self::log('The write was successful.');
                    if ($this->isProduction && $emailReport) {
                        self::log('Emailing report to ' . implode(', ', $send_report_to));
                        $this->emailReport(
                            $send_report_to,
                            $property_name,
                            $account_manager_email,
                            $report_date,
                            $outputFile
                        );
                    }
                    return 0;
                } catch (Exception $e) {
                    self::log(
                        "Error - There was an issue saving the report object to disk. Message: '" . $e->getMessage(
                        ) . "'"
                    );
                    return 1;
                }
            } catch (Exception $e) {
                self::log(
                    "Error - There was an issue saving the array to the spreadsheet object. Message: '" . $e->getMessage(
                    ) . "'"
                );
                return 1;
            }
        } else {
            self::log("This client doesn't have a current LOA within the report period.");
            return 1;
        }
    }

    /**
     * Utility method to log output
     *
     * @access    public
     * @param    string message to log
     */
    public function log($message)
    {
        parent::log($message, self::$logfile);
        echo date('Y-m-d H:i:s') . ' - ' . $message . "\n";
    }

    /**
     * Overloading parent::welcome to do nothing
     */
    public function _welcome()
    {
        // do nothing
    }

    /**
     * Email the generated report
     *
     * @access    private
     * @param    array to address(es)
     * @param    string property name
     * @param    string account manager email address
     * @param    string path to file to attach
     */
    private function emailReport($recipient, $property_name, $account_manager_email, $report_date, $file)
    {
        ini_set('session.save_handler', 'files');
        $this->Email->reset();
        $this->Email->from = 'Luxury Link Travel Group <accounts@luxurylink.com>';
        $this->Email->to = 'Luxury Link Travel Group <noreply@luxurylink.com>';
        $this->Email->bcc = $recipient;
        $this->Email->subject = "Monthly Marketing Report for $property_name from LuxuryLink.com - Including Detailed Lead Information";
        $this->Email->template = 'consolidated_report_email';
        $this->Email->sendAs = 'html';
        $this->Controller->set('account_manager_email', $account_manager_email);
        $this->Controller->set('property_name', $property_name);
        $this->Email->attachments = array($file);
        $this->Email->send();
    }
}
