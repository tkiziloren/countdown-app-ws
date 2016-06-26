<?php

require_once("Constants.php");
require_once("Utility.php");
require_once("Rest.php");
require_once("ArrayToXml.php");
require_once("StatsUtility.php");
@require_once "Mail.php";

class Stats extends Rest{


    protected function validateActionParameters()
    {
        if($this->action == Constants::$ACTION_GET_STATS){
            if ($this->requestObject->username == null || $this->requestObject->statsId == null)
                Utility::prepareResponse(Constants::$ERROR_INVALID_JSON);

            if(!StatsUtility::isStatsIdValid($this->requestObject->statsId))
                Utility::prepareResponse(Constants::$ERROR_INVALID_JSON);

            Utility::checkUserExists($this->requestObject);

            if ("ben" == $this->requestObject->statsId && Utility::getIdForUserName($this->requestObject->userOfInterest))
                self::prepareResponse(Constants::$RESULT_USERNAME_DOESNT_EXIST);
        }
        else {
            Utility::send404Response();
        }
    }

    protected function execute()
    {
        $username = null;
        if("ben" == $this->requestObject->statsId)
            $username = $this->requestObject->userOfInterest;
        else
            $username = $this->requestObject->username;

        $statsId = $this->requestObject->statsId;
        $this->responseObject = StatsUtility::getStats($username,$statsId);
        $this->resultCode = Constants::$SUCCESS_GENERAL_SUCCESS_CODE;
    }
}

$stats = new Stats();
$stats->performAction();