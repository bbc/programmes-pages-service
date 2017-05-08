<?php

namespace BBC\ProgrammesPagesService\Cache;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class Cache implements CacheInterface
{
    /** @var CacheItemPoolInterface */
    private $cachePool;

    /** @var string */
    private $prefix;

    /** @var bool */
    private $flushCache = false;

    private $defaultCacheTimes = [
        CacheInterface::NONE => -1,
        CacheInterface::SHORT => 60,
        CacheInterface::NORMAL => 300,
        CacheInterface::MEDIUM => 1200,
        CacheInterface::LONG => 7200,
        CacheInterface::X_LONG => 86400
    ];

    /** @var array */
    private $cacheTimes;

    public function __construct(
        CacheItemPoolInterface $cachePool,
        string $prefix,
        array $cacheTimes = []
    ) {
        $this->cachePool = $cachePool;
        $this->prefix = $prefix;
        $this->cacheTimes = array_merge($this->defaultCacheTimes, $cacheTimes);
    }

    public function getItem(string $key): CacheItemInterface
    {
        $key = $this->createKey($key);
        if ($this->flushCache) {
            $this->cachePool->deleteItem($key);
        }
        return $this->cachePool->getItem($key);
    }

    public function setItem($ttl, CacheItemInterface $item): bool
    {
        $ttl = $this->calculateTtl($ttl);
        $item->expiresAfter($ttl);
        return $this->cachePool->save($item);
    }

    /**
     * IF CALLABLE RETURNS SOMETHING THAT EVALUATES TO EMPTY THE RESULT WILL NOT BE CACHED
     *
     * @param string $key
     * @param int|class constant $ttl
     * @param callable $function
     * @param array|null $arguments
     * @return mixed
     */
    public function getOrSet(string $key, $ttl, callable $function, array $arguments = null)
    {
        $cacheItem = $this->getItem($key);
        if ($cacheItem->isHit() && !$this->flushCache) {
            return $cacheItem->get();
        }
        $ttl = $this->calculateTtl($ttl);
        $result = call_user_func_array($function, $arguments);
        if (!empty($result)) {
            $cacheItem->set($result);
            $cacheItem->expiresAfter($ttl);
            $this->cachePool->save($cacheItem);
        }
        return $result;
    }

    public function keyHelper(string $className, string $functionName, string ...$uniqueValues): string
    {
        // Please help prevent cache namespace collisions by driving carefully
        $uniqueValues = str_replace('.', '_', $uniqueValues);
        $values = [$className, $functionName] + $uniqueValues;
        return join('.', $values);
    }

    public function setFlushCache(bool $flushCache): void
    {
        $this->flushCache = $flushCache;
    }

    private function createKey(string $key)
    {
        $key = $this->prefix . '.' . $key;
        return preg_replace('/[^A-Za-z0-9_\.]/', '_', $key);
    }

    private function calculateTtl($ttl): int
    {
        if (is_numeric($ttl)) {
            $ttl = (int)$ttl;
        } elseif (is_string($ttl) && isset($this->cacheTimes[$ttl])) {
            $ttl = $this->cacheTimes[$ttl];
        } else {
            throw new \InvalidArgumentException("TTL must be a number or Cache class constant");
        }
        return $this->protectLifetimeFromStampede($ttl);
    }

    private function protectLifetimeFromStampede(int $ttl)
    {
        $ten = floor($ttl / 10);
        $modifier = rand(0, $ten);
        $modifier = min($modifier, 120);
        return $ttl + $modifier;
    }
}
