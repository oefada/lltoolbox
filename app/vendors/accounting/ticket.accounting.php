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
    private $totalPromos;
    private $handlingFee = 40;

    public function __construct()
    {
        //$this->ticketAmount = ($this->ticketAmount - $this->handlingFee);
    }

    public function processPaymentDetails($arrPaymentDetails)
    {

        foreach ($arrPaymentDetails as $key => $payment) {

            if (
                $payment['pd']['isSuccessfulCharge'] !== '1'
            ) {
                //skip if unsuccessful charge
                continue;
            }

            if ($payment['pd']['paymentTypeId'] == '1') {
                //if charge, add revenue
                $this->addTicketRevenue($payment['pd']['paymentAmount']);
            } else {
                //if, COF, GC, or promo.
                //we'd add the amount as an adjustment
                $this->addTicketAdjustment($payment['pd']['paymentAmount']);

                if ($payment['pd']['paymentTypeId'] == '3' && $payment['pd']['paymentAmount'] > 0) {
                    //if COF, add to total COF
                    $this->addCOF($payment['pd']['paymentAmount']);
//                    var_dump($this->ticketAmount);
//                    var_dump(($payment['pd']['paymentAmount']));
                    if (($payment['pd']['paymentAmount'] == $this->ticketAmount) ||
                        $payment['pd']['paymentAmount'] > $this->ticketAmount
                        ) {
                        //TICKET4527: Item 4- f a Ticket was paid for in full with a CoF, the Revenue column should be '0' and the Adjustment column should be equal to the CoF value
                        $this->totalRevenue = 0;
                        $this->adjustAmount = $payment['pd']['paymentAmount'];
                        break;
                    }
                }
            }
        }
    }

    public function processPromoDetails($arrPromos)
    {
        if (!isset($arrPromos)) {
            return false;
        }
        if (!empty($arrPromos['amountOff'])) {
            $this->addTicketAdjustment($arrPromos['amountOff']);
            $this->addPromoAmount($arrPromos['amountOff']);
            return true;
        }
        if (!empty($arrPromos['percentOff'])) {

            $amountOff = $this->ticketAmount * ($arrPromos['percentOff'] / 100);

            $this->addTicketAdjustment($amountOff);
            $this->addPromoAmount($amountOff);
            return true;
        }
    }

    public function ticketAccountingSummary()
    {
        $results['totalRevenue'] = $this->getTotalRevenue();
        $results['adjustments'] = $this->getTotalAdjustments();
        $results['totalCOF'] = $this->getTotalCOFAmount();
        $results['totalPromos'] = $this->getTotalPromos();

        $this->resetValues();

        return $results;

    }
    public function resetValues()
    {
        $this->adjustAmount = 0;
        $this->ticketAmount = 0;
        $this->totalRevenue= 0;
    }

    /**
     * @param Sums all revenues.
     */
    public function addTicketRevenue($amount)
    {
        $amount = floatval($amount);
        if (isset($amount)) {
            $this->totalRevenue = $this->totalRevenue + $amount;
        }
    }

    public function addTicketAdjustment($amount)
    {
        $amount = floatval($amount);
        if (isset($amount)) {
            $this->adjustAmount = $this->adjustAmount + $amount;
        }
    }

    public function addCOF($amount)
    {
        $amount = floatval($amount);
        if (isset($amount)) {
            $this->totalCOF = $this->totalCOF + $amount;
        }
    }

    public function addPromoAmount($amount)
    {
        $amount = floatval($amount);
        if (isset($amount)) {
            $this->totalPromos = $this->totalPromos + $amount;
        }
    }

    public function setTicketAmount($price)
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

    public function getTotalPromos()
    {
        return $this->totalPromos;
    }

}
