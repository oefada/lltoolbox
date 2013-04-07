<?php
App::import('Core', array('Model', 'Controller'));
App::import('Model', array('Reservation'));
App::import('Controller', array('Reservations'));
App::import('Controller', 'WebServiceTickets');

/**
 * cake post_trip_email
 */
class PostTripEmailShell extends Shell {
	/** The base URL for the survey sent in the emails.
	 *
	 * @var string
	 */
	private $baseURL = 'http://www.surveygizmo.com/s3/675896/How-was-your-Luxury-Link-Vacation';

	private $header = "
     ___        _     _____    _        ___            _ _
    | _ \___ __| |_  |_   _| _(_)_ __  | __|_ __  __ _(_) |
    |  _/ _ (_-<  _|   | || '_| | '_ \ | _|| '  \/ _` | | |
    |_| \___/__/\__|   |_||_| |_| .__/ |___|_|_|_\__,_|_|_|
                                |_|
";

	private $Reservation = null;
	private $WebServiceTicketsController = null;

	private $template = null;

	private $sendCount = 0;

	function main() {
		$this->template = file_get_contents(APP . '/vendors/shells/templates/post_trip_email.ctp');

		$this->Reservation = new Reservation();
		$this->WebServiceTicketsController = new WebServiceTicketsController();
		@$this->WebServiceTicketsController->constructClasses();
		$this->out($this->header);
		$this->hr();
		$this->out('Updating reservations:');
		// Get some test data ready
		// $this->Reservation->query('UPDATE reservation SET postTripEmailSent = 1 WHERE 1');
		// $this->Reservation->query('UPDATE reservation SET postTripEmailSent = 0 WHERE reservationId=41091');
		// $this->Reservation->query('UPDATE reservation SET postTripEmailSent = 0 WHERE 1 ORDER BY reservationId DESC LIMIT 500');

		$reservations = $this->getUnprocessedTickets();

		foreach ($reservations as $r) {
			$this->processPostTripEmail($r);
		}
		$this->out('Done.');
		$this->hr();
	}

	/**
	 * Query database to find reservations where a follow up email needs to be sent
	 */
	function getUnprocessedTickets() {
		$sql = "
SELECT
    client.clientId
    , client.name
    , clientLoaPackageRel.packageId
    , ticket.ticketId
    , ticket.userFirstName
    , ticket.userEmail1
    , ticket.userId
    , reservation.reservationId
    , reservation.departureDate
    , client.locationDisplay
    , ticket.ticketStatusId
FROM
    toolbox.client
    INNER JOIN toolbox.clientLoaPackageRel 
        ON (client.clientId = clientLoaPackageRel.clientId)
    INNER JOIN toolbox.ticket 
        ON (clientLoaPackageRel.packageId = ticket.packageId)
    INNER JOIN toolbox.reservation 
        ON (ticket.ticketId = reservation.ticketId)
WHERE (reservation.departureDate BETWEEN NOW() - INTERVAL 120 DAY AND NOW() - INTERVAL 3 DAY
    AND reservation.postTripEmailSent =0
    AND ticket.siteId =1)
    AND ticket.ticketStatusId BETWEEN 4 AND 6
ORDER BY reservation.departureDate ASC, ticket.userEmail1 ASC
LIMIT 200;
";
		$result = $this->Reservation->query($sql);

		/* Reformat the array for easier processing by the view */
		$tickets = array();
		foreach ($result as $r) {
			$ticketId = $r['ticket']['ticketId'];
			$tickets[$ticketId]['Ticket']['ticketId'] = $r['ticket']['ticketId'];
			$tickets[$ticketId]['User']['FirstName'] = ucwords($r['ticket']['userFirstName']);
			$tickets[$ticketId]['User']['Email'] = $r['ticket']['userEmail1'];
			$tickets[$ticketId]['User']['UserId'] = $r['ticket']['userId'];
			$tickets[$ticketId]['Reservation']['reservationId'] = $r['reservation']['reservationId'];
			$tickets[$ticketId]['Package']['packageId'] = $r['clientLoaPackageRel']['packageId'];
			$client = array();
			$clientName = '';
			$client['clientId'] = $r['client']['clientId'];
			$client['Name'] = $r['client']['name'];
			$client['Location'] = $r['client']['locationDisplay'];
			$tickets[$ticketId]['Clients'][$client['clientId']] = $client;
		}

		/* Generate the URL to the survey */
		foreach ($tickets as $tid => &$t) {
			$url = $this->baseURL;
			$url .= '?ticketid=' . urlencode($t['Ticket']['ticketId']);
			$url .= '&reservationid=' . urlencode($t['Reservation']['reservationId']);
			$url .= '&firstname=' . urlencode($t['User']['FirstName']);
			$clientids = $clientnames = $clientlocations = $bvclients = array();
			foreach ($t['Clients'] as $tid => $v) {
				$clientnames[] = $v['Name'];
				$clientids[] = $v['clientId'];
				$bvclients[] = $v['clientId']; 
				$clientlocations[$v['Location']] = $v['Location'];
			}
			$clientids = $this->array_to_english($clientids, '');
			$clientnames = $this->array_to_english($clientnames);
			$t['ClientIds'] = $clientids;
			$t['ClientNames'] = $clientnames;
			$url .= '&clientids=' . urlencode($clientids);
			$url .= '&clientname=' . urlencode($clientnames);
			if (count($clientlocations) != 1) {
				$url .= '&clientlocation=';
			} else {
				$url .= '&clientlocation=' . urlencode($this->array_to_english($clientlocations));
			}
			$t['ClientLocation'] = $this->array_to_english($clientlocations);
			$t['ClientLocationIn'] = ' in ' . $t['ClientLocation'];
			// $t['URL'] = $url;
			
			$t['URL'] = 'http://www.luxurylink.com/bv/container.php?bvaction=rr_submit_review&bvproductId=' . $bvclients[0] . '&bvusertoken=' . $this->getBvToken($t['User']['UserId']); 
		}
		return $tickets;
	}

	/**
	 * Sets the postTripEmailSent flag in the reservations table so that duplicate emails are never sent
	 *
	 * @param integer $id The reservationId to be marked
	 * @return boolean True unless the reservation has already been marked or could not be marked
	 */
	private function processPostTripEmail($reservationTicketData) {
		if (!is_numeric($reservationTicketData['Reservation']['reservationId'])) {
			return false;
		}
		$this->Reservation->recursive = -1;
		$this->Reservation->id = $reservationTicketData['Reservation']['reservationId'];

		// Check to see if the email has already been processed to avoid duplicate mails
		$result = $this->Reservation->read(array('postTripEmailSent'));
		if ($result['Reservation']['postTripEmailSent']) {
			return false;
		}
		// Update cell marking the reservation as processed
		$this->Reservation->save(array('postTripEmailSent' => '1'));
		$result = $this->Reservation->read(array('postTripEmailSent'));
		if ($result['Reservation']['postTripEmailSent']) {
			// The database now knows the email is being sent
			$this->sendEmail($reservationTicketData);
			return true;
		}
		return false;
	}

	/**
	 * Send an individual email to the email server
	 *
	 * @param array $data As associative array of data needed to generate and send the message.
	 * @return boolean True if the email was successfully sent, false otherwise.
	 */
	private function sendEmail($data) {
		$this->out("#" . ++$this->sendCount . " r:" . $data['Reservation']['reservationId'] . ' t:' . $data['Ticket']['ticketId'] . ' e:' . $data['User']['Email'] . ' f:' . $data['User']['FirstName']);
		$this->WebServiceTicketsController->sendPpvEmail($emailTo = $data['User']['Email'], $emailFrom = 'reservations@luxurylink.com', $emailCc = null, $emailBcc = null, $emailReplyTo = 'no-reply@luxurylink.com', $emailSubject = 'Rate your Luxury Link experience at ' . $data['ClientNames'], $emailBody = $this->getTemplate($this->array_flatten($data)), $ticketId = $data['Ticket']['ticketId'], $ppvNoticeTypeId = 37, $ppvInitials = NULL);
		return true;
	}

	private function getBvToken($uid, $maxage = 45) {
		if (intval($uid) == 0) { return ''; }
        $sharedkey = 'xRKkVuqYLRwsbFFMl0hvlnAim';
        $userStr = 'date=' . date('Ymd') . '&userid=' . $uid;
        if ($maxage) { $userStr .= '&maxage=' . $maxage; }
        return md5($sharedkey . $userStr) . bin2hex($userStr);
    }

	/**
	 * Takes an array and converts it to an English comma separated list.
	 *
	 * @param array $list A list of items
	 * @param string $separator Optional word prepended to last list item
	 * @return string The English sentence version of the inputted list
	 */
	function array_to_english($list, $separator = 'and') {
		if (!is_array($list)) {
			return $list;
		}
		if (count($list) == 1) {
			return end($list);
		}
		$string = '';
		$lastKey = end(array_keys($list));
		foreach ($list as $k => &$v) {
			if ($k != $lastKey) {
				$string .= str_replace(',', ' - ', $v) . ', ';
			} else {
				$string .= $separator . ' ' . str_replace(',', ' - ', $v);
			}
		}
		return $string;
	}

	function getTemplate($data) {
		$buffer = $this->template;
		$data['CurrentDate'] = date('F jS, Y');
		foreach ($data as $k => $v) {
			if (is_string($v)) {
				$buffer = str_replace('%%' . $k . '%%', $v, $buffer);
			}
		}
		return $buffer;
	}

	function array_flatten($a, $prefix = '') {
		if ($prefix != '') {$prefix .= '.';
		}
		$b = array();
		foreach ($a as $k => $v) {
			if (is_array($v)) {
				$b = array_merge($b, $this->array_flatten($v, $prefix . $k));
			} else {
				$b[$prefix . $k] = $v;
			}
		}
		return $b;
	}

	function help() {
		$this->out($this->header);
		$this->out('');
		$this->out("To print this help message:");
		$this->out("cake post_trip_email help");
		$this->out('');
		$this->out("To generate and send up to 200 emails:");
		$this->out("cake post_trip_email");
		$this->out('');
		$this->out("Example cron:");
		$this->out("*/20 8-17 * * * cd /home/html/toolbox;./cake/console/cake post_trip_email > /dev/null 2>&1");
		$this->out('');
		$this->out('Version: $Id$');
		$this->out('');
		
		$this->hr();
	}

}
