<?php

namespace App\Policies;

use App\Models\User;
use LucaCermenati\NovaSettings\Models\Setting;

/**
 * Only super admins may create, change or delete setting definitions.
 * Changing a setting's value happens on the settings tool page, not here.
 */
class SettingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canManageSettingDefinitions();
    }

    public function view(User $user, Setting $setting): bool
    {
        return $user->canManageSettingDefinitions();
    }

    public function create(User $user): bool
    {
        return $user->canManageSettingDefinitions();
    }

    public function update(User $user, Setting $setting): bool
    {
        return $user->canManageSettingDefinitions();
    }

    public function delete(User $user, Setting $setting): bool
    {
        return $user->canManageSettingDefinitions();
    }
}
