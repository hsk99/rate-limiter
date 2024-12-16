<?php

namespace Webman\RateLimiter\Driver;

use Workerman\Timer;

class Memory implements DriverInterface
{
    protected array $data = [];
    protected array $expire = [];

    public function __construct()
    {
        Timer::add(60, function () {
            $this->clearExpire();
        });
    }

    public function increase(string $key, int $ttl = 24*60*60, $step = 1): int
    {
        $expireTime = $this->getExpireTime($ttl);
        $key = "$key-$expireTime-$ttl";
        if (!isset($this->data[$key])) {
            $this->data[$key] = 0;
            $this->expire[$expireTime][$key] = time();
        }
        $this->data[$key] += $step;
        return $this->data[$key];
    }

    protected function getExpireTime($ttl): int
    {
        return  ceil(time()/$ttl) * $ttl;
    }

    protected function clearExpire(): void
    {
        foreach ($this->expire as $expireTime => $keys) {
            if ($expireTime < time()) {
                foreach ($keys as $key => $time) {
                    unset($this->data[$key]);
                }
                unset($this->expire[$expireTime]);
            }
        }
    }
}