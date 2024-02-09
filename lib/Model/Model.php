<?php

namespace Lib\Model;

use Lib\Connection\Connection;
use Lib\Exception\ExceptionHandler;
use PDO;

class Model
{
    /**
     * @var \PDO
     */
    protected $connection;

    /**
     * @var \PDOStatement|\array|null
     */
    protected $query;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var array
     */
    protected $hidden = [];
    public function __construct()
    {
        $this->connection = (new Connection())->getConnection();
    }

    public function query(string $sql, array $values = [])
    {
        try {
            $statement = $this->connection->prepare($sql);
            $statement->execute($values);
            $this->query = $statement->fetchAll(PDO::FETCH_OBJ);

            return $this;
        } catch (\PDOException $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function first()
    {
        try {
            $result = isset($this->query[0]) ? $this->query[0] : null;

            return $result;
        } catch (\Exception $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function get()
    {
        try {
            $results = $this->query;

            foreach ($results as &$result) {
                $result = $this->hideFields($result);
            }

            return $results;
        } catch (\Exception $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public static function all()
    {
        try {
            $instance = new static;
            $stmt = $instance->connection->prepare("SELECT * FROM {$instance->table}");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);

            return $result;
        } catch (\PDOException $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public static function find($id)
    {
        try {
            $instance = new static;
            $stmt = $instance->connection->prepare("SELECT * FROM {$instance->table} WHERE id = ?");
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_OBJ);

            return $result ? $instance->hideFields($result) : null;
        } catch (\PDOException $th) {
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
            $stmt->bindParam(1, $value);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);

            $instance->query = $result;

            return $instance;
        } catch (\PDOException $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function create($data = [])
    {
        try {
            $this->connection->beginTransaction();

            $columns = implode(', ', array_keys($data));
            $values = implode(', ', array_fill(0, count($data), '?'));

            $query = "INSERT INTO {$this->table} ({$columns}) VALUES ({$values})";

            $stmt = $this->connection->prepare($query);

            $stmt->execute(array_values($data));

            $id = $this->connection->lastInsertId();

            $this->connection->commit();

            return $this->find($id);
        } catch (\PDOException $th) {
            $this->connection->rollBack();
            ExceptionHandler::handleException($th);
        }
    }

    public function update($id, $data)
    {
        try {
            $fields = [];
            $params = [];

            foreach ($data as $key => $value) {
                $fields[] = "{$key} = ?";
                $params[] = $value;
            }

            $params[] = $id;

            $fields = implode(', ', $fields);

            $stmt = $this->connection->prepare("UPDATE {$this->table} SET {$fields} WHERE id = ?");
            $stmt->execute($params);

            return $this->find($id);
        } catch (\PDOException $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function from($table)
    {
        try {
            $this->query = "SELECT * FROM " . $this->connection->quote($table);
            return $this;
        } catch (\Exception $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function delete(mixed $id)
    {
        try {
            $this->query("DELETE FROM {$this->table} WHERE id = ?", [$id]);
        } catch (\Exception $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function deleteForTheStatus(int $id)
    {
        try {
            $this->query("UPDATE {$this->table} SET status = '0' WHERE id = ?", [$id]);
        } catch (\Exception $th) {
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
            }

            return $this->create($data);
        } catch (\Exception $th) {
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
        } catch (\Exception $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function limit($limit)
    {
        try {
            $this->query .= " LIMIT {$limit}";
            return $this;
        } catch (\Exception $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function offset($offset)
    {
        try {
            $this->query .= " OFFSET {$offset}";
            return $this;
        } catch (\Exception $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function join($table, $foreignColumn, $operator, $localColumn)
    {
        try {
            $this->query .= " JOIN {$table} ON {$foreignColumn} {$operator} {$localColumn}";
            return $this;
        } catch (\Exception $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function groupBy($column)
    {
        try {
            $this->query .= " GROUP BY {$column}";
            return $this;
        } catch (\Exception $th) {
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
            $this->query .= " HAVING {$column} {$operator} ?";
            $this->execute([$value]);
            return $this;
        } catch (\Exception $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function between($column, $start, $end)
    {
        try {
            $this->query .= " WHERE {$column} BETWEEN ? AND ?";
            $this->execute([$start, $end]);
            return $this;
        } catch (\Exception $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function whereIn($column, array $values)
    {
        try {
            $inValues = implode(', ', array_fill(0, count($values), '?'));

            $this->query("SELECT * FROM {$this->table} WHERE {$column} IN ({$inValues})");
            $this->execute($values);
            return $this;
        } catch (\Exception $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function percentage($column, $percentage)
    {
        try {
            $this->query("SELECT {$column} * ? / 100 FROM {$this->table}");
            $this->execute([$percentage]);
            return $this;
        } catch (\Exception $th) {
            ExceptionHandler::handleException($th);
        }
    }

    private function execute($values)
    {
        try {
            $stmt = $this->connection->prepare($this->query);
            $stmt->execute($values);
            $this->query = '';
        } catch (\PDOException $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function distinct()
    {
        try {
            $this->query = str_replace('SELECT', 'SELECT DISTINCT', $this->query);
            return $this;
        } catch (\Exception $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function whereNull($column)
    {
        try {
            $this->query("SELECT * FROM {$this->table} WHERE {$column} IS NULL");
            return $this;
        } catch (\Exception $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function whereNotNull($column)
    {
        try {
            $this->query("SELECT * FROM {$this->table} WHERE {$column} IS NOT NULL");
            return $this;
        } catch (\Exception $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function selectRaw($expression)
    {
        try {
            $this->query("SELECT {$expression} FROM {$this->table}");
            return $this;
        } catch (\Exception $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function sum($column)
    {
        try {
            $this->query("SELECT SUM({$column}) FROM {$this->table}");
            return $this;
        } catch (\Exception $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function subtract($column1, $column2)
    {
        try {
            $this->query("SELECT {$column1} - {$column2} FROM {$this->table}");
            return $this;
        } catch (\Exception $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function multiply($column1, $column2)
    {
        try {
            $this->query("SELECT {$column1} * {$column2} FROM {$this->table}");
            return $this;
        } catch (\Exception $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function divide($column1, $column2)
    {
        try {
            $this->query("SELECT {$column1} / {$column2} FROM {$this->table}");
            return $this;
        } catch (\Exception $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function absolute($column)
    {
        try {
            $this->query("SELECT ABS({$column}) FROM {$this->table}");
            return $this;
        } catch (\Exception $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function round($column, $precision = 0)
    {
        try {
            $this->query("SELECT ROUND({$column}, {$precision}) FROM {$this->table}");
            return $this;
        } catch (\Exception $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public function count()
    {
        try {
            return count($this->query);
        } catch (\Exception $th) {
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
}
