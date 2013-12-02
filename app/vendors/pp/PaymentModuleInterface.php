<?php
interface PaymentModuleInterface
{
    public function addCvc($cvc);
    public function chargeSuccess();
    public function getMappedParams();
    public function getMappedResponse();
    public function getPostData();
    public function getPostSale();
    public function getResponse();
    public function getResponseTxt();
    public function getUrl();
    public function isValidResponse($valid_param);
    public function processResponse($raw_response);
}
