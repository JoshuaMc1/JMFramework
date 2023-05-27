<?php

namespace Lib\Model;

use Lib\Connection\Connection;
use Lib\Exception\ExceptionHandler;

class Model
{
    protected $connection;
    protected $query;
    protected $table;
    protected $hidden = [];

    public function __construct()
    {
        $this->connection = (new Connection())->getConnection();
    }

    public function query(string $sql, array $values = [])
    {
        try {
            $statement = $this->connection->prepare($sql);

            if ($values) {
                $statement->bind_param(str_repeat('s', count($values)), ...$values);
            }

            $statement->execute();
            $this->query = $statement->get_result();

            return $this;
        } catch (\Exception $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function first()
    {
        try {
            $result = $this->query->fetch_assoc();

            return $result;
        } catch (\Exception  $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function get()
    {
        try {
            $results = $this->query->fetch_all(MYSQLI_ASSOC);

            foreach ($results as &$result) {
                $result = $this->hideFields($result);
            }

            return $results;
        } catch (\Exception  $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public static function all()
    {
        try {
            $instance = new static;
            $stmt = $instance->connection->prepare("SELECT * FROM {$instance->table}");
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (\Exception  $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public static function find($id)
    {
        try {
            $instance = new static;
            $stmt = $instance->connection->prepare("SELECT * FROM {$instance->table} WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            $result = $instance->hideFields($result);

            return $result;
        } catch (\Exception  $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public static function where($column, $operator = '=', $value = null)
    {
        try {
            $instance = new static;

            if ($value === null) {
                $value = $operator;
                $operator = "=";
            }

            $operators = ['=', '<', '>', '<=', '>=', '!=', '<>'];

            if (!in_array($operator, $operators)) {
                throw new \InvalidArgumentException("Invalid operator: {$operator}");
            }

            $query = "SELECT * FROM {$instance->table} WHERE `{$column}` {$operator} ?";

            $stmt = $instance->connection->prepare($query);
            $stmt->bind_param('s', $value);
            $stmt->execute();
            $result = $stmt->get_result();

            $instance->query = $result;

            return $instance;
        } catch (\Exception $th) {
            ExceptionHandler::handleException($th);
        }
    }


    public function create($data = [])
    {
        try {
            $columns = implode(', ', array_keys($data));
            $values = implode(', ', array_fill(0, count($data), '?'));

            $query = "INSERT INTO {$this->table} ({$columns}) VALUES ({$values})";

            $stmt = $this->connection->prepare($query);

            $stmt->bind_param(str_repeat('s', count($data)), ...array_values($data));
            $stmt->execute();

            $id = $this->connection->insert_id;

            return $this->find($id);
        } catch (\Exception  $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function update($id, $data)
    {
        try {
            $fields = [];
            $types = '';
            $params = [];

            foreach ($data as $key => $value) {
                $fields[] = "{$key} = ?";
                $types .= $this->getType($value);
                $params[] = $value;
            }

            $params[] = $id;

            $fields = implode(', ', $fields);

            $stmt = $this->connection->prepare("UPDATE {$this->table} SET {$fields} WHERE id = ?");
            $stmt->bind_param($types . 'i', ...$params);
            $stmt->execute();

            return $this->find($id);
        } catch (\Exception  $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function getConnection()
    {
        return $this->connection;
    }

    private function getType($value)
    {
        try {
            $typeMap = [
                'integer' => 'i',
                'double' => 'd',
                'string' => 's',
            ];

            $type = gettype($value);
            return isset($typeMap[$type]) ? $typeMap[$type] : 's';
        } catch (\Exception  $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function from($table)
    {
        try {
            $this->query = "SELECT * FROM " . $this->connection->real_escape_string($table);
            return $this;
        } catch (\Exception $th) {
            ExceptionHandler::handleException($th);
        }
    }


    public function delete(int $id)
    {
        try {
            $this->query("DELETE FROM {$this->table} WHERE id = ?", [$id]);
        } catch (\Exception  $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function deleteForTheStatus(int $id)
    {
        try {
            $this->query("UPDATE {$this->table} SET status = '0' WHERE id = {$id}");
        } catch (\Exception  $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function save(array $data)
    {
        try {
            if (isset($data['id'])) {
                $id = $data['id'];
                unset($data['id']);
                $this->update($id, $data);
                return $this->find($id);
            } else {
                return $this->create($data);
            }
        } catch (\Exception  $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function select($columns = '*', $conditions = [])
    {
        try {
            $columns = is_array($columns) ? implode(', ', $columns) : $columns;
            $query = "SELECT {$columns} FROM {$this->table}";

            if (!empty($conditions)) {
                $whereClause = ' WHERE ';
                $conditionsArray = [];
                $values = [];

                foreach ($conditions as $field => $value) {
                    $conditionsArray[] = "{$field} = ?";
                    $values[] = $value;
                }

                $whereClause .= implode(' AND ', $conditionsArray);
                $query .= $whereClause;
            }

            $this->query($query, $values);

            if ($columns !== '*') {
                if (!is_array($columns)) {
                    $columns = explode(',', $columns);
                }
                $this->hidden = array_diff($this->hidden, $columns);
            }

            return $this;
        } catch (\Exception $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function orderBy($column, $direction = 'ASC')
    {
        try {
            $this->query .= " ORDER BY {$column} {$direction}";
            return $this;
        } catch (\Exception  $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function limit($limit)
    {
        try {
            $this->query .= " LIMIT {$limit}";
            return $this;
        } catch (\Exception  $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function offset($offset)
    {
        try {
            $this->query .= " OFFSET {$offset}";
            return $this;
        } catch (\Exception  $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function join($table, $foreignColumn, $operator, $localColumn)
    {
        try {
            $this->query .= " JOIN {$table} ON {$foreignColumn} {$operator} {$localColumn}";
            return $this;
        } catch (\Exception  $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function groupBy($column)
    {
        try {
            $this->query .= " GROUP BY {$column}";
            return $this;
        } catch (\Exception  $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function having($column, $operator, $value = null)
    {
        try {
            if ($value === null) {
                $value = $operator;
                $operator = '=';
            }
            $this->query .= " HAVING {$column} {$operator} {$value}";
            return $this;
        } catch (\Exception  $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function distinct()
    {
        try {
            $this->query = str_replace('SELECT', 'SELECT DISTINCT', $this->query);
            return $this;
        } catch (\Exception  $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function between($column, $start, $end)
    {
        try {
            $this->query .= " WHERE {$column} BETWEEN {$start} AND {$end}";
            return $this;
        } catch (\Exception  $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function whereNull($column)
    {
        try {
            $this->query("SELECT * FROM {$this->table} WHERE {$column} IS NULL");
            return $this;
        } catch (\Exception  $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function whereNotNull($column)
    {
        try {
            $this->query("SELECT * FROM {$this->table} WHERE {$column} IS NOT NULL");
            return $this;
        } catch (\Exception  $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function whereIn($column, array $values)
    {
        try {
            $inValues = implode(', ', array_map(function ($value) {
                return "'{$value}'";
            }, $values));

            $this->query("SELECT * FROM {$this->table} WHERE {$column} IN ({$inValues})");
            return $this;
        } catch (\Exception  $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function selectRaw($expression)
    {
        try {
            $this->query("SELECT {$expression} FROM {$this->table}");
            return $this;
        } catch (\Exception  $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function sum($column)
    {
        try {
            $this->query("SELECT SUM({$column}) FROM {$this->table}");
            return $this;
        } catch (\Exception  $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function subtract($column1, $column2)
    {
        try {
            $this->query("SELECT {$column1} - {$column2} FROM {$this->table}");
            return $this;
        } catch (\Exception  $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function percentage($column, $percentage)
    {
        try {
            $this->query("SELECT {$column} * {$percentage} / 100 FROM {$this->table}");
            return $this;
        } catch (\Exception  $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function multiply($column1, $column2)
    {
        try {
            $this->query("SELECT {$column1} * {$column2} FROM {$this->table}");
            return $this;
        } catch (\Exception  $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function divide($column1, $column2)
    {
        try {
            $this->query("SELECT {$column1} / {$column2} FROM {$this->table}");
            return $this;
        } catch (\Exception  $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function absolute($column)
    {
        try {
            $this->query("SELECT ABS({$column}) FROM {$this->table}");
            return $this;
        } catch (\Exception  $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function round($column, $precision = 0)
    {
        try {
            $this->query("SELECT ROUND({$column}, {$precision}) FROM {$this->table}");
            return $this;
        } catch (\Exception  $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function count()
    {
        try {
            return $this->query->num_rows;
        } catch (\Exception  $th) {
            ExceptionHandler::handleException($th);
        }
    }

    protected function hideFields($result)
    {
        foreach ($this->hidden as $field) {
            if (isset($result[$field])) {
                unset($result[$field]);
            }
        }

        return $result;
    }

    public function __destruct()
    {
        $this->connection->close();
    }
}
