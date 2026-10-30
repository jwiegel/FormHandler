<?php

declare(strict_types=1);

/**
 * interface DatabaseAdapter
 *
 * Everything dbFormHandler and the db* fields need from a database layer.
 * Implemented by DbalAdapter (Doctrine DBAL); mock it in tests.
 *
 * @package FormHandler
 */
interface DatabaseAdapter
{
    // connection

    /**
     * @param mixed $conn the connection of the database layer (a Doctrine DBAL Connection for DbalAdapter)
     * @return void
     */
    public function setConnectionResource(&$conn);

    /**
     * @return bool
     */
    public function isConnected();

    /**
     * @param string $host the host to connect to
     * @param string $username the username which should be used to login
     * @param string $password the password which should be used to login
     * @return bool
     */
    public function connect($host = 'localhost', $username = '', $password = '');

    /**
     * @return bool
     */
    public function close();

    // queries and results

    /**
     * @param string $query the query to execute
     * @return mixed the result (passed to recordCount(), getRecord() and result()) or false on an error
     */
    public function query($query);

    /**
     * @return int|string the id of the last inserted record
     */
    public function getInsertId();

    /**
     * @param mixed $sql a result of query()
     * @param int $row the row to read
     * @param string|null $field the field to read, or null for the first field
     * @return mixed the value or false when it does not exist
     */
    public function result($sql, $row = 0, $field = null);

    /**
     * @param mixed $sql a result of query()
     * @return int
     */
    public function recordCount($sql);

    /**
     * @param mixed $sql a result of query()
     * @return array<string, mixed>|false the next record in assoc mode or false when there are no records left
     */
    public function getRecord($sql);

    /**
     * @return string
     */
    public function getError();

    /**
     * @return int
     */
    public function getErrorNo();

    /**
     * @return string
     */
    public function getLastQuery();

    // building sql

    /**
     * @param string $name a table or column name
     * @return string the quoted name
     */
    public function quote($name);

    /**
     * @return bool should numbers be quoted in queries?
     */
    public function quoteNumbers();

    /**
     * @param string $string
     * @return string the escaped string without the surrounding quotes
     */
    public function escapeString($string);

    /**
     * @param string $y
     * @param string $m
     * @param string $d
     * @return string the date as SQL literal
     */
    public function dbDate($y, $m, $d);

    // table information

    /**
     * @param string $table
     * @return array<int, string> the column names
     */
    public function getFieldNames($table);

    /**
     * @param string $table
     * @return array<string, array<int, mixed>> field name => array(type name, length, default)
     */
    public function getFieldTypes($table);

    /**
     * @param string $table
     * @return array<int, string> the columns which can not be NULL
     */
    public function getNotNullFields($table);

    /**
     * @param string $table
     * @return array<int, string> the columns of the primary key
     */
    public function getPrKeys($table);

    /**
     * @param string $table
     * @return array<string, array<int, string>> index name => field names, for every unique index (including the primary key)
     */
    public function getUniqueFields($table);
}
