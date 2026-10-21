<?php

/**
 * Yadal mail class and the yadal object creator function
 *
 * @package Yadal
 */

define('YADAL_DEFAULT_DB_TYPE', 'mysql');

interface DatabaseAdapter
{
    public function setConnectionResource(&$conn);
    public function isConnected();
    public function connect($host = 'localhost', $username = '', $password = '');
    public function close();
    public function query($query);
    public function getInsertId();
    public function result($sql, $row = 0, $field = 0);
    public function getError();
    public function getErrorNo();
    public function recordCount($sql);
    public function getRecord($sql);
    public function getFieldNames($table);
    public function getLastQuery();
}

/**
 * newYadal()
 *
 * Create a new database object of the correct type
 *
 * @param string $database: the database name to connect to
 * @param string $type: the type of database to use (default MySQL)
 * @return object
 * @access public
 * @author Teye Heimans
 */
function newYadal($database = null, $type = null)
{
    // set the default database type if none is given
    if (is_null($type))
    {
        $type = YADAL_DEFAULT_DB_TYPE;
    }

    switch (strtolower($type))
    {
            // mysql
        case 'mysqli':
            include_once dirname(__FILE__) . '/class.MySQLi.php';
            return new YadalMySQLi($database);

            // mysql
        case 'mysql':
            include_once dirname(__FILE__) . '/class.MySQL.php';
            return new MySQL($database); // @phpstan-ignore-line (Instantiated class MySQL not found.)

            // postgresql
        case 'postgresql':
        case 'postgres':
        case 'pgsql':
            include_once dirname(__FILE__) . '/class.PostgreSQL.php';
            return new PostgreSQL($database); // @phpstan-ignore-line (Instantiated class PostgreSQL not found.)

            // Microsoft SQL server (MSSQL)
        case 'mssql':
            include_once dirname(__FILE__) . '/class.MSSQL.php';
            return new MSSQL($database); // @phpstan-ignore-line (Instantiated class MSSQL not found.)

            // Microsoft access database (Windows only)
        case 'access':
            include_once dirname(__FILE__) . '/class.Access.php';
            return new Access($database); // @phpstan-ignore-line (Instantiated class Access not found.)

            // ODBC
            /* NOT SUPPORTED YET
        case 'odbc':
            include_once 'class.ODBC.php';
            return new ODBC( $database );
            break;
        */

            // wrong type given!
        default:
            trigger_error(
                'Error, database type "' . $type . '" not supported!',
                E_USER_ERROR
            );
    }
}

/**
 * class Yadal
 *
 * Yadal - Yet Another Database Abstraction Layer
 * Abstract database class
 *
 * @author Teye Heimans
 * @package Yadal
 */
class Yadal implements DatabaseAdapter
{
    protected $_conn;           // resource: contains the connection resource
    protected $_db;             // string: contains the database name
    protected $_table;          // string: contains the table name
    protected $_keys;           // array: contains the primary keys
    protected $_isConnected;    // boolean: do we have a connection ?
    public $_quoteNumbers;      // boolean: do we have to quote numbers?
    protected $_nameQuote;      // char/array: quote to use around table and field names (for possible spaces in the names)
    protected $_lastQuery;      // string: the last query executed
    protected $_cache;          // array: cache of most actions.

    /**
     * Yadal::Yadal()
     *
     * Abstract constructor: store the db name.
     * Dont use this class to make a new Yadal object!!! Use the function
     * newYadal() instead!!
     *
     * @param string $db: the database we are using
     * @author Teye Heimans
     */
    public function __construct($db = null)
    {
        if (!is_null($db))
        {
            $this->_db = $db;
        }

        $this->_isConnected  = false;
        $this->_quoteNumbers = false;
        if (!isset($this->_nameQuote) || $this->_nameQuote == null)
        {
            $this->_nameQuote = '"';
        }
    }

