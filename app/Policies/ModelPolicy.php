<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Str;

class ModelPolicy
{
    protected const EMPLOYEE_ALLOWED_ABILITIES = [
        'aiddistributions.view',
        'aiddistributions.create',
        'aiddistributions.update',
        'aiddistributions.import',
        'bookings.view',
        'bookings.update',
        'bookings.confirm',
        'projects.view',
        'projects.create',
        'projects.update',
        'aiditems.view',
        'aiditems.create',
        'aiditems.update',
    ];

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function __call($name, $arguments)
    {
        $class_name = str_replace('Policy', '', class_basename($this));
        $class_name = Str::plural(Str::lower($class_name));

        if ($name == 'viewAny') {
            $name = 'view';
        }

        $ability = $class_name.'.'.Str::kebab($name);
        $user = $arguments[0];
        if ($user instanceof User) {
            if (
                $user->user_type === 'employee' &&
                in_array($ability, self::EMPLOYEE_ALLOWED_ABILITIES, true)
            ) {
                return true;
            }

            return ($user->roles->where('role_name', $ability)->first() == null) ? false : true;
        }
    }
}
