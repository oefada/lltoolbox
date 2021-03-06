<?php
class SandboxController extends AppController
{
    public $name = 'Sandbox';
    public $uses = array();

    public function tokenizerTest()
    {
        App::import('Model', 'UserPaymentSetting');
        $ups = new UserPaymentSetting();
        var_dump($ups->tokenizeCcNum('4111111111111111', '0415'));
        die;
    }

    public function processorTest($processor = 'NOVA')
    {
        $testTransaction = true;

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
                'ticketId' => uniqid(),
                'billingPrice' => 100
            )
        );

        require_once(APP . '/vendors/pp/Processor.class.php');
        $processor = new Processor($processor, $testTransaction);
        $processor->InitPayment($paymentSettings, $ticket);
        $processor->SubmitPost();

        var_dump($processor->getModule()->getMappedResponse());
        var_dump($processor->getModule()->getResponseTxt());
        var_dump($processor->getModule()->getResponse());
        die;
    }
}
