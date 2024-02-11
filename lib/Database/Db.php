<?php

namespace Lib\Database;

use Lib\Connection\Connection;
use Lib\Exception\CustomException;
use PDO;

class DB
{
    protected $connection;
    protected $table;
    protected $whereClause = '';
    protected $selectClause = '';
    protected $joinClause = '';
    protected $groupClause = '';
    protected $params = [];
    protected $columns = [];

    public function __construct()
    {
        $this->connection = (new Connection())->getConnection();
    }

    public static function table(string $table)
    {
        $db = new self();
        $db->table = $table;
        return $db;
    }

    public function where($column, $operator = '=', $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = "=";
        }

        $operators = ['=', '<', '>', '<=', '>=', '!=', '<>'];

        if (!in_array($operator, $operators)) {
            throw new CustomException(3104, lang('invalid_operator'), lang('invalid_operator_message'));
        }

        $this->whereClause = "$column $operator ?";
        $this->params[] = $value;

        return $this;
    }

    public function get()
    {
        if (empty($this->table)) {
            throw new CustomException(3101, lang('table_not_set'), lang('table_not_set_message'));
        }

        $this->selectClause = empty($this->selectClause) ?
            "SELECT *" :
            "SELECT " . $this->selectClause;

        $this->whereClause = !empty($this->whereClause) ? " WHERE $this->whereClause" : "";
        $this->groupClause = !empty($this->groupClause) ? " GROUP BY $this->groupClause" : "";
        $this->joinClause = !empty($this->joinClause) ? " $this->joinClause" : "";

        $sql = "$this->selectClause FROM $this->table$this->joinClause$this->whereClause$this->groupClause";
        $stmt = $this->connection->prepare($sql);

        $stmt->execute($this->params);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function first()
    {
        $result = $this->get();

        return !empty($result) ? $result[0] : null;
    }

    public function all()
    {
        return $this->get();
    }

    public function count()
    {
        $result = $this->get();

        return count($result);
    }

    public function select(...$columns)
    {
        $columns = implode(', ', $columns);
        $this->selectClause = "SELECT $columns";

        return $this;
    }

    public function insert(array $data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO $this->table ($columns) VALUES ($placeholders)";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(array_values($data));
        return $this->connection->lastInsertId();
    }

    public function update(array $data)
    {
        $setClause = implode(', ', array_map(function ($key) {
            return "$key = ?";
        }, array_keys($data)));

        $sql = "UPDATE $this->table SET $setClause $this->whereClause";
        $stmt = $this->connection->prepare($sql);

        $stmt->execute(array_merge(array_values($data), $this->params));

        return $stmt->rowCount();
    }

    public function delete()
    {
        $sql = "DELETE FROM $this->table $this->whereClause";
        $stmt = $this->connection->prepare($sql);

        $stmt->execute($this->params);

        return $stmt->rowCount();
    }

    public function exists(): bool
    {
        return $this->count() === 1;
    }

    public function unique(): bool
    {
        return $this->count() === 1;
    }

    public function join($table, $firstColumn, $operator = '=', $secondColumn)
    {
        $this->joinClause = "JOIN $table ON $firstColumn $operator $secondColumn";

        return $this;
    }

    public function group(...$columns)
    {
        $columns = implode(', ', $columns);
        $this->groupClause = "GROUP BY $columns";

        return $this;
    }

    protected function reset()
    {
        $this->whereClause = '';
        $this->params = [];
    }

    public function __destruct()
    {
        $this->reset();
    }
}
