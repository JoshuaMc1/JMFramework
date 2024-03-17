<?php

namespace Lib\Database;

use Illuminate\Database\ConnectionInterface;
use Lib\Connection\Connection;
use Lib\Exception\CustomException;

class DB
{
    protected ConnectionInterface $connection;
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

        return $this->connection->select($sql, $this->params);
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
        $result = $this->connection->table($this->table)->insert($data);
        return $result ? $this->connection->getPdo()->lastInsertId() : null;
    }

    public function update(array $data)
    {
        $result = $this->connection->table($this->table)->whereRaw($this->whereClause, $this->params)->update($data);
        return $result;
    }

    public function delete()
    {
        $result = $this->connection->table($this->table)->whereRaw($this->whereClause, $this->params)->delete();
        return $result;
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