    /**
     * Yadal::setConnectionResource()
     *
     * Instead of opening a new connection, set the
     * connection resource of the already opend connection
     *
     * @param resource $conn: The connection resource
     * @return void
     * @access public
     * @author Teye Heimans
     */
    public function setConnectionResource(&$conn)
    {
        $this->_conn = &$conn;
        $this->_isConnected = true;
    }

    /**
     * Yadal::isConnected()
     *
     * Return if we have a connection or not
     *
     * @return boolean
     * @access public
     * @author Teye Heimans
     */
    public function isConnected()
    {
        return $this->_isConnected;
    }

    /**
     * Yadal::dbDate()
     *
     * Convert the given date to the correct database format.
     *
     * @param string $y: The year of the date which should be converteds
     * @param string $m: The month of the date which should be converteds
     * @param string $d: The day of the date which should be converteds
     * @return string the date in the correct format or null when the date could not be converted
     * @access public
     * @author Teye Heimans
     */
    public function dbDate($y, $m, $d)
    {
        return "'$y-$m-$d'";
    }

    /**
     * Yadal::array2case()
     *
     * Change an array to a CASE statement which can be used in an query.
     * Example:
     * array2case( 'UserType', array( 1 => 'Admin', 2 => 'Moderator', 3 => 'User' ) );
     *
     * Result:
     * CASE `UserType`
     *  WHEN 1 THEN 'Admin'
     *  WHEN 2 THEN 'Moderator'
     *  WHEN 3 THEN 'User'
     *  ELSE 'Unknown'
     * END
     *
     * @param string $field: The field which we should use in the case or if statement
     * @param array $options: Array of options. The array key will be used as statement compare-item and the value will be used as value
     * @param string $default: The default value if none of the array keys are matched. Default is "Unknown". If you dont want to have a default value, use false
     * @return string
     * @access public
     * @author Teye Heimans
     */
    public function array2case($field, $options, $default = 'Unknown')
    {
        // if there are 2 options, use an if statement
        if (sizeof($options) == 2)
        {
            list($key1, $value1) = $options;
            list($key2, $value2) = $options;

            return
                "IF( " . $this->quote($field) . " == '" . $key1 . "', " .
                "'" . $this->escapeString($value1) . "', " .
                "'" . $this->escapeString($value2) . "' )";
        }
        // there are more then 2 options, use a case statement
        else
        {
            $result = "CASE " . $this->quote($field) . "\n";
            foreach ($options as $key => $value)
            {
                $result .= "  WHEN '" . $key . "' THEN '" . $this->escapeString($value) . "'\n";
            }
            if ($default)
            {
                $result .= "  ELSE '" . $this->escapeString($default) . "'\n";
            }
            $result .= "END";

            return $result;
        }
    }

    /**
     * Yadal::quoteNumbers()
     *
     * Do we have to quote numbers ?
     *
     * @return boolean
     * @access public
     * @author Teye Heimans
     */
    public function quoteNumbers()
    {
        return $this->_quoteNumbers;
    }

    /**
     * Yadal::quote()
     *
     * Return the table name or field name quoted (so that spaces can be used)
     *
     * @param string $name: The table or field name which should me quoted
     * @return string
     * @access public
     * @author Teye Heimans
     */
    public function quote($name)
    {
        // is there a dot in the name ? (like table.name)
        $pos = strpos($name, '.');
        if ($pos !== false)
        {
            return
                $this->quote(substr($name, 0, $pos)) . '.' .
                $this->quote(substr($name, $pos + 1));
        }
        if (is_array($this->_nameQuote))
        {
            return $this->_nameQuote[0] . $name . $this->_nameQuote[1];
        }
        else
        {
            return $this->_nameQuote . $name . $this->_nameQuote;
        }
    }

    /**
     * Yadal::clearCache()
     *
     * Clears the cache of most functions, like getUniqueFields, getNotNullFields, etc.
     *
     * @return void
     * @access public
     * @author Teye Heimans
     */
    public function clearCache()
    {
        $this->_cache = null;
    }

    /**
     * Yadal::getLastQuery()
     *
     * Return the last query which was executed
     *
     * @return string
     * @access public
     * @author Teye Heimans
     */
    public function getLastQuery()
    {
        return $this->_lastQuery;
    }

