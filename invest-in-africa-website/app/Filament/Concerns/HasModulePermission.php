<?php

namespace App\Filament\Concerns;

use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/**
 * Resource access driven by the role / module matrix of §8.3
 * (see App\Enums\Role::canManage). Each resource declares its module.
 */
trait HasModulePermission
{
    abstract public static function module(): string;

    /** Keep labels as written (French typography: no Title Case). */
    public static function hasTitleCaseModelLabel(): bool
    {
        return false;
    }

    public static function getAuthorizationResponse(string|UnitEnum $action, ?Model $record = null): Response
    {
        return auth()->user()?->canManage(static::module())
            ? Response::allow()
            : Response::deny();
    }
}
