<?php
namespace BBC\ProgrammesPagesService\Cache;

use Psr\Cache\CacheItemInterface;

interface CacheInterface
{
    const NONE = 'none';
    const SHORT = 'short';
    const NORMAL = 'normal';
    const MEDIUM = 'medium';
    const LONG = 'long';
    const X_LONG = 'xlong';

    public function getItem(string $key): CacheItemInterface;

    public function setItem($ttl, CacheItemInterface $item): bool;

    public function getOrSet(string $key, $ttl, callable $function, array $arguments = null);

    public function setFlushCache(bool $flushCache): void;
}
