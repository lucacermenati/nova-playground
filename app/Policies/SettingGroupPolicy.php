<?php

namespace App\Policies;

use App\Models\User;
use LucaCermenati\NovaSettings\Models\SettingGroup;

/**
 * Only super admins may create, change or delete setting groups (tabs).
 */
class SettingGroupPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canManageSettingDefinitions();
    }

    public function view(User $user, SettingGroup $group): bool
    {
        return $user->canManageSettingDefinitions();
    }

    public function create(User $user): bool
    {
        return $user->canManageSettingDefinitions();
    }

    public function update(User $user, SettingGroup $group): bool
    {
        return $user->canManageSettingDefinitions();
    }

    public function delete(User $user, SettingGroup $group): bool
    {
        return $user->canManageSettingDefinitions();
    }
}
