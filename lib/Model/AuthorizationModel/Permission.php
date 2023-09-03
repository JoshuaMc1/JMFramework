<?php

namespace Lib\Model\AuthorizationModel;

use Lib\Model\Model;

/**
 * Class Permission
 * 
 * this is the model for the permission table, which is used to store the permissions
 */
class Permission extends Model
{
    protected $table = 'permissions'; // the name of the table

    /**
     * The function returns the table name as a string.
     * 
     * @return string the value of the variable table, which is a string.
     */
    public function getTableName(): string
    {
        return $this->table;
    }
}
