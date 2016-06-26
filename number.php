<?php
/**
 * Created by JetBrains PhpStorm.
 * User: tkiziloren
 * Date: 31.05.2012
 * Time: 10:20
 * To change this template use File | Settings | File Templates.
 */
/* require the user as the parameter */
require_once("Constants.php");
require_once("Utility.php");
require_once("Rest.php");


class Number extends Rest{

    protected function execute()
    {
        if($this->action== Constants::$ACTION_SAVE_NUMBER_GAME_RESULTS)
            $this->insertNumberGameResult($this->requestObject);

    }

    protected function validateActionParameters()
    {
        $this->validateParametersForNumberGameResult($this->requestObject);
    }

    protected function validateParametersForNumberGameResult($jsonObject){

        if ($jsonObject->username == null || $jsonObject->targetNumber == null || $jsonObject->numbers == null
            || $jsonObject->remainingTime == null || $jsonObject->points == null || $jsonObject->userresult)
        {
            if($jsonObject->operationLog === null)
                Utility::prepareResponse(Constants::$ERROR_INVALID_JSON);
        }
        //Utility::printOperationGameResult($jsonResult);
    }




    protected function insertNumberGameResult($jsonObject){

        $userid = Utility::getIdForUserName($jsonObject->username);

        $link = mysql_connect(Constants::$hostname, Constants::$username, Constants::$password) or die('Cannot connect to the DB');
        mysql_select_db(Constants::$database, $link) or die('Cannot select the DB');

        $insertResult = mysql_query("INSERT INTO answersnumbers VALUES (default, '$userid',
                                                                        $jsonObject->targetNumber, '$jsonObject->numbers',
                                                                        '$jsonObject->operationLog', '$jsonObject->remainingTime',
                                                                        '$jsonObject->points', $jsonObject->userresult, NOW())");

        mysql_close($link);

        if(!$insertResult)
            Utility::prepareResponse(Constants::$ERROR_WHEN_INSERTING_GAME_RESULT);
        else
            Utility::prepareResponse(Constants::$SUCCESS_GAME_RESULT_INSERTED);

    }

}


$number = new Number();
$number->performAction();




