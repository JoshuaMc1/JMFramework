<?php

namespace Lib\Model\AuthorizationModel;

use Lib\Model\Model;

class RolePermission extends Model
{
    protected $table = 'role_permissions';

    public function getTableName()
    {
        return $this->table;
    }
}
