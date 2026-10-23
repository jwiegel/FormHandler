<?php

/**
 * SQLite database of the database examples.
 *
 * The file is in the temp folder of the container, so it starts empty again
 * after "docker compose restart web".
 */

declare(strict_types=1);

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

function example_db(): Connection
{
    $file = sys_get_temp_dir() . '/formhandler-examples.sqlite';
    $new = !file_exists($file);

    $conn = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'path' => $file]);

    if ($new)
    {
        $conn->executeStatement(
            "CREATE TABLE persons (
                id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(50) NOT NULL,
                email VARCHAR(100) NOT NULL,
                birthday DATE NULL,
                country VARCHAR(2) NULL
            )"
        );
        $conn->executeStatement('CREATE UNIQUE INDEX persons_email ON persons (email)');

        $conn->executeStatement(
            "CREATE TABLE preferences (
                id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
                country VARCHAR(2) NULL,
                hobbies VARCHAR(100) NULL,
                languages VARCHAR(100) NULL,
                color VARCHAR(10) NULL,
                city VARCHAR(50) NULL
            )"
        );

        // n:m table for addJunctionTable(): the hobbies of a person
        $conn->executeStatement('CREATE TABLE person_hobbies (person_id INTEGER NOT NULL, hobby_id INTEGER NOT NULL)');

        // lookup tables for the db* fields
        $conn->executeStatement('CREATE TABLE countries (code VARCHAR(2) NOT NULL PRIMARY KEY, name VARCHAR(50) NOT NULL)');
        $conn->executeStatement("INSERT INTO countries VALUES ('at', 'Austria'), ('de', 'Germany'), ('nl', 'Netherlands'), ('ch', 'Switzerland')");
        $conn->executeStatement('CREATE TABLE hobbies (id INTEGER NOT NULL PRIMARY KEY, name VARCHAR(50) NOT NULL)');
        $conn->executeStatement("INSERT INTO hobbies VALUES (1, 'Sport'), (2, 'Music'), (3, 'Books'), (4, 'Travel')");
        $conn->executeStatement('CREATE TABLE languages (id INTEGER NOT NULL PRIMARY KEY, name VARCHAR(50) NOT NULL)');
        $conn->executeStatement("INSERT INTO languages VALUES (1, 'German'), (2, 'Dutch'), (3, 'English')");
        $conn->executeStatement('CREATE TABLE colors (code VARCHAR(10) NOT NULL PRIMARY KEY, name VARCHAR(20) NOT NULL)');
        $conn->executeStatement("INSERT INTO colors VALUES ('red', 'Red'), ('green', 'Green'), ('blue', 'Blue')");
        $conn->executeStatement('CREATE TABLE cities (name VARCHAR(50) NOT NULL PRIMARY KEY)');
        $conn->executeStatement("INSERT INTO cities VALUES ('Amsterdam'), ('Berlin'), ('Rotterdam'), ('Vienna'), ('Zurich')");

        $conn->executeStatement("INSERT INTO persons (name, email, birthday, country) VALUES ('Anna Meyer', 'anna@example.org', '1990-05-17', 'de')");
    }

    return $conn;
}

/**
 * onSaved callback of the database examples: show the saved values and the form again
 */
function example_on_saved($id, $data, $form)
{
    $GLOBALS['example_values'] = array('saved id' => $id) + $data;
    return false;
}

/**
 * print all records of a table as HTML table
 */
function example_table(Connection $conn, string $table, ?string $editPage = null): void
{
    $rows = $conn->fetchAllAssociative('SELECT * FROM ' . $table . ' ORDER BY 1, 2');

    echo '<h2>Table "' . htmlspecialchars($table) . '"</h2>';
    if ($editPage)
    {
        echo '<p><a href="' . $editPage . '">new record</a></p>';
    }
    if (count($rows) == 0)
    {
        echo '<p>empty</p>';
        return;
    }

    echo '<table border="1" cellpadding="4"><tr>';
    foreach (array_keys($rows[0]) as $column)
    {
        echo '<th>' . htmlspecialchars($column) . '</th>';
    }
    echo $editPage ? '<th></th>' : '';
    echo '</tr>';
    foreach ($rows as $row)
    {
        echo '<tr>';
        foreach ($row as $value)
        {
            echo '<td>' . htmlspecialchars((string) $value) . '</td>';
        }
        echo $editPage ? '<td><a href="' . $editPage . '?id=' . $row[array_key_first($row)] . '">edit</a></td>' : '';
        echo '</tr>';
    }
    echo '</table>';
}
