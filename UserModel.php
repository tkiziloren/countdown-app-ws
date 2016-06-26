<?php
/**
 * Created by JetBrains PhpStorm.
 * User: tkiziloren
 * Date: 13.01.2013
 * Time: 00:01
 * To change this template use File | Settings | File Templates.
 */
class UserModel
{

    private $tableName = "users";

    public $username;
    public $email;
    public $id;
    public $password;

    function __construct()
    {
        $this->username = null;
        $this->email = null;
        $this->id = null;
        $this->password = null;
    }



    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSelectQuery(){

        if($this->isEmptyObject())
            return null;

        $query = "select * from " . $this->tableName . $this->getWhereStatement();
        return $query;
    }

    public function getUpdateQuery(){
        if($this->isEmptyObject())
            return null;

        $query = "update " . $this->tableName . $this->getSetStatment() . $this->getWhereStatement();
        return $query;
    }

    public function getWhereStatement(){

        $array = json_decode(json_encode($this), true);
        $where = " where ";

        $useAnd = false;
        foreach($array as $key => $value){

            if($key == "tableName")
                continue;

            if($key != "id" && $key != "username")
                continue;

            if($this->{$key} != null){
                $where .= $useAnd ? " and " : "";
                $where .= $key . "='" . $this->{$key} . "'";
                $useAnd = true;
            }

        }
        return $where;
    }

    public function getSetStatment(){

        $array = json_decode(json_encode($this), true);
        $set = " set ";

        $useComma = false;
        foreach($array as $key => $value){

            if($key == "tableName" || $key=="id" || $key == "username")
                continue;

            if($this->{$key} != null){
                $set .= $useComma ? ", " : "";
                $set .= $key . "='" . $this->{$key} . "'";
                $useComma = true;
            }

        }
        return $set;
    }


    public function isEmptyObject(){
        return $this->tableName == null || ($this->getUsername() == null && $this->getId() == null && $this->getEmail() == null);

    }

}
