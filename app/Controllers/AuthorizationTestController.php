<?php

namespace App\Controllers;

use App\Models\Contact;
use Lib\Authorization\Authorization;
use Lib\Model\Model;

class AuthorizationTestController
{
    public function test()
    {
        $permissions = [
            'edit.post',
            'create.post',
            'delete.post',
            'view.post',
            'edit.user',
            'create.user',
            'delete.user',
        ];

        $roles = [
            'auditor',
        ];

        // Authorization::createPermissions($permissions);

        // Authorization::createRoles($roles);

        // Authorization::assignRoleToUser(3, 'user');

        // Authorization::revokeRoleFromUser(3, 4);

        // Authorization::grantPermissionToRole('admin', 1);

        // Authorization::revokePermissionFromRole('admin', 1);

        // $hasRole = Authorization::checkUserHasRole(1, 2);
        // if ($hasRole) {
        //     echo "El usuario tiene el rol.";
        // } else {
        //     echo "El usuario no tiene el rol.";
        // }

        // $hasPermission = Authorization::checkRoleHasPermission('admin', 'edit_post');
        // if ($hasPermission) {
        //     echo "El rol tiene el permiso.";
        // } else {
        //     echo "El rol no tiene el permiso.";
        // }

        // $userRoles = Authorization::getUserRoles(1);
        // echo "Roles del usuario:";
        // foreach ($userRoles as $role) {
        //     echo $role['name'];
        // }

        // $rolePermissions = Authorization::getRolePermissions('admin');
        // echo "Permisos del rol:";
        // foreach ($rolePermissions as $permission) {
        //     echo $permission['name'];
        // }
    }
}
