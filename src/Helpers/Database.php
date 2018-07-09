<?php
/**
 * Created by PhpStorm.
 * User: eche
 * Date: 6/29/18
 * Time: 12:34 PM
 */

namespace API\Helpers;

class Database
{
    private $config; // holds the config file to be imported.

    public $conn; // holds the PDO connection.

    function __construct()
    {
         $this->config = include_once __DIR__ . '/config.php';

        // Attempt to connect to the database.
        try {
            $this->conn = new \PDO($this->config['dsn'], $this->config['username'], $this->config['password']);
            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        }
        catch (\Exception $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
