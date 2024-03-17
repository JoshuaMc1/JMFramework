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
use Lib\Http\Auth;
use Lib\Model\AuthorizationModel\{
    Permission,
    Role,
    RolePermission,
    UserRole
};
use Lib\Http\Request;
use Lib\Model\Model;
use PDO;

/**
 * Class Authorization
 *
 * Provides functionality for managing user roles and permissions.
 */

class Authorization extends Model
{
    /**
     * Assigns a role to a user.
     *
     * @param int|string $userId The ID of the user.
     * @param int|string $roleId The ID or name of the role.
     * @throws UserNotFoundException If the user is not found.
     * @throws RoleNotFoundException If the role is not found.
     * @throws UserAlreadyHasPermissionException If the user already has the specified role.
     * @return bool Returns true if the role is assigned successfully, false otherwise.
     */
    public static function assignRoleToUser(int|string $userId, int|string $roleId): bool
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

    /**
     * Returns an array containing role information based on either
     * the role ID or role name provided as an argument.
     * 
     * @param int roleIdOrName The parameter `roleIdOrName` can accept either an integer or a string
     * value. It represents the ID or name of a role.
     * 
     * @return ?array an array or null.
     */
    private static function getRoleByIdOrName(int|string $roleIdOrName): ?array
    {
        return (is_numeric($roleIdOrName)) ?
            self::getRole($roleIdOrName) :
            self::getRoleByName($roleIdOrName);
    }

