<?php

namespace App\Models;

use mysqli;

class Model
{
    protected $db_host = DB_HOST;
    protected $db_user = DB_USER;
    protected $db_pass = DB_PASS;
    protected $db_name = DB_NAME;
    protected $db_port = DB_PORT;

    protected $connection;
    protected $query;
    protected $table;

    public function __construct()
    {
        $this->connection();
    }

    public function connection()
    {
        $this->connection =  new mysqli($this->db_host, $this->db_user, $this->db_pass, $this->db_name, $this->db_port);
        if ($this->connection->connect_error) {
            die('Connection error: ' . $this->connection->connect_error);
        }
    }

    public function query($sql)
    {
        $this->query = $this->connection->query($sql);

        return $this;
    }

    public function first()
    {
        return $this->query->fetch_assoc();
    }

    public function get()
    {
        return $this->query->fetch_all(MYSQLI_ASSOC);
    }

    public function all()
    {
        return $this->query("SELECT * FROM {$this->table}")->get();
    }

    public function find($id)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE id = {$id}")->first();
    }

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

    public function create($data)
    {
        $columns = implode(', ', array_keys($data));
        $values = "'" . implode("', '", array_values($data)) . "'";

        $this->query("INSERT INTO {$this->table} ({$columns}) VALUES ({$values})");

        return $this->find($this->connection->insert_id);
    }

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

    public function delete($id)
    {
        $this->query("DELETE FROM {$this->table} WHERE id = {$id}");
    }
}
