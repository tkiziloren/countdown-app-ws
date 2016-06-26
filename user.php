<?php
/**
 * Created by JetBrains PhpStorm.
 * User: tkiziloren
 * Date: 31.05.2012
 * Time: 10:20
 * To change this template use File | Settings | File Templates.
 */
require_once("Constants.php");
require_once("Utility.php");
require_once("Rest.php");
require_once("ArrayToXml.php");
@require_once "Mail.php";

class User extends Rest{
    protected function execute(){

        if($this->action== Constants::$ACTION_LOGIN_USER)
            Utility::loginUser($this->requestObject);

        if($this->action == Constants::$ACTION_SIGNUP_USER)
            Utility::signupUser($this->requestObject);

        if($this->action== Constants::$ACTION_REMIND_PASSWORD)
            $this->remindPassword($this->requestObject);

        if($this->action== Constants::$ACTION_UPDATE_USER)
            $this->updateUser($this->requestObject);


    }
    protected function validateActionParameters()
    {
        if ($this->requestObject->username == null)
            self::prepareResponse(Constants::$ERROR_INVALID_JSON);

        if($this->action == Constants::$ACTION_LOGIN_USER)
            if ($this->requestObject->password == null)
                Utility::prepareResponse(Constants::$ERROR_INVALID_JSON);

        if($this->action  == Constants::$ACTION_SIGNUP_USER)
            if ($this->requestObject->password == null || $this->requestObject->email == null)
                Utility::prepareResponse(Constants::$ERROR_INVALID_JSON);


        if($this->action == Constants::$ACTION_REMIND_PASSWORD && $this->requestObject->email == null)
            Utility::checkUserExists($this->requestObject);

        if($this->action == Constants::$ACTION_UPDATE_USER)
            Utility::checkUserExists($this->requestObject);

    }

    protected function remindPassword($userObject){

        if($userObject->username == null && $userObject->email == null)
            Utility::prepareResponse(Constants::$ERROR_INVALID_JSON);

        $userModel = new UserModel();
        $userModel->setUsername($userObject->username);
        $userModel->setEmail($userObject->email);

        $userModel = Utility::retrieveUser($userModel);

        if($userModel != null && $userModel->getId() != null){

            $userModel->setPassword(Utility::generatePassword(6));
            $updateResult = Utility::updateUser($userModel);

            if($updateResult == true && $this->sendEmail($userModel) == true)
                Utility::prepareResponse(Constants::$RESULT_REMIND_PASSWORD_SUCCESS);
            else
                Utility::prepareResponse(Constants::$RESULT_REMIND_PASSWORD_FAIL);

        }

        Utility::prepareResponse(Constants::$RESULT_REMIND_PASSWORD_FAIL);
    }


    protected function updateUser($userObject){

        if($userObject->username == null && $userObject->email == null)
            Utility::prepareResponse(Constants::$ERROR_INVALID_JSON);

        $userModel = new UserModel();
        $userModel->setUsername($userObject->username);

        $userModel = Utility::retrieveUser($userModel);

        if($userModel != null && $userModel->getId() != null){

            if($userObject->email != null)
                $userModel->setEmail($userObject->email);

            if($userObject->password != null)
                $userModel->setPassword($userObject->password);

            $updateResult = Utility::updateUser($userModel);

            if($updateResult == true)
                Utility::prepareResponse(Constants::$RESULT_UPDATE_USER_SUCCESS);
            else
                Utility::prepareResponse(Constants::$RESULT_UPDATE_USER_FAIL);

        }

        Utility::prepareResponse(Constants::$RESULT_REMIND_PASSWORD_FAIL);
    }
    protected function sendEmail(UserModel $userModel)
    {
        $subject = "Bir Kelime Bir İşlem Uygulaması, Yeni Şifreniz";
        $body = "Merhaba <strong>" . $userModel->getUsername() . "</strong>. <br/>Yeni şifren: <strong>" . $userModel->password . "</strong><br/><br/>İyi eğlenceler.";

        $from = "<bir.kelime.islem@gmail.com>";
        $to = "<" . $userModel->getEmail() .">";
        $host = "ssl://smtp.gmail.com";
        $port = "465";
        $username = "bir.kelime.islem@gmail.com";
        $password = "plsqldvlpr";

        $headers = array ('From' => $from,'To' => $to, 'Subject' => $subject, 'Content-type' => 'text/html');

        $smtp = @Mail::factory('smtp', array ('host' => $host, 'port' => $port, 'auth' => true, 'username' => $username, 'password' => $password));
        $mail = @$smtp->send($to, $headers, $body);

        if (@PEAR::isError($mail))
            return false;
        else
            return true;
    }
}

$user = new User();
$user->performAction();

