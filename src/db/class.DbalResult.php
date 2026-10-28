<?php

declare(strict_types=1);

/**
 * class DbalResult
 *
 * Buffered result of a query executed by DbalAdapter. Buffering makes the
 * record count reliable on every driver (PDO reports 0 rows for SELECT on SQLite).
 *
 * @package FormHandler
 */
class DbalResult
{
    private $_rows;
    private $_position = 0;

    /**
     * DbalResult::__construct()
     *
     * @param array $rows the fetched records in assoc mode
     */
    public function __construct(array $rows)
    {
        // Every value is a string or null, like with the Yadal drivers of FormHandler 3.
        // PDO and mysqli return ints and floats natively since PHP 8.1, but form values are
        // strings, and FormHandler and its users compare, trim and concatenate them as such.
        foreach ($rows as $i => $row)
        {
            foreach ($row as $field => $value)
            {
                if (is_bool($value))
                {
                    $rows[$i][$field] = $value ? '1' : '0';
                }
                elseif (is_int($value) || is_float($value))
                {
                    $rows[$i][$field] = (string) $value;
                }
            }
        }

        $this->_rows = $rows;
    }

    /**
     * DbalResult::fetch()
     *
     * @return array|false the next record or false when there are no records left
     */
    public function fetch()
    {
        if ($this->_position >= count($this->_rows))
        {
            return false;
        }

        return $this->_rows[$this->_position++];
    }

    /**
     * DbalResult::count()
     *
     * @return int the number of records
     */
    public function count()
    {
        return count($this->_rows);
    }
}
