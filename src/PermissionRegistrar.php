<?php

namespace Larapacks\Authorization;

use PDOException;
use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Auth\Access\Gate;

class PermissionRegistrar
{
    /**
     * The auth gate.
     *
     * @var Gate
     */
    protected $gate;

    /**
     * The cache manager.
     *
     * @var CacheManager
     */
    protected $cache;

    /**
     * PermissionRegistrar constructor.
     *
     * @param Gate         $gate
     * @param CacheManager $manager
     */
    public function __construct(Gate $gate, CacheManager $manager)
    {
        $this->gate = $gate;
        $this->cache = $manager;
    }

    /**
     * Registers permissions into the gate.
     *
     * @return void
     */
    public function register()
    {
        // Dynamically register permissions with Laravel's Gate.
        foreach ($this->getPermissions() as $permission) {
            $this->gate->define($permission->name, function ($user) use ($permission) {
                return $user->hasPermission($permission);
            });
        }
    }

    /**
     * Flushes the permission cache.
     *
     * @return void
     */
    public function flushCache()
    {
        $this->cache->forget(Authorization::cacheKey());
    }

    /**
     * Fetch the collection of site permissions.
     *
     * @return \Illuminate\Database\Eloquent\Collection|array
     */
    protected function getPermissions()
    {
        try {
            return $this->cache->remember(Authorization::cacheKey(), Authorization::cacheExpiresIn(), function () {
                Authorization::permission()->get();
            });
        } catch (PDOException $e) {
            // We catch PDOExceptions here in case the developer
            // hasn't migrated authorization tables yet.
        }

        return [];
    }
}
