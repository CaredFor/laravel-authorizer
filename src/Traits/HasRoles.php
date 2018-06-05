<?php

namespace Benwilkins\Authorizer\Traits;

use Benwilkins\Authorizer\AuthorizerFacade as Authorizer;
use \Benwilkins\Authorizer\Contracts\Role;
use Benwilkins\Authorizer\Exceptions\RoleNotGranted;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;

trait HasRoles
{
    public function roles(): MorphToMany
    {
        return $this->morphToMany(
            Authorizer::getClass('role'),
            'entity',
            config('authorizer.tables.roles_assigned')
        )->withPivot('team_id');
    }

    /**
     * @param string|Role $role
     * @param string|int|\Illuminate\Database\Eloquent\Model $team
     * @return self
     */
    public function grantRole($role, $team = null): self
    {
        $this->roles()->save($this->getSavedRole($role), ['team_id' => $this->getTeamForRole($team)]);

        return $this;
    }

    /**
     * @param string|Role $role
     * @param string|int|\Illuminate\Database\Eloquent\Model $team
     * @return self
     */
    public function revokeRole($role, $team = null): self
    {
        $teamId = $this->getTeamForRole($team);
        $role = $this->getSavedRole($role);

        if (!$this->roles->contains(function ($item, $key) use ($role, $teamId) {
            return $item->id === $role->id && $item->pivot->team_id == $teamId;
        })) {
            throw RoleNotGranted::create($role->handle, $teamId);
        }

        $this->roles()->wherePivot('team_id', $teamId)->detach($role);

        return $this;
    }

    /**
     * Determines if the model has been granted a role.
     *
     * @param string|Role $role The role handle or model
     * @param string|int|\Illuminate\Database\Eloquent\Model $team
     * @return bool
     */
    public function isRole($role, $team = null): bool
    {
        $team = $this->getTeamForRole($team);

        return $this->roles->contains(function ($item, $key) use ($role, $team) {
            if (is_string($role)) { // role handle
                return ($item->handle === $role && (is_null($item->pivot->team_id) || $item->pivot->team_id == $team));

            } else { // Role model
                return ($item->id === $role->id && (!$item->pivot->team_id || $item->pivot->team_id == $team));
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

        return $query->whereHas('roles', function($query) use ($role) {
            $query->where(config('authorizer.tables.roles').'.id', $role->id);
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