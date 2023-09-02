<?php

namespace Lib\Model\AuthorizationModel;

use Lib\Exception\ExceptionHandler;
use Lib\Model\Model;

/**
 * Class UserRole
 * 
 * this is the model for the user_role table, which is used to store the roles for a user,
 * and the permissions for a role
 */
class UserRole extends Model
{
    protected $table = 'user_roles'; // the name of the table

    /**
     * The function returns the table name as a string.
     * 
     * @return string the value of the variable ->table, which is a string.
     */
    public function getTableName(): string
    {
        return $this->table;
    }

    /**
     * The function deletes a record from a database table based on the provided user ID and role ID.
     * 
     * @param mixed userId The userId parameter is a mixed type, which means it can accept any data
     * type. It is used to specify the user ID for which the record needs to be deleted from the
     * database table.
     * @param mixed roleId The `roleId` parameter is the identifier of the role that you want to
     * delete. It is of type `mixed`, which means it can accept any data type.
     * 
     * @return bool a boolean value. It returns true if the deletion operation was successful and
     * affected at least one row in the database. Otherwise, it returns false.
     */
    public function deleteByUserIdAndRoleId(mixed $userId, mixed $roleId): bool
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

    /**
     * The function creates a new role in a database table using the provided data.
     * 
     * @param array data An associative array containing the data to be inserted into the database
     * table. The keys of the array represent the column names, and the values represent the
     * corresponding values to be inserted.
     * 
     * @return bool a boolean value. If the try block is executed successfully, it will return true. If
     * an exception is caught, it will not return anything.
     */
    public function createRole(array $data = []): bool
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
