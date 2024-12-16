<?php

namespace Webman\RateLimiter\Driver;

use support\exception\BusinessException;

class Apcu implements DriverInterface
{
    public function __construct()
    {
        if (!extension_loaded('apcu')) {
            throw new BusinessException('APCu extension is not loaded');
        }

        if (!apcu_enabled()) {
            throw new BusinessException('APCu is not enabled. Please set apc.enable_cli=1 in php.ini to enable APCu.');
        }
    }

    public function increase(string $key, int $ttl = 24*60*60, $step = 1): int
    {
        return apcu_inc("$key-" . $this->getExpireTime($ttl) . '-' . $ttl, $step, $success, $ttl) ?: 0;
    }

    protected function getExpireTime($ttl): int
    {
        return  ceil(time()/$ttl) * $ttl;
    }
}