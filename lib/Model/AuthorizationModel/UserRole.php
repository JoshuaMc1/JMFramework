<?php

namespace Lib\Model\AuthorizationModel;

use Lib\Exception\ExceptionHandler;
use Lib\Model\Model;

class UserRole extends Model
{
    protected $table = 'user_roles';

    public function getTableName()
    {
        return $this->table;
    }

    public function deleteByUserIdAndRoleId($userId, $roleId): bool
    {
        try {
            $query = "DELETE FROM {$this->table} WHERE user_id = ? AND role_id = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("ii", $userId, $roleId);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                return true;
            }

            return false;
        } catch (\Exception $th) {
            ExceptionHandler::handleException($th);
        }
    }


    public function createRole($data = []): bool
    {
        try {
            $columns = implode(', ', array_keys($data));
            $values = implode(', ', array_fill(0, count($data), '?'));

            $query = "INSERT INTO {$this->table} ({$columns}) VALUES ({$values})";

            $stmt = $this->connection->prepare($query);

            $stmt->bind_param(str_repeat('s', count($data)), ...array_values($data));
            $stmt->execute();

            return true;
        } catch (\Throwable  $th) {
            ExceptionHandler::handleException($th);
        }
    }
}
