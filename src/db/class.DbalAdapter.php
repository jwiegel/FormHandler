<?php

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception as DbalException;
use Doctrine\DBAL\Schema\Index\IndexType;
use Doctrine\DBAL\Schema\Name\UnqualifiedName;
use Doctrine\DBAL\Schema\Table;

/**
 * class DbalAdapter
 *
 * DatabaseAdapter on top of a Doctrine DBAL connection.
 * Works with every database DBAL supports (MySQL, MariaDB, PostgreSQL, SQLite, SQL Server, ...).
 *
 * @package FormHandler
 */
class DbalAdapter implements DatabaseAdapter
{
    // the old Yadal database types and the DBAL driver they use
    private const DRIVERS = array(
        'mysql'      => 'mysqli',
        'mysqli'     => 'mysqli',
        'postgresql' => 'pdo_pgsql',
        'postgres'   => 'pdo_pgsql',
        'pgsql'      => 'pdo_pgsql',
        'mssql'      => 'pdo_sqlsrv',
        'sqlite'     => 'pdo_sqlite',
    );

    private $_conn = null;      // Connection: the DBAL connection
    private $_database = null;  // string: database name (or file for sqlite) for connect()
    private $_driver = null;    // string: DBAL driver for connect()
    private $_lastQuery = '';   // string: the last query executed
    private $_error = '';       // string: message of the last error
    private $_errorNo = 0;      // int: code of the last error
    private $_tables = array(); // array: cache of introspected tables

    /**
     * DbalAdapter::__construct()
     *
     * @param Connection|null $conn the DBAL connection to use
     */
    public function __construct($conn = null)
    {
        if (!is_null($conn))
        {
            $this->setConnectionResource($conn);
        }
    }

    /**
     * DbalAdapter::setConnectionResource()
     *
     * @param Connection $conn the DBAL connection to use
     * @return void
     */
    public function setConnectionResource(&$conn)
    {
        if ($conn instanceof Connection)
        {
            $this->_conn = $conn;
            $this->_tables = array();
        }
        else
        {
            throw new FormHandlerException(
                'Error, DbalAdapter needs a Doctrine\DBAL\Connection!'
            );
        }
    }

    /**
     * DbalAdapter::setDatabase()
     *
     * Remember which database connect() should open (used by dbFormHandler::dbInfo())
     *
     * @param string $database the database name, or the file for sqlite
     * @param string $type a Yadal database type (mysql, postgresql, mssql, sqlite) or a DBAL driver name
     * @return bool always true
     * @throws FormHandlerException when the type is not supported
     */
    public function setDatabase($database, $type)
    {
        $type = strtolower($type);
        $drivers = DriverManager::getAvailableDrivers();

        if (isset(self::DRIVERS[$type]))
        {
            $this->_driver = self::DRIVERS[$type];
        }
        else if (in_array($type, $drivers))
        {
            $this->_driver = $type;
        }
        else
        {
            $this->_driver = null;
            throw new FormHandlerException(
                'Error, database type "' . $type . '" not supported!'
            );
        }

        $this->_database = $database;
        return !is_null($this->_driver);
    }

    /**
     * DbalAdapter::isConnected()
     *
     * @return bool
     */
    public function isConnected()
    {
        return !is_null($this->_conn);
    }

    /**
     * DbalAdapter::connect()
     *
     * Open the database given by setDatabase(). With a connection from
     * setConnectionResource() this only checks that the connection works.
     *
     * @param string $host the host to connect to
     * @param string $username the username which should be used to login
     * @param string $password the password which should be used to login
     * @return bool
     */
    public function connect($host = 'localhost', $username = '', $password = '')
    {
        try
        {
            if (!$this->isConnected())
            {
                if (is_null($this->_driver))
                {
                    $this->_error = 'No database given, call setDatabase() first';
                    return false;
                }

                $params = $this->_driver == 'pdo_sqlite'
                    ? array('driver' => $this->_driver, 'path' => $this->_database)
                    : array(
                        'driver'   => $this->_driver,
                        'host'     => $host,
                        'user'     => $username,
                        'password' => $password,
                        'dbname'   => $this->_database
                    );

                $this->_conn = DriverManager::getConnection($params);
            }

            $this->_conn->getServerVersion();
            return true;
        }
        catch (DbalException $e)
        {
            $this->_setError($e);
            return false;
        }
    }

    /**
     * DbalAdapter::close()
     *
     * @return bool
     */
    public function close()
    {
        if ($this->isConnected())
        {
            $this->_conn->close();
        }

        return true;
    }

    /**
     * DbalAdapter::query()
     *
     * @param string $query the query to execute
     * @return DbalResult|false the result or false on an error
     */
    public function query($query)
    {
        $this->_lastQuery = $query;

        try
        {
            $result = $this->_conn->executeQuery($query);
            $rows = $result->columnCount() > 0 ? $result->fetchAllAssociative() : array();
            $result->free();

            return new DbalResult($rows);
        }
        catch (DbalException $e)
        {
            $this->_setError($e);
            return false;
        }
    }

    /**
     * DbalAdapter::getInsertId()
     *
     * @return int|string the id of the last inserted record
     */
    public function getInsertId()
    {
        try
        {
            return $this->_conn->lastInsertId();
        }
        catch (DbalException $e)
        {
            $this->_setError($e);
            return 0;
        }
    }

