<?php
/**
 * Created by JetBrains PhpStorm.
 * User: tkiziloren
 * Date: 29.06.2012
 * Time: 14:30
 * To change this template use File | Settings | File Templates.
 */

require_once("UserModel.php");
class Utility
{
    /*
          Cihazlardan gelen işlem oyun sonuçlarının doğru formatta gönderilip
          gönderilmediğinin validasyonunu yapar
    */
    public static function validateParametersForOperationResult($jsonObject){

        if($jsonObject == null)
            self::send404Response();

        if ($jsonObject->username == null || $jsonObject->targetNumber == null || $jsonObject->numbers == null ||
            $jsonObject->operationLog == null || $jsonObject->remainingTime == null || $jsonObject->points == null)
        {
            self::prepareResponse(Constants::$ERROR_INVALID_JSON);
        }
        //self::printOperationGameResult($jsonResult);
        return $jsonObject;
    }

    public static function printOperationGameResult($operationGameResult){
        echo $operationGameResult->username;
        echo $operationGameResult->targetnumber;
        echo $operationGameResult->numbers;
        echo $operationGameResult->operationlog;
        echo $operationGameResult->remainingtime;
        echo $operationGameResult->points;
    }

    /*
     Bir $key ile 128-bit AES algoritmasıyla şifrelenmiş metni decrypt eder
    */
    public static function decryptText($cipher, $key)
    {
        $cipher=str_replace(" ", "+", $cipher);

        $Initial_Vector = '';

        for($i=0;$i<16;$i++)
            $Initial_Vector .= "\0";

        $plaintext =
            mcrypt_decrypt(
                MCRYPT_RIJNDAEL_128,
                $key,
                base64_decode($cipher),
                MCRYPT_MODE_CBC,
                $Initial_Vector
            );

        $padChar = ord(substr($plaintext, -1));
        $resultWithoutPadding = substr($plaintext, 0, strlen($plaintext) - $padChar);
        return $resultWithoutPadding;
    }

    public static function send404Response(){
        header('HTTP/1.0 404 Not Found');
        echo "<h1>404 Not Found</h1>";
        echo "The page that you have requested could not be found.";
        die;
    }


    public static function prepareResponse($responseCode){
        header('Success', true, 200);
        self::prepareFullResponse($responseCode, null);
        die;
    }

    public static function prepareFullResponse($responseCode, $responseDetails){

        $data = array();
        $data[] = array('responseCode' =>$responseCode);

        if($responseDetails != null)
            $data[] = array('responseDetails' => $responseDetails);

        if(Constants::$MESSAGE_STYLE == Constants::$MESSAGE_STYLE_JSON){
            echo json_encode($data);
        }
        else if(Constants::$MESSAGE_STYLE == Constants::$MESSAGE_STYLE_XML){
            echo ArrayToXML::toXML($data, "response");
        }
    }

    public static function getURLParameter($urlParameter){

        $result = $_POST[$urlParameter];

        // debug mod ise hem get hem post kobul et
        if(!$result && Constants::$DEBUG_MODE_ACCEPT_BOTH_GET_AND_POST)
            $result = $_GET[$urlParameter];

        return $result;
    }

    public static function getIdForUserName($username){
        $link = mysql_connect(Constants::$hostname, Constants::$username, Constants::$password) or die('Cannot connect to the DB');
        mysql_select_db(Constants::$database, $link) or die('Cannot select the DB');

        $query = "SELECT id FROM users where username='$username'";
        $results = mysql_query($query, $link) or die('Errant query:  '.$query);

        $userid = null;
        if($results){
            $row = mysql_fetch_assoc($results);
            $userid = $row['id'];
        }
        mysql_close($link);
        return $userid;

    }


    public static function getUserFor($username){
        $link = mysql_connect(Constants::$hostname, Constants::$username, Constants::$password) or die('Cannot connect to the DB');
        mysql_select_db(Constants::$database, $link) or die('Cannot select the DB');

        $query = "SELECT id FROM users where username='$username'";
        $results = mysql_query($query, $link) or die('Errant query:  '.$query);

        $userid = null;
        if($results){
            $row = mysql_fetch_assoc($results);
            $userid = $row['id'];
        }
        mysql_close($link);
        return $userid;

    }

