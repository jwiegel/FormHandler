<?php

/**
 * interface DatabaseAdapter
 *
 * Everything dbFormHandler and the db* fields need from a database layer.
 * Implemented by Yadal (legacy) and DbalAdapter (Doctrine DBAL).
 *
 * @package FormHandler
 */
interface DatabaseAdapter
{
    // connection
    public function setConnectionResource(&$conn);
    public function isConnected();
    public function connect($host = 'localhost', $username = '', $password = '');
    public function close();

    // queries and results
    public function query($query);
    public function getInsertId();
    public function result($sql, $row = 0, $field = null);
    public function recordCount($sql);
    public function getRecord($sql);
    public function getError();
    public function getErrorNo();
    public function getLastQuery();

    // building sql
    public function quote($name);
    public function quoteNumbers();
    public function escapeString($string);
    public function dbDate($y, $m, $d);

    // table information
    public function getFieldNames($table);
    public function getFieldTypes($table);
    public function getNotNullFields($table);
    public function getPrKeys($table);
    public function getUniqueFields($table);
}
