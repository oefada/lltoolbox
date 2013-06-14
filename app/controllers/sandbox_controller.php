<?php
class SandboxController extends AppController
{
    public $name = 'Sandbox';
    public $uses = array();

    public function processorTest()
    {
        $paymentSettings = array(
            'UserPaymentSetting' => array(
                'nameOnCard' => 'Mort Wilson',
                'expMonth' => date('m'),
                'expYear' => date('y', strtotime('+1 year')),
                'ccNumber' => '4111111111111111',
                'address1' => '1234 Test Ave',
                'address2' => '',
                'city' => 'Los Angeles',
                'state' => 'CA',
                'postalCode' => '90046',
                'country' => 'USA'
            )
        );

        $ticket = array(
            'Ticket' => array(
                'billingPrice' => 100,
                'ticketId' => uniqid()
            )
        );

        require_once(APP . '/vendors/pp/Processor.class.php');
        $processor = new Processor('NOVA', true);
        $processor->InitPayment($paymentSettings, $ticket);
        //var_dump($processor); die;
        $processor->SubmitPost();
        var_dump($processor->GetMappedResponse());
        var_dump($processor->GetResponseTxt());
        var_dump($processor->getResponseData());
        die;
    }
}
