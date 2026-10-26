<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case SuperAdmin = 'super_admin';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::SuperAdmin => 'Super admin',
        };
    }

    /**
     * Whether this role may change the values of existing settings.
     */
    public function canEditSettingValues(): bool
    {
        return true;
    }

    /**
     * Whether this role may create, edit and delete setting groups and settings.
     */
    public function canManageSettingDefinitions(): bool
    {
        return $this === self::SuperAdmin;
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (Role $role) => [$role->value => $role->label()])
            ->all();
    }
}
