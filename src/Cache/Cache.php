<?php

namespace BBC\ProgrammesPagesService\Cache;

use InvalidArgumentException;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class Cache implements CacheInterface
{
    /** @var CacheItemPoolInterface */
    private $cachePool;

    /** @var string */
    private $prefix;

    /** @var bool */
    private $flushCacheItems = false;

    private $defaultCacheTimes = [
        CacheInterface::NONE => -1,
        CacheInterface::SHORT => 60,
        CacheInterface::NORMAL => 300,
        CacheInterface::MEDIUM => 1200,
        CacheInterface::LONG => 7200,
        CacheInterface::X_LONG => 86400,
        CacheInterface::INDEFINITE => 0,
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
        $key = $this->standardiseKey($key);
        if ($this->flushCacheItems) {
            $this->cachePool->deleteItem($key);
        }
        return $this->cachePool->getItem($key);
    }

    /**
     * @param CacheItemInterface $item
     * @param int|string $ttl
     *   TTL in seconds, or a constant from CacheInterface
     * @return bool
     */
    public function setItem(CacheItemInterface $item, $ttl): bool
    {
        $ttl = $this->calculateTtl($ttl);
        $item->expiresAfter($ttl);
        return $this->cachePool->save($item);
    }

    /**
     * IF CALLABLE RETURNS SOMETHING THAT EVALUATES TO EMPTY THE RESULT WILL NOT BE CACHED
     *
     * @param string $key
     * @param int|string $ttl
     *   TTL in seconds, or a constant from CacheInterface
     * @param callable $function
     * @param array|null $arguments
     * @return mixed
     */
    public function getOrSet(string $key, $ttl, callable $function, array $arguments = [])
    {
        $cacheItem = $this->getItem($key);
        if ($cacheItem->isHit() && !$this->flushCacheItems) {
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

    /**
     * Helps you to construct good cache keys by prodding you in the correct direction.
     * Entirely optional but you are encouraged to use it.
     *
     * @param string $className
     * @param string $functionName
     * @param \null[]|\string[] ...$uniqueValues
     * @return string
     */
    public function keyHelper(string $className, string $functionName, ?string ...$uniqueValues): string
    {
        // Please help prevent cache namespace collisions by driving carefully
        $uniqueValues = str_replace('.', '_', array_map(function ($v) {
            return (is_null($v)) ? "" : $v;
        }, $uniqueValues));
        $uniqueValues = preg_replace('!_+!', '_', $uniqueValues);
        $values = [$className, $functionName] + $uniqueValues;
        return join('.', $values);
    }

    public function setFlushCacheItems(bool $flushCacheItems): void
    {
        $this->flushCacheItems = $flushCacheItems;
    }

    /**
     * Attach a prefix to the key and strip anything that's not a valid PSR-6
     * cache key.
     *
     * @param string $key
     * @return mixed
     */
    private function standardiseKey(string $key)
    {
        $key = $this->prefix . '.' . $key;
        return preg_replace('/[^A-Za-z0-9_\.]/', '_', $key);
    }

    /**
     * TTL can be in seconds, or one of the constants from CacheInterface
     * which is converted into the TTL in seconds defined in the constructor.
     * Anything else results in an exception
     *
     * @param int|string $ttl
     * @return int
     */
    private function calculateTtl($ttl): int
    {
        if (is_numeric($ttl)) {
            $ttl = (int) $ttl;
        } elseif (is_string($ttl) && isset($this->cacheTimes[$ttl])) {
            $ttl = $this->cacheTimes[$ttl];
        } else {
            throw new InvalidArgumentException("TTL must be a number or Cache class constant");
        }
        return $this->protectLifetimeFromStampede($ttl);
    }

    /**
     * Fuzz the TTL by a random value in order to avoid caches expiring simultaneously
     * and creating unnecessary server load.
     *
     * @param int $ttl
     * @return int
     */
    private function protectLifetimeFromStampede(int $ttl): int
    {
        $ten = floor($ttl / 10);
        $modifier = rand(0, $ten);
        $modifier = min($modifier, 120);
        return $ttl + $modifier;
    }
}
