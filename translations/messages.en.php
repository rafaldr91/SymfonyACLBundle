<?php
//test

return [
    'messages' => [
        'role_exists' => 'Role with the given name already exists'
    ],
    'tooltips' => [
        'permissions' => 'Edit Permissions'
    ],
    'buttons'  => [
        'back_to_list' => 'Back to list',
        'add_role'     => 'Add new role'
    ],
    'dt'       => [
        'roles' => [
            'columns' => [
                'name'    => 'Role Name',
                'slug'    => 'System name',
                'actions' => 'Actions',
            ]
        ],
    ],
    'pages'    => [
        'titles'               => [
            'add_role'              => "Create a new role",
            'roles'                 => "User Roles",
            'edit_role_permissions' => 'Edit Permissions'
        ],
        'role'                 => [
            'labels'       => [
                'name' => 'Name'
            ],
            'placeholders' => [
                'name' => 'Role name (must be unique)'
            ]
        ],
        'acl_role_permissions' => [
            'forms' => [
                'acl' => [
                    'group_title' => 'ACL Management',
                    'create'      => 'Create',
                    'read'        => 'Viewing',
                    'update'      => 'Updating',
                    'delete'      => 'Delete',
                    'permissions' => 'Permission management',
                ]
            ]
        ]
    ],


];