    /**
     * Yadal::result()
     *
     * Return a specific result of a sql resource
     *
     * @param resource $sql: The sql where you want to get a result from
     * @param int $row: The row where you want a result from
     * @param string $field: The field which result you want
     * @return string|false
     * @access public
     * @author Teye Heimans
     */
    public function result($sql, $row = 0, $field = null)
    {
        $i = 0;
        while ($data = $this->getRecord($sql))
        {
            if ($i++ == $row)
            {
                break;
            }
        }

        if (is_null($field))
        {
            list(, $value) = each($data);
            return $value;
        }
        else
        {
            foreach ($data as $column => $value)
            {
                if (strtolower($column) == strtolower($field))
                {
                    return $value;
                }
            }
        }

        return false;
    }

    public function getTables()
    {
        trigger_error(
            'Error, abstract function ' . __FUNCTION__ . ' has not been overwritten by class ' . get_class($this),
            E_USER_WARNING
        );
    }

    public function getFieldTypes($table)
    {
        trigger_error(
            'Error, abstract function ' . __FUNCTION__ . ' has not been overwritten by class ' . get_class($this),
            E_USER_WARNING
        );
    }

    public function connect($host = 'localhost', $username = '', $password = '')
    {
        trigger_error(
            'Error, abstract function ' . __FUNCTION__ . ' has not been overwritten by class ' . get_class($this),
            E_USER_WARNING
        );
    }

    public function getNotNullFields($table)
    {
        trigger_error(
            'Error, abstract function ' . __FUNCTION__ . ' has not been overwritten by class ' . get_class($this),
            E_USER_WARNING
        );
    }

    public function getUniqueFields($table)
    {
        trigger_error(
            'Error, abstract function ' . __FUNCTION__ . ' has not been overwritten by class ' . get_class($this),
            E_USER_WARNING
        );
    }

    public function getPrKeys($table)
    {
        trigger_error(
            'Error, abstract function ' . __FUNCTION__ . ' has not been overwritten by class ' . get_class($this),
            E_USER_WARNING
        );
    }

    public function recordCount($sql)
    {
        trigger_error(
            'Error, abstract function ' . __FUNCTION__ . ' has not been overwritten by class ' . get_class($this),
            E_USER_WARNING
        );
    }

    public function getFieldNames($table)
    {
        trigger_error(
            'Error, abstract function ' . __FUNCTION__ . ' has not been overwritten by class ' . get_class($this),
            E_USER_WARNING
        );
    }

    public function escapeString($string)
    {
        trigger_error(
            'Error, abstract function ' . __FUNCTION__ . ' has not been overwritten by class ' . get_class($this),
            E_USER_WARNING
        );
    }

    public function getRecord($sql)
    {
        trigger_error(
            'Error, abstract function ' . __FUNCTION__ . ' has not been overwritten by class ' . get_class($this),
            E_USER_WARNING
        );
    }

    public function getInsertId()
    {
        trigger_error(
            'Error, abstract function ' . __FUNCTION__ . ' has not been overwritten by class ' . get_class($this),
            E_USER_WARNING
        );
    }

    public function query($query)
    {
        trigger_error(
            'Error, abstract function ' . __FUNCTION__ . ' has not been overwritten by class ' . get_class($this),
            E_USER_WARNING
        );
    }

    public function close()
    {
        trigger_error(
            'Error, abstract function ' . __FUNCTION__ . ' has not been overwritten by class ' . get_class($this),
            E_USER_WARNING
        );
    }

    public function getError()
    {
        trigger_error(
            'Error, abstract function ' . __FUNCTION__ . ' has not been overwritten by class ' . get_class($this),
            E_USER_WARNING
        );
    }

    public function GetErrorNo()
    {
        trigger_error(
            'Error, abstract function ' . __FUNCTION__ . ' has not been overwritten by class ' . get_class($this),
            E_USER_WARNING
        );
    }
}
