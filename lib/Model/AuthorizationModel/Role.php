<?php

namespace Lib\Model\AuthorizationModel;

use Lib\Model\Model;

class Role extends Model
{
    protected $table = 'roles';

    public function getTableName()
    {
        return $this->table;
    }
}
