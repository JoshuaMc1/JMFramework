<?php

namespace App\Models;

use Lib\Connection\Connection;

class Model
{
    protected $connection;
    protected $query;
    protected $table;

    public function __construct()
    {
        $this->connection = (new Connection())->getConnection();
    }

    /**
     * It takes a SQL query as a parameter, and returns the result of the query
     * 
     * @param sql The SQL query to execute.
     * 
     * @return The query object.
     */
    public function query($sql)
    {
        $this->query = $this->connection->query($sql);

        return $this;
    }

    /**
     * It returns the first row of the result set as an associative array.
     * 
     * @return The first row of the result set.
     */
    public function first()
    {
        return $this->query->fetch_assoc();
    }

    /**
     * It returns the result of the query as an associative array
     * 
     * @return An array of associative arrays.
     */
    public function get()
    {
        return $this->query->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * This function returns all the rows from the table specified in the  property.
     * 
     * @return The query is being returned.
     */
    public function all()
    {
        return $this->query("SELECT * FROM {$this->table}")->get();
    }

    /**
     * It returns the first row of the result set of the query that selects all columns from the table
     * where the id is equal to the id passed to the function
     * 
     * @param id The id of the record you want to find
     * 
     * @return The first row of the table.
     */
    public function find($id)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE id = {$id}")->first();
    }

    /**
     * It takes a column, an operator, and a value, and returns a query that selects all rows from the
     * table where the column is equal to the value
     * 
     * @param column The column name
     * @param operator The operator to use.
     * @param value The value to be inserted into the database.
     * 
     * @return The query result.
     */
    public function where($column, $operator, $value = null)
    {
        if ($value == null) {
            $value = $operator;
            $operator = "=";
        }

        $value = $this->connection->real_escape_string($value);

        $this->query("SELECT * FROM {$this->table} WHERE {$column} {$operator} '{$value}'");

        return $this;
    }

    /**
     * It takes an array of data, creates a string of column names and a string of values, and then
     * inserts them into the database
     * 
     * @param data an array of the data to be inserted into the database
     * 
     * @return The last inserted id.
     */
    public function create($data)
    {
        $columns = implode(', ', array_keys($data));
        $values = "'" . implode("', '", array_values($data)) . "'";

        $this->query("INSERT INTO {$this->table} ({$columns}) VALUES ({$values})");

        return $this->find($this->connection->insert_id);
    }

    /**
     * It takes an array of data, loops through it, and creates a string of key value pairs separated
     * by commas
     * 
     * @param id The id of the record you want to update.
     * @param data The data to be updated.
     * 
     * @return The updated record.
     */
    public function update($id, $data)
    {
        $fields = [];

        foreach ($data as $key => $value) {
            $fields[] = "{$key} = '{$value}'";
        }

        $fields = implode(', ', $fields);

        $this->query("UPDATE {$this->table} SET {$fields} WHERE id = {$id}");

        return $this->find($id);
    }

    /**
     * It deletes a record from the database
     * 
     * @param id The id of the record you want to delete.
     */
    public function delete($id)
    {
        $this->query("DELETE FROM {$this->table} WHERE id = {$id}");
    }

    /**
     * This function will update the status column of the table to 0 where the id is equal to the id
     * passed in the function.
     * 
     * @param id The id of the row you want to delete
     */
    public function deleteForTheStatus($id)
    {
        $this->query("UPDATE {$this->table} SET status = '0' WHERE id = {$id}");
    }
}
