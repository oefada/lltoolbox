<?php

/**
 * User: oefada
 * Date: 5/21/14
 * Time: 8:53 PM
 */
Configure::write('debug', 0);
class TicketAccounting
{
    private $ticketAmount, $dateFirstSuccessfulPayment;
    private $totalCOF = 0;
    private $totalRevenue = 0;
    private $adjustAmount = 0;
    private $promotionAmount;

    function __construct()
    {

    }

    public function processPaymentDetails($arrPaymentDetails)
    {

        foreach ($arrPaymentDetails as $key => $payment) {

            if ($payment['pd']['isSuccessfulCharge'] !== '1') {
                //skip if unsuccessful charge
                continue;
            }

            if ($payment['pd']['paymentTypeId'] == '3' && $payment['pd']['paymentAmount'] > 0) {
                //if COF
                $this->addCOF($payment['pd']['paymentAmount']);
                if ($payment['pd']['paymentAmount'] == $this->ticketAmount) {
                    //TICKET4527: Item 4- f a Ticket was paid for in full with a CoF, the Revenue column should be '0' and the Adjustment column should be equal to the CoF value
                    $this->totalRevenue = 0;
                    $this->adjustAmount = $payment['pd']['paymentAmount'];
                    break;
                }
                //add amount to revenue
            }
            $this->addTicketRevenue($payment['pd']['paymentAmount']);
        }
    }

    public function processPromoDetails($arrPromos)
    {
        if (!isset($arrPromos, $arraPromoCodes)) {
            return false;
        }
        if (!empty($arrPromos['amountOff'])) {
            $this->addTicketAdjustment($arrPromos['amountOff']);
        }
        if (!empty($arrPromos['percentOff'])) {

            $amountOff = $this->ticketAmount * ($arrPromos['percentOff'] / 100);
            $this->addTicketAdjustment($amountOff);
        }
    }

    public function ticketAccountingSummary()
    {
        $results['totalRevenue'] = $this->getTotalRevenue();
        $results['adjustments'] = $this->getTotalAdjustments();
        $results['totalCOF'] = $this->getTotalCOFAmount();

        return $results;

    }

    /**
     * @param Sums all revenues.
     */
    public function addTicketRevenue($amount)
    {
        if (isset($amount)) {
            $this->totalRevenue = $this->totalRevenue + $amount;
        }
    }

    public function addTicketAdjustment($amount)
    {
        if (isset($amount)) {
            $this->adjustAmount = $this->adjustAmount + $amount;
        }
    }

    public function addCOF($amount)
    {
        if (isset($amount)) {
            $this->totalCOF = $this->totalCOF + $amount;
        }
    }

    public function setticketAmount($price)
    {
        if (isset($this->ticketAmount)) {
            unset($this->ticketAmount);
        }

        if (isset($price)) {
            $this->ticketAmount = $price;
        }
    }

    public function getTotalAdjustments()
    {
        return $this->adjustAmount;
    }

    public function getTotalRevenue()
    {
        return $this->totalRevenue;
    }

    public function getTotalCOFAmount()
    {
        return $this->totalCOF;
    }

}