    public static function getUserForEmail($email){
        $link = mysql_connect(Constants::$hostname, Constants::$username, Constants::$password) or die('Cannot connect to the DB');
        mysql_select_db(Constants::$database, $link) or die('Cannot select the DB');

        $query = "SELECT username FROM users where email='$email'";
        $results = mysql_query($query, $link) or die('Errant query:  '.$query);

        $username= null;
        if($results){
            $row = mysql_fetch_assoc($results);
            $username = $row['username'];
        }
        mysql_close($link);
        return $username;

    }

    public static function sortCharsInString($str)
    {
        $splitted = preg_split('/(?<!^)(?!$)/u', $str);
        sort($splitted);
        $result = implode('',$splitted);
        return $result;
    }

    public static function setCharset(){
        mb_internal_encoding('UTF-8');
        mysql_query('SET NAMES UTF8');
        mysql_query("SET CHARACTER SET utf8");
        mysql_query("SET COLLATION_CONNECTION = 'utf8_turkish_ci'");
    }

    public static function checkUserExists($jsonObject){

        if($jsonObject == null)
            self::send404Response();

        if ($jsonObject->username == null)
            self::prepareResponse(Constants::$ERROR_INVALID_JSON);

        if(!Utility::isUserExists($jsonObject->username))
            self::prepareResponse(Constants::$RESULT_USERNAME_DOESNT_EXIST);
    }

    public static function isUserExists($username){

        if($username == null)
            return false;

        $link = mysql_connect(Constants::$hostname, Constants::$username, Constants::$password) or die('Cannot connect to the DB');
        mysql_select_db(Constants::$database, $link) or die('Cannot select the DB');

        $query = "SELECT count(id) as num FROM users where username='$username'";
        $result = mysql_query($query, $link) or die('Errant query:  '.$query);
        $row = mysql_fetch_assoc($result);
        $numUsers = $row['num'];

        mysql_close($link);

        if($numUsers > 0)
            return true;

        return false;

    }

    public static function loginUser($jsonObject){

        if($jsonObject == null)
            self::send404Response();

        if ($jsonObject->username == null)
            self::prepareResponse(Constants::$ERROR_INVALID_JSON);

        $link = mysql_connect(Constants::$hostname, Constants::$username, Constants::$password) or die('Cannot connect to the DB');
        mysql_select_db(Constants::$database, $link) or die('Cannot select the DB');

        $query = "SELECT count(id) as num FROM users where username='$jsonObject->username' and password='$jsonObject->password'";
        $result = mysql_query($query, $link) or die('Errant query:  '.$query);
        $row = mysql_fetch_assoc($result);
        $numUsers = $row['num'];

        mysql_close($link);

        if($numUsers > 0)
            self::prepareResponse(Constants::$RESULT_LOGIN_SUCCESS);
        else
            self::prepareResponse(Constants::$RESULT_LOGIN_FAIL);


    }


    public static function signupUser($userObject){

        if($userObject == null)
            Utility::send404Response();

        if($userObject->deviceid == null || $userObject->username == null || $userObject->email == null || $userObject->password == null)
            Utility::prepareResponse(Constants::$ERROR_INVALID_JSON);

        if(Utility::isUserExists($userObject->username))
            Utility::prepareResponse(Constants::$RESULT_USERNAME_EXISTS);

        $link = mysql_connect(Constants::$hostname, Constants::$username, Constants::$password) or die('Cannot connect to the DB');
        mysql_select_db(Constants::$database, $link) or die('Cannot select the DB');

        $insertResult = mysql_query("INSERT INTO users VALUES (default, '$userObject->username', '$userObject->email', '$userObject->deviceid', NOW(), '$userObject->password')");

        mysql_close($link);

        if(!$insertResult)
            Utility::prepareResponse(Constants::$RESULT_SIGNUP_FAIL);
        else
            Utility::prepareResponse(Constants::$RESULT_SIGNUP_SUCCESS);

    }



