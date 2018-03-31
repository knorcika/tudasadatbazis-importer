<?php
define('BASE_DIR', dirname(__FILE__));
require_once "classes/DB.php";
$db = new DB();

include "./view/index.html";

if (isset($_POST['submit'])) {
    $table = explode('.', $_FILES['table']['name'])[0];
    $res = $db->query("SELECT * FROM $table");
    if (!$res->isError()) {
        echo 'Table is not found!<br>';
        die();
    }
    $fields = Array();
    echo "Tábla:" . $table . '<br>';
    $row = 1;
    $handle = fopen($_FILES['table']['tmp_name'], "r");
    if ($handle !== false) {
        while (($data = fgetcsv($handle, 0, ",")) !== false) {
            if ($row === 1) {
                $fields = $data;
            } else {
                $values = Array();
                foreach ($data as $val) {
                    if (is_numeric($val)) {
                        array_push($values, $val);
                    } else {
                        array_push($values, "'" . $val . "'");
                    }
                }
                $sql = 'INSERT INTO ' . $table . ' (' . implode(',', $fields) . ') VALUES (' . implode(',', $values) . ')';
                if ($db->query($sql)->isError()) {
                    echo 'Row ' . ($row - 1) . '. inserted. <br>';
                } else {
                    echo 'Row ' . ($row - 1) . '. insert error. <br>' . $sql . '<br>';
                }
            }
            $row++;
        }
        fclose($handle);
    }
}

if (isset($_POST['submit2'])) {
    $query = $db->query($_POST['query']);
    if ($query->isError()) {
        echo 'SQL executed.<br>';
    } else {
        echo 'SQL execute failed.<br>';
    }

}