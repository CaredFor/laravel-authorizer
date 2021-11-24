<?php

namespace Benwilkins\Authorizer\Traits;

use Benwilkins\Authorizer\AuthorizerFacade as Authorizer;
use \Benwilkins\Authorizer\Contracts\Role;
use Benwilkins\Authorizer\Exceptions\RoleNotGranted;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;
use Carbon\Carbon;

trait HasRoles
{
    /**
     * Boot up the HasRoles trait
     */
    public static function bootHasRoles()
    {
        /**
         * Tap into the eloquent booted event so we can add the extra observable events.
         */
        app('events')->listen('eloquent.booted: ' . static::class, function (Model $model) {
            $model->addObservableEvents(['roleGranted', 'roleRevoked']);
        });
    }

    public static function roleGranted($callback)
    {
        static::registerModelEvent('roleGranted', $callback);
    }

    public static function roleRevoked($callback)
    {
        static::registerModelEvent('roleRevoked', $callback);
    }

    public function roles(): MorphToMany
    {
        return $this->morphToMany(
            Authorizer::getClass('role'),
            'entity',
            config('authorizer.tables.roles_assigned')
        )
            ->whereNull('deleted_at')
            ->withTimestamps()
            ->withPivot('team_id', 'facility_id', 'deleted_at');
    }

    public function rolesWithTrashed(): MorphToMany
    {
        return $this->morphToMany(
            Authorizer::getClass('role'),
            'entity',
            config('authorizer.tables.roles_assigned')
        )
            ->withTimestamps()
            ->withPivot('team_id', 'facility_id', 'deleted_at');
    }

    /**
     * @param string|Role $role
     * @param string|int|\Illuminate\Database\Eloquent\Model $team
     * @param string|null $facilityId
     * @return self
     * @throws RoleNotGranted
     */
    public function grantRole($role, $team = null, $facilityId = null): self
    {
        $this->roles()->save($this->getSavedRole($role), ['team_id' => $this->getTeamForRole($team), 'facility_id' => $facilityId]);
        $this->fireModelEvent('roleGranted', false);

        return $this;
    }

    /**
     * @param string|Role $role
     * @param string|int|\Illuminate\Database\Eloquent\Model $team
     * @param string|null $facilityId
     * @return self
     * @throws RoleNotGranted
     */
    public function revokeRole($role, $team = null, $facilityId = null): self
    {
        // Need observer to catch
        // when deleting, update deleted_at and return false.
        $teamId = $this->getTeamForRole($team);
        $role = $this->getSavedRole($role);

        if (!$this->roles->contains(function ($item, $key) use ($role, $teamId, $facilityId) {
            if ($teamId) {
                return $item->id === $role->id && $item->pivot->team_id == $teamId;
            } else {
                return $item->id === $role->id && $item->pivot->facility_id === $facilityId;
            }
        })) {
            throw RoleNotGranted::create($role->handle, $teamId);
        }

        if ($teamId) {
            $this->roles()->wherePivot('team_id', $teamId)->detach($role);
        } else {
            $this->roles()->wherePivot('team_id', $teamId)->wherePivot('facility_id', $facilityId)->detach($role);
        }

        $this->fireModelEvent('roleRevoked', false);

        return $this;
    }

    /**
     * Determines if the model has been granted a role.
     *
     * @param string|Role $role The role handle or model
     * @param string|int|\Illuminate\Database\Eloquent\Model $team
     * @param string|null $facilityId
     * @return bool
     */
    public function isRole($role, $team = null, $facilityId = null): bool
    {
        $team = $this->getTeamForRole($team);

        return $this->roles->contains(function ($item, $key) use ($role, $team, $facilityId) {
            if (is_string($role)) { // role handle
                return ($item->handle === $role
                    && (is_null($item->pivot->team_id) || $item->pivot->team_id == $team)
                    && (is_null($item->pivot->facility_id) || $item->pivot->facility_id == $facilityId));

            } else { // Role model
                return ($item->id === $role->id
                    && (!$item->pivot->team_id || $item->pivot->team_id == $team)
                    && (!$item->pivot->facility_id || $item->pivot->facility_id == $facilityId));
            }
        });
    }

    /**
     * @param Builder $query
     * @param string|Role $role
     * @return Builder
     */
    public function scopeOfRole(Builder $query, $role): Builder
    {
        if (is_string($role)) {
            $role = Authorizer::getClass('role')::findByHandle($role);
        }

        return $query->whereHas('roles', function ($query) use ($role) {
            $query->where(config('authorizer.tables.roles') . '.id', $role->id);
        });
    }

    /**
     * @param $roles
     * @return mixed
     */
    protected function getSavedRole($roles)
    {
        $class = Authorizer::getClass('role');

        if (is_string($roles)) {
            return $class::findByHandle($roles);
        }

        if (is_array($roles)) {
            return $class::whereIn('handle', $roles)->get();
        }

        return $roles;
    }

    /**
     * @param $team
     * @return int|string
     */
    protected function getTeamForRole($team)
    {
        if ($team) {
            $team = (is_string($team) || is_int($team))
                ? $team
                : $team->id;
        }

        return $team;
    }
}
