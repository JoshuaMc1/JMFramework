<?php

namespace Lib\Authorization;

use App\Models\User;
use Lib\Exception\ExceptionHandler;
use Lib\Exception\AuthorizationExceptions\{
    PermissionCreationException,
    PermissionNotFoundException,
    RoleCreationException,
    UserNotFoundException,
    RoleNotFoundException,
    UserAlreadyHasPermissionException
};
use Lib\Model\AuthorizationModel\{
    Permission,
    Role,
    RolePermission,
    UserRole
};
use Lib\Http\Request;
use Lib\Model\Model;

class Authorization extends Model
{
    public static function assignRoleToUser($userId, $roleId)
    {
        try {
            $user = self::getUser($userId);

            if ($user === null) {
                throw new UserNotFoundException($userId);
            }

            $role = self::getRoleByIdOrName($roleId);

            if ($role == null) {
                throw new RoleNotFoundException($roleId);
            }

            $authorization = new UserRole();

            if (self::checkUserHasRole($userId, $role['id'])) {
                throw new UserAlreadyHasPermissionException($userId, $role['id']);
            }

            return $authorization->createRole(['user_id' => $userId, 'role_id' => $role['id']]);
        } catch (UserNotFoundException | RoleNotFoundException | UserAlreadyHasPermissionException | \Throwable $exception) {
            ExceptionHandler::handleException($exception);
        }
    }

    private static function getRoleByIdOrName($roleIdOrName)
    {

        if (is_numeric($roleIdOrName)) {
            return self::getRole($roleIdOrName);
        } else {
            return self::getRoleByName($roleIdOrName);
        }
    }

    public static function revokeRoleFromUser($userId, $roleId): bool
    {
        try {
            if (is_numeric($roleId)) {
                $role = self::getRole($roleId);
            } else {
                $role = self::getRoleByName($roleId);
            }

            if ($role !== null) {
                $authorization = new UserRole();
                $result = $authorization->select('*', [
                    'user_id' => $userId,
                    'role_id' => $role['id']
                ])->get();

                if (count($result) == 0) {
                    return false;
                }

                foreach ($result as $row) {
                    $resultUserId = $row['user_id'];
                    $resultRoleId = $row['role_id'];
                    $authorization->deleteByUserIdAndRoleId($resultUserId, $resultRoleId);
                }

                return true;
            }

            return false;
        } catch (\Exception $exception) {
            ExceptionHandler::handleException($exception);
        }
    }

    public static function grantPermissionToRole($roleId, $permissionId)
    {
        if (is_numeric($roleId)) {
            $role = self::getRole($roleId);
        } else {
            $role = self::getRoleByName($roleId);
        }

        if (is_numeric($permissionId)) {
            $permission = self::getPermission($permissionId);
        } else {
            $permission = self::getPermissionByName($permissionId);
        }

        if ($role !== null && $permission !== null) {
            $authorization = new RolePermission();
            $authorization->create(['role_id' => $role['id'], 'permission_id' => $permission['id']]);
        }
    }

    public static function revokePermissionFromRole($roleId, $permissionId)
    {
        if (is_numeric($roleId)) {
            $role = self::getRole($roleId);
        } else {
            $role = self::getRoleByName($roleId);
        }

        if (is_numeric($permissionId)) {
            $permission = self::getPermission($permissionId);
        } else {
            $permission = self::getPermissionByName($permissionId);
        }

        if ($role !== null && $permission !== null) {
            $authorization = new RolePermission();
            $result = RolePermission::where('role_id', $role['id'])
                ->where('permission_id', $permission['id'])
                ->get();

            if (!empty($result)) {
                $permissionId = $result[0]['id'];
                $authorization->delete($permissionId);
            }
        }
    }

    private static function getRoleByName(string $roleName)
    {
        $result = Role::where('name', $roleName)->get();

        if (!empty($result)) {
            return $result[0];
        }

        return null;
    }

    private static function checkUserHasRole($userId, $roleId): bool
    {
        $result = UserRole::where('user_id', $userId)->where('role_id', $roleId)->get();

        return !empty($result);
    }

