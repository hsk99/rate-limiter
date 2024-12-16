<?php

namespace Webman\RateLimiter\Annotation;

use Attribute;

#[Attribute]
class RateLimiter
{
    /**
     * IP
     */
    const IP = 'ip';

    /**
     * UID
     */
    const UID = 'uid';

    /**
     * SID
     */
    const SID = 'sid';

    /**
     * RateLimiter constructor.
     * @param int $limit
     * @param int $ttl
     * @param mixed $key
     * @param string $message
     */
    public function __construct(public int $limit, public int $ttl = 1, public mixed $key = 'ip', public string $message = 'Too Many Requests') {}
}