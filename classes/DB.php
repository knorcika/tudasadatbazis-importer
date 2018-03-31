<?php
include BASE_DIR . "/config.php";

class DB
{
    private $connection;
    private $result;
    private $error = false;

    public function __construct()
    {
        global $config;
        $tns = "(DESCRIPTION =(ADDRESS_LIST =(ADDRESS = (PROTOCOL = TCP)(HOST = " . $config["db_host"] . ")(PORT = " . $config["db_port"] . ")))(CONNECT_DATA =(SID = " . $config["db_sid"] . ")))";

        $this->connection = oci_connect($config["db_username"], $config["db_password"], $tns, 'UTF8');
        if (!$this->connection) {
            echo "Connection to database failed!";
            die();
        }
    }

    public function __destruct()
    {
        oci_close($this->connection);
    }

    public function query($sql)
    {
        try {
            $this->result = oci_parse($this->connection, $sql);
            oci_execute($this->result);
            $this->error = false;
        } catch (Exception $e) {
            $this->error = true;
        }
        return $this;
    }

    public function isError()
    {
        return $this->error;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function getFetchedResult()
    {
        $arr = Array();
        while ($row = oci_fetch_array($this->result, OCI_ASSOC + OCI_RETURN_NULLS)) {
            $arrRow = Array();
            foreach ($row as $key => $val) {
                $arrRow[$key] = $val;
            }
            array_push($arr, $arrRow);
        }
        return $arr;
    }
}