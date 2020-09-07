<?php


namespace App\Services\Cache;


use Symfony\Component\Cache\Adapter\RedisAdapter;

class RedisCacheFactory
{
    public static function createConnection($dsn, array $options = [])
    {
        $redis = RedisAdapter::createConnection($dsn, $options);
        $redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_JSON);
        $redis->setOption(\Redis::OPT_PREFIX, '');

        return $redis;
    }
}