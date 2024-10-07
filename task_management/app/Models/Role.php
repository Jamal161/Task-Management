<?php

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// Creating roles
$adminRole = Role::create(['name' => 'admin']);
$userRole = Role::create(['name' => 'user']);

// Creating permissions
$editArticles = Permission::create(['name' => 'edit articles']);

// Assigning permission to role
$adminRole->givePermissionTo($editArticles);

// Assigning role to a user
$user = User::find(1); // Assuming you have a user with ID 1
$user->assignRole('admin');
