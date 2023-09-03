<?php

namespace App\Console\Commands;

require_once __DIR__ . '/../../../config/env.php';

use Illuminate\Console\Command;
use Lib\Connection\Connection;
use Lib\Http\ErrorHandler;
use Symfony\Component\Console\Helper\Table;

class ListPermissionsCommand extends Command
{
    protected $signature = 'permission:list';

    protected $description = 'List all roles and their permissions';

    public function handle()
    {
        $connection = new Connection();
        $connection = $connection->getConnection();

        try {
            $roles = $connection->query("SELECT * FROM roles");
            $permissions = $connection->query("SELECT * FROM permissions");
            $rolePermissions = $connection->query("SELECT r.name AS role_name, p.name AS permission_name
                                                  FROM roles r
                                                  LEFT JOIN role_permissions rp ON r.id = rp.role_id
                                                  LEFT JOIN permissions p ON rp.permission_id = p.id");

            $this->displayRolesTable($roles);
            $this->displayPermissionsTable($permissions);
            $this->displayRolePermissionsTable($rolePermissions);
        } catch (\Throwable $th) {
            ErrorHandler::renderError(500, 'Internal server error', $th->getMessage());
        }
    }

    private function displayRolesTable($roles)
    {
        $headers = ['Roles'];
        $rows = [];

        while ($role = $roles->fetch_assoc()) {
            $rows[] = [$role['name']];
        }

        $table = new Table($this->output);
        $table->setHeaders($headers)->setRows($rows);
        $table->render();
        $this->output->writeln('');
    }

    private function displayPermissionsTable($permissions)
    {
        $headers = ['Permissions'];
        $rows = [];

        while ($permission = $permissions->fetch_assoc()) {
            $rows[] = [$permission['name']];
        }

        $table = new Table($this->output);
        $table->setHeaders($headers)->setRows($rows);
        $table->render();
        $this->output->writeln('');
    }

    private function displayRolePermissionsTable($rolePermissions)
    {
        $headers = ['Role', 'Permissions'];
        $rows = [];

        $currentRole = null;
        $currentPermissions = [];

        while ($row = $rolePermissions->fetch_assoc()) {
            $role = $row['role_name'];
            $permission = $row['permission_name'];

            if ($role !== $currentRole) {
                if ($currentRole !== null) {
                    $rows[] = [$currentRole, implode(", ", $currentPermissions)];
                }

                $currentRole = $role;
                $currentPermissions = [];
            }

            $currentPermissions[] = $permission;
        }

        if ($currentRole !== null) {
            $rows[] = [$currentRole, implode(", ", $currentPermissions)];
        }

        $table = new Table($this->output);
        $table->setHeaders($headers)->setRows($rows);
        $table->render();
        $this->output->writeln('');
    }
}
