<?php

namespace Lib\Model\AuthorizationModel;

use Lib\Model\Model;

/**
 * Class RolePermission
 * 
 * this is the model for the role_permission table, which is used to store the permissions for a role
 */
class RolePermission extends Model
{
    protected $table = 'role_permissions'; // the name of the table

    /**
     * The function returns the table name as a string.
     * 
     * @return string the value of the variable ->table, which is a string.
     */
    public function getTableName(): string
    {
        return $this->table;
    }
}
