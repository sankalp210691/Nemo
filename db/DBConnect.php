<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DBConnect
 *
 * @author Sankalp
 */
class DBConnect {

    private $con;
    private $connection_type;
    private $server = "localhost";
    private $username = "root";
    private $password = "HuckHazard123";
    private $database;

    function DBConnect($connection_type, $database, $server, $username, $password) {
        if ($connection_type == null || strlen($connection_type) == 0) {
            echo "Invalid connection type.";
            return;
        }
        if (($database == null || strlen($database) == 0) && $connection_type == "mysqli") {
            echo "Invalid database.";
            return;
        }

        if ($server != null && strlen($server) != 0)
            $this->server = $server;
        if ($username != null && strlen($username) != 0)
            $this->username = $username;
        if ($password != null && strlen($password) != 0)
            $this->password = $password;
        $this->connection_type = $connection_type;
        $this->database = $database;

        if ($this->connection_type == "mysql") {
            $this->con = mysql_connect($this->server, $this->username, $this->password);
            if ($database != null && strlen($database) != 0)
                mysql_select_db($this->database, $this->con);
        }else if ($this->connection_type == "mysqli") {
            $this->con = new mysqli($this->server, $this->username, $this->password, $this->database);
        }
    }

    function mysql_connect_close() {
        mysql_close($this->con);
    }

    function mysqli_connect_close() {
        $this->con->close();
    }

    function getCon() {
        return $this->con;
    }

    function getDatabase() {
        return $this->database;
    }
}

?>
