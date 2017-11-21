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
    const INDEFINITE = 'indefinite';

    public function getItem(string $key): CacheItemInterface;

    public function setItem(CacheItemInterface $item, $value, $ttl): bool;

    public function getOrSet(string $key, $ttl, callable $function, array $arguments = [], $nullTtl = CacheInterface::NONE);

    public function deleteItem(string $key): bool;

    public function setFlushCacheItems(bool $flushCacheItems): void;

    public function keyHelper(string $className, string $functionName, ...$uniqueValues): string;
}
