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
require_once("ArrayToXml.php");

class Word extends Rest{

    protected $result = null;

    protected function execute(){
        if($this->action  == Constants::$ACTION_CHECK_FOR_WORD)
            $this->checkForWordAndSaveResults();
        else if($this->action  == Constants::$ACTION_SUGGEST_WORD)
            $this->suggestWord();
        else
            Utility::send404Response();

    }

    protected function validateActionParameters(){

        if($this->action == Constants::$ACTION_CHECK_FOR_WORD)
        {
            if ($this->requestObject->username == null || $this->requestObject->answer == null ||
                $this->requestObject->question == null || $this->requestObject->remainingTime == null)
            {
                Utility::prepareResponse(Constants::$ERROR_INVALID_JSON);
                return;
            }
        }
        else if($this->action  == Constants::$ACTION_SUGGEST_WORD){
            if ($this->requestObject->word == null || $this->requestObject->username == null){
                Utility::prepareResponse(Constants::$ERROR_INVALID_JSON);
                return;
            }
        }
    }

    protected function calculateAndInsertPoints(){

        if($this->checkForWord($this->requestObject->answer))
        {
            $points = $this->calculatePoints($this->requestObject->answer, $this->requestObject->remainingTime);
            $insertResult = $this->insertGameResult($this->requestObject,$points);
            $this->resultCode = Constants::$RESULT_WORD_IS_CORRECT;
        }
        else
        {
            $points = 0;
            $insertResult = $this->insertGameResult($this->requestObject,$points);
            $this->resultCode = Constants::$RESULT_WORD_IS_INCORRECT;
        }

        if($insertResult == Constants::$ERROR_WHEN_INSERTING_GAME_RESULT)
            $this->resultCode = Constants::$ERROR_WHEN_INSERTING_GAME_RESULT;
    }

    protected function checkForWord($answer){

        $link = mysql_connect(Constants::$hostname, Constants::$username, Constants::$password) or die('Cannot connect to the DB');

        Utility::setCharset();

        mysql_select_db(Constants::$database, $link) or die('Cannot select the DB');

        $answer = str_replace("?", "_", $answer);

        $query = "SELECT word as bulunanKelime FROM words where word like '$answer'";
        $result = mysql_query($query, $link) or die('Errant query:  '.$query);

        if (mysql_num_rows($result) == 0){
            mysql_close($link);
            return false;
        }

        $row = mysql_fetch_assoc($result);
        $this->result = $row['bulunanKelime'];
        return true;
    }




    protected function suggestWord()
    {
        Utility::insertSuggestedWord($this->requestObject);
    }

    protected function calculatePoints($answer, $remainingTime){

        if(strlen($answer) < 3)
            return 0;

        $jokerli = strpos($answer,"?");
        $puan = (mb_strlen($answer) * 10);
        $katsayi = ($jokerli === false) ? 1 : 0.5;

        $puan = $puan + $katsayi * $remainingTime;
        return round($puan);

    }

    protected function insertGameResult($jsonObject, $points){

        $userid = Utility::getIdForUserName($jsonObject->username);
        $sortedQuestion = Utility::sortCharsInString($jsonObject->question);

        $link = mysql_connect(Constants::$hostname, Constants::$username, Constants::$password) or die('Cannot connect to the DB');
        mysql_select_db(Constants::$database, $link) or die('Cannot select the DB');


        Utility::setCharset();
        $insertResult = mysql_query("INSERT INTO answerswords VALUES (default, '$userid',
                                                                         '$sortedQuestion', '$jsonObject->answer',
                                                                        '$jsonObject->remainingTime', '$points', NOW())");

        mysql_close($link);

        if(!$insertResult)
            return Constants::$ERROR_WHEN_INSERTING_GAME_RESULT;

        return Constants::$SUCCESS_GAME_RESULT_INSERTED;
    }

    protected function checkForWordAndSaveResults(){
        $this->calculateAndInsertPoints($this->requestObject);

        if($this->result != null)
        {
            $data = array();
            $data[] = array('wordFoundInServer' =>$this->result);
            $this->responseObject = $data;
        }
    }

}

$word = new Word();
$word->performAction();

