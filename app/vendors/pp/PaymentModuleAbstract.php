<?php
abstract class PaymentModuleAbstract
{
    protected $url;
    protected $mappedParams;
    protected $postData;
    protected $response;

    /**
     * @return mixed
     */
    public function getMappedParams()
    {
        return $this->mappedParams;
    }

    /**
     * @return mixed
     */
    public function getPostData()
    {
        return $this->postData;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param $cvc
     */
    public function addCvc($cvc)
    {
        // STUB, required by Interface
    }
}
