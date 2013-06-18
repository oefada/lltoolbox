<?php
interface PaymentModuleInterface
{
    public function getUrl();
    public function ProcessResponse($raw_response);
    public function getPostSale();
    public function ChargeSuccess($response);
    public function GetResponseTxt($response);
    public function GetMappedResponse($response);
    public function IsValidResponse($response, $valid_param);
    public function AddCvc($cvc);
    public function getMappedParams();
    public function getPostData();
}