    /**
     * DbalAdapter::result()
     *
     * @param DbalResult $sql the result to read from
     * @param int $row the row to read
     * @param string|null $field the field to read, or null for the first field
     * @return mixed the value or false when it does not exist
     */
    public function result($sql, $row = 0, $field = null)
    {
        $data = false;
        for ($i = 0; $i <= $row; $i++)
        {
            $data = $this->getRecord($sql);
        }

        if (!is_array($data) || $data === array())
        {
            return false;
        }

        if (is_null($field))
        {
            return reset($data);
        }

        foreach ($data as $column => $value)
        {
            if (strtolower($column) == strtolower($field))
            {
                return $value;
            }
        }

        return false;
    }

    /**
     * DbalAdapter::recordCount()
     *
     * @param DbalResult $sql
     * @return int
     */
    public function recordCount($sql)
    {
        return $sql instanceof DbalResult ? $sql->count() : 0;
    }

    /**
     * DbalAdapter::getRecord()
     *
     * @param DbalResult $sql
     * @return array|false the next record in assoc mode or false when there are no records left
     */
    public function getRecord($sql)
    {
        return $sql instanceof DbalResult ? $sql->fetch() : false;
    }

    /**
     * DbalAdapter::getError()
     *
     * @return string
     */
    public function getError()
    {
        return $this->_error;
    }

    /**
     * DbalAdapter::getErrorNo()
     *
     * @return int
     */
    public function getErrorNo()
    {
        return $this->_errorNo;
    }

    /**
     * DbalAdapter::getLastQuery()
     *
     * @return string
     */
    public function getLastQuery()
    {
        return $this->_lastQuery;
    }

    /**
     * DbalAdapter::quote()
     *
     * Quote a table or field name, also when it contains a dot (like table.name)
     *
     * @param string $name
     * @return string
     */
    public function quote($name)
    {
        $parts = explode('.', $name);
        foreach ($parts as $i => $part)
        {
            $parts[$i] = $this->_conn->quoteSingleIdentifier($part);
        }

        return implode('.', $parts);
    }

    /**
     * DbalAdapter::quoteNumbers()
     *
     * @return bool
     */
    public function quoteNumbers()
    {
        return true;
    }

    /**
     * DbalAdapter::escapeString()
     *
     * @param string $string
     * @return string the escaped string without the surrounding quotes
     */
    public function escapeString($string)
    {
        return substr($this->_conn->quote((string) $string), 1, -1);
    }

    /**
     * DbalAdapter::dbDate()
     *
     * @param string $y
     * @param string $m
     * @param string $d
     * @return string
     */
    public function dbDate($y, $m, $d)
    {
        return "'$y-$m-$d'";
    }

    /**
     * DbalAdapter::getFieldNames()
     *
     * @param string $table
     * @return array
     */
    public function getFieldNames($table)
    {
        return array_keys($this->getFieldTypes($table));
    }

    /**
     * DbalAdapter::getFieldTypes()
     *
     * @param string $table
     * @return array field name => array(DBAL type name, length, default)
     */
    public function getFieldTypes($table)
    {
        $result = array();
        foreach ($this->_getTable($table)->getColumns() as $column)
        {
            $result[$this->_name($column->getObjectName())] = array(
                $column->getTypeName(),
                $column->getLength(),
                $column->getDefault()
            );
        }

        return $result;
    }

    /**
     * DbalAdapter::getNotNullFields()
     *
     * @param string $table
     * @return array
     */
    public function getNotNullFields($table)
    {
        $result = array();
        foreach ($this->_getTable($table)->getColumns() as $column)
        {
            if ($column->getNotnull())
            {
                $result[] = $this->_name($column->getObjectName());
            }
        }

        return $result;
    }

    /**
     * DbalAdapter::getPrKeys()
     *
     * @param string $table
     * @return array
     */
    public function getPrKeys($table)
    {
        $primaryKey = $this->_getTable($table)->getPrimaryKeyConstraint();
        if (is_null($primaryKey))
        {
            return array();
        }

        return array_map(array($this, '_name'), $primaryKey->getColumnNames());
    }

    /**
     * DbalAdapter::getUniqueFields()
     *
     * @param string $table
     * @return array index name => array of field names, for every unique index (including the primary key)
     */
    public function getUniqueFields($table)
    {
        // the primary key counts as unique index (as it did with Yadal)
        $result = array();
        $keys = $this->getPrKeys($table);
        if (sizeof($keys) > 0)
        {
            $result['PRIMARY'] = $keys;
        }

        foreach ($this->_getTable($table)->getIndexes() as $index)
        {
            if ($index->getType() !== IndexType::UNIQUE)
            {
                continue;
            }

            $fields = array();
            foreach ($index->getIndexedColumns() as $column)
            {
                $fields[] = $this->_name($column->getColumnName());
            }

            // some platforms report the primary key also as unique index
            if ($fields !== $keys)
            {
                $result[$this->_name($index->getObjectName())] = $fields;
            }
        }

        return $result;
    }

    /**
     * DbalAdapter::_getTable()
     *
     * @param string $table
     * @return Table the introspected table (cached)
     */
    private function _getTable($table)
    {
        $t = strtolower($table);
        if (!isset($this->_tables[$t]))
        {
            $this->_tables[$t] = $this->_conn->createSchemaManager()->introspectTableByUnquotedName($table);
        }

        return $this->_tables[$t];
    }

    /**
     * DbalAdapter::_name()
     *
     * @param mixed $name a DBAL name object
     * @return string the plain name
     */
    private function _name($name)
    {
        return $name instanceof UnqualifiedName ? $name->getIdentifier()->getValue() : $name->toString();
    }

    /**
     * DbalAdapter::_setError()
     *
     * @param DbalException $e
     * @return void
     */
    private function _setError($e)
    {
        $this->_error = $e->getMessage();
        $this->_errorNo = (int) $e->getCode();
    }
}