    public static function retrieveUser(UserModel $userModel)
    {

        if ($userModel == null)
            return null;

        $link = mysql_connect(Constants::$hostname, Constants::$username, Constants::$password) or die('Cannot connect to the DB');
        mysql_select_db(Constants::$database, $link) or die('Cannot select the DB');

        $query = $userModel->getSelectQuery();
        $results = mysql_query($query, $link) or die('Errant query:  '.$query);


        if($results){
            $row = mysql_fetch_assoc($results);
            $userModel->setEmail($row['email']);
            $userModel->setId($row['id']);
            $userModel->setUsername($row['username']);
        }
        mysql_close($link);
        return $userModel;
    }

    public static function updateUser(UserModel $userModel){

        if ($userModel == null)
            return null;

        $link = mysql_connect(Constants::$hostname, Constants::$username, Constants::$password) or die('Cannot connect to the DB');
        mysql_select_db(Constants::$database, $link) or die('Cannot select the DB');

        $updateResult = mysql_query($userModel->getUpdateQuery());
        mysql_close($link);
        return $updateResult;
    }

    public static function insertSuggestedWord($requestObject){

        if($requestObject == null)
            Utility::send404Response();

        if($requestObject->username == null || $requestObject->word == null)
            Utility::prepareResponse(Constants::$ERROR_INVALID_JSON);

        $suggestedWord = Utility::toUpperTR($requestObject->word);

        if(Utility::isWordExistsWordsInTable($suggestedWord) || Utility::isWordExistsInSuggestedWordTable($suggestedWord))
            Utility::prepareResponse(Constants::$RESULT_SUGGEST_WORD_EXISTS);

        $link = mysql_connect(Constants::$hostname, Constants::$username, Constants::$password) or die('Cannot connect to the DB');
        mysql_select_db(Constants::$database, $link) or die('Cannot select the DB');


        Utility::setCharset();
        $insertResult = mysql_query("INSERT INTO suggestedwords VALUES (default, '$suggestedWord', '$requestObject->username', NOW(), 0)");

        mysql_close($link);

        if(!$insertResult)
            Utility::prepareResponse(Constants::$RESULT_SUGGEST_WORD_FAIL);
        else
            Utility::prepareResponse(Constants::$RESULT_SUGGEST_WORD_SUCCESS);

    }

    public static function isWordExistsWordsInTable($kelime){

        $link = mysql_connect(Constants::$hostname, Constants::$username, Constants::$password) or die('Cannot connect to the DB');

        Utility::setCharset();

        mysql_select_db(Constants::$database, $link) or die('Cannot select the DB');


        $query = "SELECT * FROM words where word = '$kelime'";
        $result = mysql_query($query, $link) or die('Errant query:  '.$query);

        if (mysql_num_rows($result) == 0){
            mysql_close($link);
            return false;
        }
        return true;
    }

    public static function isWordExistsInSuggestedWordTable($kelime){

        $link = mysql_connect(Constants::$hostname, Constants::$username, Constants::$password) or die('Cannot connect to the DB');

        Utility::setCharset();

        mysql_select_db(Constants::$database, $link) or die('Cannot select the DB');

        $query = "SELECT * FROM suggestedwords where word = '$kelime'";
        $result = mysql_query($query, $link) or die('Errant query:  '.$query);

        if (mysql_num_rows($result) == 0){
            mysql_close($link);
            return false;
        }
        return true;
    }


    public static function generatePassword( $length ) {
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        return substr(str_shuffle($chars),0,$length);
    }

    public static function toUpperTR($arg) {
        return strtoupper (str_replace(array ('ı', 'i', 'ğ', 'ü', 'ş', 'ö', 'ç' ),array ('I', 'İ', 'Ğ', 'Ü', 'Ş', 'Ö', 'Ç' ),$arg));
    }


}



