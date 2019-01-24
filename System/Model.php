<?php

namespace System;

use PDO;
use App\Config\Database;

/**
 * Base model
 * extra functions
 *
 */
abstract class Model
{

    /**
     * Get the PDO database connection
     *
     * @return mixed
     */
    protected static function getDB()
    {
        static $db = null;

        if ($db === null) {
            $dsn = 'mysql:host=' . Database::DB_HOST . ';dbname=' . Database::DB_NAME . ';charset=utf8';
            $db = new PDO($dsn, Database::DB_USER, Database::DB_PASSWORD);

            // Throw an Exception when an error occurs
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return $db;
    }

    function random_username($string) {
        $pattern = "";
        $name = array(0 => $string, 1 => $pattern);
        $firstname = $name[0];
        $lastname = $name[1];
        $firstname = strtolower($firstname);
        $lastname = strtolower($lastname);
        $nrRand = rand(0, 999);
        return  $firstname . $lastname . $nrRand;
    }
}