    /**
     * Revokes a role from a user in the authorization system.
     * 
     * @param int userId The userId parameter is the identifier of the user from whom you want to
     * revoke a role. It can be either an integer or a string.
     * @param int roleId The roleId parameter is the identifier of the role that you want to revoke
     * from the user. It can be either an integer or a string.
     * 
     * @return bool a boolean value. It returns true if the role is successfully revoked from the user,
     * and false otherwise.
     */
    public static function revokeRoleFromUser(int|string $userId, int|string $roleId): bool
    {
        try {
            $role = (is_numeric($roleId)) ?
                self::getRole($roleId) : self::getRoleByName($roleId);


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

    /**
     * Grants a permission to a role.
     *
     * @param int|string $roleId The ID or name of the role.
     * @param int|string $permissionId The ID or name of the permission.
     */
    public static function grantPermissionToRole(int|string $roleId, int|string $permissionId): void
    {
        $role = (is_numeric($roleId)) ?
            self::getRole($roleId) :
            self::getRoleByName($roleId);

        $permission = (is_numeric($permissionId)) ?
            self::getPermission($permissionId) :
            self::getPermissionByName($permissionId);

        if ($role !== null && $permission !== null) {
            $authorization = new RolePermission();

            $authorization->create(['role_id' => $role['id'], 'permission_id' => $permission['id']]);
        }
    }

    /**
     * Revokes a permission from a role.
     *
     * @param int|string $roleId The ID or name of the role.
     * @param int|string $permissionId The ID or name of the permission.
     */
    public static function revokePermissionFromRole(int|string $roleId, int|string $permissionId): void
    {
        $role = (is_numeric($roleId)) ?
            self::getRole($roleId) :
            self::getRoleByName($roleId);

        $permission = (is_numeric($permissionId)) ?
            self::getPermission($permissionId) :
            self::getPermissionByName($permissionId);

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

    /**
     * Retrieves a role from the database based on its name and returns it
     * as an array, or null if no role is found.
     * 
     * @param string roleName The roleName parameter is a string that represents the name of the role
     * you want to retrieve from the database.
     * 
     * @return ?array an array containing the role that matches the given role name. If no matching
     * role is found, it returns null.
     */
    private static function getRoleByName(string $roleName): ?array
    {
        $result = Role::where('name', $roleName)->get();

        if (!empty($result)) {
            return $result[0];
        }

        return null;
    }

    /**
     * The function checks if a user has a specific role.
     * 
     * @param int|string userId The userId parameter is the identifier of the user for whom we want to check
     * if they have a specific role. It can be either an integer or a string value.
     * @param int|string roleId The `` parameter is the ID of the role that you want to check if the
     * user has.
     * 
     * @return bool a boolean value.
     */
    private static function checkUserHasRole(int|string $userId, int|string $roleId): bool
    {
        return !empty(UserRole::where('user_id', $userId)->where('role_id', $roleId)->get());
    }

    /**
     * Checks if a role has a specific permission.
     *
     * @param int|string $roleId The ID or name of the role.
     * @param int|string $permissionId The ID or name of the permission.
     * @return bool Whether the role has the permission.
     */
    public static function checkRoleHasPermission(int|string $roleId, int|string $permissionId): bool
    {
        return count(
            RolePermission::where('role_id', $roleId)
                ->where('permission_id', $permissionId)
                ->get()
        ) > 0;
    }

    /**
     * Retrieves the roles assigned to a user.
     *
     * @param int|string $userId The ID of the user.
     * @return array An array of roles assigned to the user.
     */
    public static function getUserRoles(int|string $userId): array
    {
        $result = UserRole::where('user_id', $userId)->get();

        $roles = [];

        foreach ($result as $row) {
            $roles[] = self::getRole($row['role_id']);
        }

        return $roles;
    }

    /**
     * Retrieves the permissions assigned to a role.
     *
     * @param int|string $roleId The ID or name of the role.
     * @return array An array of permissions assigned to the role.
     */
    public static function getRolePermissions(int|string $roleId): array
    {
        $result = RolePermission::where('role_id', $roleId)->get();

        $permissions = [];

        foreach ($result as $row) {
            $permissions[] = self::getPermission($row['permission_id']);
        }

        return $permissions;
    }

    /**
     * Returns a Role object based on the given roleId.
     * 
     * @param int|string $roleId The parameter `` is the ID of the role that you want to retrieve from the
     * database.
     * 
     * @return ?Role an instance of the Role class or null if no role is found with the given roleId.
     */
    public static function getRole(int|string $roleId): ?Role
    {
        return Role::find($roleId);
    }

    /**
     * Retrieves a permission object based on the given permission ID.
     * 
     * @param int|string $permissionId The parameter `permissionId` is the ID of the permission that you want to
     * retrieve.
     * 
     * @return ?Permission an instance of the Permission class or null.
     */
    public static function getPermission(int|string $permissionId): ?Permission
    {
        return Permission::find($permissionId);
    }

    /**
     * Creates roles in the system.
     *
     * @param array $roles An array of role names to create.
     * @throws RoleCreationException If role creation fails.
     */
    public static function createRoles(array $roles)
    {
        try {
            $createdRoles = [];

            foreach ($roles as $role) {
                $createdRole = Role::create(['name' => $role]);

                $createdRoles[] = $createdRole;
            }

            foreach ($createdRoles as $role) {
                if ($role === null) {
                    throw new RoleCreationException($role);
                }
            }
        } catch (RoleCreationException | \Throwable $th) {
            ExceptionHandler::handleException($th);
        }
    }

    /**
     * Creates permissions in the system.
     *
     * @param array $permissions An array of permission names to create.
     * @throws PermissionCreationException If permission creation fails.
     */
    public static function createPermissions(array $permissions)
    {
        try {
            $createdPermissions = [];

            foreach ($permissions as $permission) {
                $createdPermission = Permission::create(['name' => $permission]);
                $createdPermissions[] = $createdPermission;
            }

            foreach ($createdPermissions as $permission) {
                if ($permission === null) {
                    throw new PermissionCreationException($permission);
                }
            }
        } catch (PermissionCreationException | \Throwable $th) {
            ExceptionHandler::handleException($th);
        }
    }

    /**
     * Assigns permissions to a role.
     *
     * @param int|string $roleIdOrName The ID or name of the role.
     * @param array $permissionIds An array of permission IDs to assign.
     * @throws RoleNotFoundException If the role is not found.
     * @throws PermissionNotFoundException If a permission is not found.
     */
    public static function assignPermissionsToRole(int|string $roleIdOrName, array $permissionIds)
    {
        try {
            $role = self::getRole($roleIdOrName);

            if ($role === null) {
                throw new RoleNotFoundException($roleIdOrName);
            }

            $roleId = $role->id;

            foreach ($permissionIds as $permissionId) {
                $permission = self::getPermission($permissionId);

                if ($permission === null) {
                    throw new PermissionNotFoundException($permissionId);
                }

                $rolePermissionModel = new RolePermission();
                $rolePermissionModel->role_id = $roleId;
                $rolePermissionModel->permission_id = $permission->id;
                $rolePermissionModel->save();
            }
        } catch (RoleNotFoundException | PermissionNotFoundException | \Throwable $th) {
            ExceptionHandler::handleException($th);
        }
    }

    /**
     * Checks if a user has a specific permission.
     *
     * @param Request $request The HTTP request object.
     * @param string $permissionName The name of the permission.
     * @return bool Whether the user has the permission.
     */
    public static function can(string $permissionName, string $guard = 'web'): bool
    {
        if (!Auth::check($guard)) {
            return false;
        }

        $user = Auth::user($guard);

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

    /**
     * Retrieves a permission object from the database based on its
     * name.
     * 
     * @param string permissionName A string representing the name of the permission you want to
     * retrieve.
     * 
     * @return mixed The permission object or null if the permission is not found.
     */
    private static function getPermissionByName(string $permissionName): mixed
    {
        return Permission::where('name', $permissionName)->first();
    }

    /**
     * Retrieves a user object based on the provided user ID.
     * 
     * @param int|string $userId The userId parameter is the unique identifier of the user that we want to retrieve
     * from the database.
     * 
     * @return User|null The user object or null if the user is not found
     */
    private static function getUser(int|string $userId): ?User
    {
        return User::find($userId);
    }
}