    public static function checkRoleHasPermission($roleId, $permissionId)
    {
        $result = RolePermission::where('role_id', $roleId)->where('permission_id', $permissionId)->get();

        return count($result) > 0;
    }

    public static function getUserRoles($userId)
    {
        $result = UserRole::where('user_id', $userId)->get();

        $roles = [];
        foreach ($result as $row) {
            $roles[] = self::getRole($row['role_id']);
        }

        return $roles;
    }

    public static function getRolePermissions($roleId)
    {
        $result = RolePermission::where('role_id', $roleId)->get();

        $permissions = [];

        foreach ($result as $row) {
            $permissions[] = self::getPermission($row['permission_id']);
        }

        return $permissions;
    }

    public static function getRole($roleId)
    {
        return Role::find($roleId);
    }

    public static function getPermission($permissionId)
    {
        return Permission::find($permissionId);
    }

    public static function createRoles(array $roles)
    {
        $roleModel = new Role();

        try {
            $connection = $roleModel->getConnection();
            $connection->begin_transaction();

            $stmt = $connection->prepare("INSERT INTO {$roleModel->getTableName()} (name) VALUES (?)");

            foreach ($roles as $role) {
                $stmt->bind_param('s', $role);
                $stmt->execute();
            }

            $connection->commit();

            foreach ($roles as $role) {
                $createdRole = self::getRoleByName($role);
                if ($createdRole === null) {
                    throw new RoleCreationException($role);
                }
            }
        } catch (RoleCreationException | \Throwable $th) {
            $connection->rollback();
            ExceptionHandler::handleException($th);
        }
    }

    public static function createPermissions(array $permissions)
    {
        try {
            $permissionModel = new Permission();
            $connection = $permissionModel->getConnection();
            $connection->begin_transaction();

            $stmt = $connection->prepare("INSERT INTO {$permissionModel->getTableName()} (name) VALUES (?)");

            foreach ($permissions as $permission) {
                $stmt->bind_param('s', $permission);
                $stmt->execute();
            }

            $connection->commit();

            foreach ($permissions as $permission) {
                $createdPermission = self::getPermissionByName($permission);
                if ($createdPermission === null) {
                    throw new PermissionCreationException($permission);
                }
            }
        } catch (PermissionCreationException | \Throwable $th) {
            $connection->rollback();
            ExceptionHandler::handleException($th);
        }
    }

    public static function assignPermissionsToRole($roleIdOrName, array $permissionIds)
    {
        try {
            $role = self::getRole($roleIdOrName);

            if ($role === null) {
                throw new RoleNotFoundException($roleIdOrName);
            }

            $roleId = $role['id'];
            $rolePermissionModel = new RolePermission();
            $connection = $rolePermissionModel->getConnection();
            $connection->begin_transaction();

            $stmt = $connection->prepare("INSERT INTO {$rolePermissionModel->getTableName()} (role_id, permission_id) VALUES (?, ?)");

            foreach ($permissionIds as $permissionId) {
                $permission = self::getPermission($permissionId);

                if ($permission === null) {
                    $connection->rollback();
                    throw new PermissionNotFoundException($permissionId);
                }

                $permissionId = $permission['id'];
                $stmt->bind_param('ii', $roleId, $permissionId);
                $stmt->execute();
            }

            $connection->commit();
        } catch (RoleNotFoundException | PermissionNotFoundException | \Throwable $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public static function can(Request $request, string $permissionName): bool
    {
        $user = $request->user();
        $userRoles = self::getUserRoles($user['id']);

        $permission = self::getPermissionByName($permissionName);
        if ($permission !== null) {
            foreach ($userRoles as $role) {
                if (self::checkRoleHasPermission($role['id'], $permission['id'])) {
                    return true;
                }
            }
        }

        return false;
    }

    private static function getPermissionByName(string $permissionName)
    {
        return Permission::where('name', $permissionName)->first();
    }

    private static function getUser($userId)
    {
        return User::find($userId);
    }
}
