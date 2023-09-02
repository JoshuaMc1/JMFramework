<?php

namespace Lib\Model\AuthorizationModel;

use Lib\Model\Model;

/**
 * Class Role
 * 
 * this model is for role table
 */
class Role extends Model
{
    protected $table = 'roles'; // the name of the table

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
