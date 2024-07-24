<?php

class DatabaseService
{
    // private static $instance;

    // public static function getInstance(){
    //     if(self::$instance==null){
    //         self::$instance = new DatabaseService();
    //     }
    //     return self::$instance;
    // }

    function __construct(){}

    function checkUser($email){
        // $this->open('services/database.db');
        $result =  $this->query('SELECT * from "admin_users" where email="'.$email.'"');
        while ($row = $result->fetchArray()) {
            return true;
        }
        return false;
    }
}

    
?>