<?php

namespace Webman\RateLimiter\Driver;

use RedisException;
use support\Redis as RedisClient;
use Workerman\Timer;

class Redis implements DriverInterface
{
    /**
     * @param string $connection
     * @throws RedisException
     */
    public function __construct(protected string $connection)
    {
        Timer::add(24 * 60 * 60, function () {
            $this->clearExpire();
        });
        $this->clearExpire();
    }

    /**
     * @throws RedisException
     */
    public function increase(string $key, int $ttl = 24 * 60 * 60, $step = 1): int
    {
        return RedisClient::connection($this->connection)->hIncrBy('rate-limiter-' . date('Y-m-d'), "$key-" . $this->getExpireTime($ttl) . '-' . $ttl, $step) ?: 0;
    }

    /**
     * @param $ttl
     * @return int
     */
    protected function getExpireTime($ttl): int
    {
        return ceil(time() / $ttl) * $ttl;
    }

    /**
     * @return void
     * @throws RedisException
     */
    protected function clearExpire(): void
    {
        $keys = RedisClient::connection($this->connection)->keys('rate-limiter-*');
        foreach ($keys as $key) {
            if (!str_contains($key, 'rate-limiter-' . date('Y-m-d'))) {
                RedisClient::connection($this->connection)->del($key);
            }
        }
    }
}