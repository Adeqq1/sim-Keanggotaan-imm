<?php

namespace App\Enums;

enum RoleEnum: string
{
    case ADMIN = 'admin';
    case KADER = 'kader';
    case INSTRUKTUR = 'instruktur';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::KADER => 'Kader',
            self::INSTRUKTUR => 'Instruktur',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::ADMIN => 'bg-primary',
            self::KADER => 'bg-success',
            self::INSTRUKTUR => 'bg-info text-dark',
        };
    }

    public static function labelFor(?string $role): string
    {
        return self::tryFrom($role ?? self::KADER->value)?->label() ?? self::KADER->label();
    }

    public static function badgeClassFor(?string $role): string
    {
        if ($role === null) {
            return self::KADER->badgeClass();
        }

        return self::tryFrom($role)?->badgeClass() ?? 'bg-secondary';
    }
}
