<?php

namespace Lib\Model;

use Lib\Connection\Connection;
use Lib\Http\ErrorHandler;

class Model
{
    protected $connection;
    protected $query;
    protected $table;

    public function __construct()
    {
        $this->connection = (new Connection())->getConnection();
    }

    public function query(string $sql)
    {
        try {
            $this->query = $this->connection->query($sql);

            return $this;
        } catch (\Throwable $th) {
            ErrorHandler::renderError(500, 'Internal server error', $th->getMessage());
        }
    }

    public function first()
    {
        try {
            return $this->query->fetch_assoc();
        } catch (\Throwable $th) {
            ErrorHandler::renderError(500, 'Internal server error', $th->getMessage());
        }
    }

    public function get()
    {
        try {
            return $this->query->fetch_all(MYSQLI_ASSOC);
        } catch (\Throwable $th) {
            ErrorHandler::renderError(500, 'Internal server error', $th->getMessage());
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
        } catch (\Throwable $th) {
            ErrorHandler::renderError(500, 'Internal server error', $th->getMessage());
        }
    }

    public static function find($id)
    {
        try {
            $instance = new static;
            $stmt = $instance->connection->prepare("SELECT * FROM {$instance->table} WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        } catch (\Throwable $th) {
            ErrorHandler::renderError(500, 'Internal server error', $th->getMessage());
        }
    }

    public static function where($column, $operator, $value = null)
    {
        try {
            $instance = new static;

            if ($value == null) {
                $value = $operator;
                $operator = "=";
            }

            $stmt = $instance->connection->prepare("SELECT * FROM {$instance->table} WHERE `{$column}` {$operator} ?");

            $stmt->bind_param('s', $value);
            $stmt->execute();
            $result = $stmt->get_result();
            $instance->query = $result;
            return $instance;
        } catch (\Throwable $th) {
            ErrorHandler::renderError(500, 'Internal server error', $th->getMessage());
        }
    }

    public function create($data)
    {
        try {
            $columns = implode(', ', array_keys($data));
            $values = "'" . implode("', '", array_values($data)) . "'";

            $stmt = $this->connection->prepare("INSERT INTO {$this->table} ({$columns}) VALUES ({$values})");
            $stmt->execute();
            $id = $this->connection->insert_id;

            return $this->find($id);
        } catch (\Throwable $th) {
            ErrorHandler::renderError(500, 'Internal server error', $th->getMessage());
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
        } catch (\Throwable $th) {
            ErrorHandler::renderError(500, 'Internal server error', $th->getMessage());
        }
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
        } catch (\Throwable $th) {
            ErrorHandler::renderError(500, 'Internal server error', $th->getMessage());
        }
    }

    public function delete(int $id)
    {
        try {
            $this->query("DELETE FROM {$this->table} WHERE id = {$id}");
        } catch (\Throwable $th) {
            ErrorHandler::renderError(500, 'Internal server error', $th->getMessage());
        }
    }

    public function deleteForTheStatus(int $id)
    {
        try {
            $this->query("UPDATE {$this->table} SET status = '0' WHERE id = {$id}");
        } catch (\Throwable $th) {
            ErrorHandler::renderError(500, 'Internal server error', $th->getMessage());
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
        } catch (\Throwable $th) {
            ErrorHandler::renderError(500, 'Internal server error', $th->getMessage());
        }
    }

    public function count()
    {
        try {
            return $this->query->num_rows;
        } catch (\Throwable $th) {
            ErrorHandler::renderError(500, 'Internal server error', $th->getMessage());
        }
    }

    public function __destruct()
    {
        $this->connection->close();
    }
}
