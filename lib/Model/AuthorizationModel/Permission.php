<?php

namespace Lib\Model\AuthorizationModel;

use Lib\Model\Model;

class Permission extends Model
{
    protected $table = 'permissions';

    public function getTableName()
    {
        return $this->table;
    }
}
