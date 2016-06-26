<?php
/**
 * Created by JetBrains PhpStorm.
 * User: tkiziloren
 * Date: 29.06.2012
 * Time: 14:33
 * To change this template use File | Settings | File Templates.
 */
abstract class Rest
{
    protected $requestObject;
    protected $resultCode;
    protected $responseObject;
    protected $action;

    protected abstract function validateActionParameters();
    protected abstract function execute();

    public function performAction(){

        self::getAction();
        self::parseRequest();
        self::validateRequestObject();

        $this->validateActionParameters();
        $this->execute();
        $this->prepareResponse();
    }

    public function parseRequest()
    {
        $cipherText = Utility::getURLParameter(Constants::$URL_PARAM_CIPHER_TEXT);

        $plainText = null;

        if($cipherText)
        {
            $plainText = Utility::decryptText($cipherText, Constants::$key);
        }
        else
        {
            $base64EncodedText = Utility::getURLParameter(Constants::$URL_PARAM_PLAIN_BASE64_ENCODED_TEXT);
            if($base64EncodedText){
                $plainText = base64_decode($base64EncodedText);
            }
        }

        if(!$plainText)
            Utility::send404Response();

        $this->requestObject = json_decode($plainText,false);
    }

    public function getAction()
    {
        $this->action = Utility::getURLParameter(Constants::$URL_PARAM_ACTION);
        if(!$this->action)
            Utility::send404Response();
    }

    public function validateRequestObject(){
        if($this->requestObject == null)
            Utility::send404Response();

        //login işlemi için ilk başta kullanıcı var mı kontrolu yapmaya gerek yok action içerisinde zaten yapılacak
        if($this->action != Constants::$ACTION_LOGIN_USER && $this->action != Constants::$ACTION_SIGNUP_USER && $this->action!=Constants::$ACTION_REMIND_PASSWORD)
            Utility::checkUserExists($this->requestObject);
    }

    public function prepareResponse(){
        Utility::prepareFullResponse($this->resultCode, $this->responseObject);
    }

    public function setRequestObject($requestObject)
    {
        $this->requestObject = $requestObject;
    }

    public function getRequestObject()
    {
        return $this->requestObject;
    }

    public function setResponseObject($responseObject)
    {
        $this->responseObject = $responseObject;
    }

    public function getResponseObject()
    {
        return $this->responseObject;
    }

    public function setResultCode($resultCode)
    {
        $this->resultCode = $resultCode;
    }

    public function getResultCode()
    {
        return $this->resultCode;
    }


}

