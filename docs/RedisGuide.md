## Redis Guide

A Redis client is ready to use using RedisClient class which start a pool of connection to Redis Server.


### Installation of Redis Server
1. Comment lines out in docker-compose.yml

```yml
redis-boiler:
  image: redis:7.0.2-alpine3.16
  container_name: redis-boiler
  restart: always
  ports:
    - 6379:6379
  volumes:
    - redis-data:/data
  networks:
    - app-network
```
2. Then, start the container detached.
```bash
docker-compose up redis-boiler -d
```

3. Config values in .env file
```bash
REDIS_HOST="redis-boiler"
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_SIZE_CONN=128
```

### Use Redis Client

1. Add Redis connection pool to container in order to init connection pool when container starts.
```php
final class ContainerBindingsProvider
{
    private array $services = [
        // ...
        [\Boilerwork\Infra\Persistence\Adapters\Redis\RedisPool::class, 'singleton', null],   // Start Redis Connection Pool to be used by services
    ];
    
    // ...
```
2. Init RedisClient and perform any action that Redis Api allows.
Redis Official Documentation: https://redis.io/docs/

```php
$redisClient = new RedisClient();
$redisClient->getConnection(); // Get connection from Pool.

$redisClient->hSet('SetOneKey', 'hashKey', json_encode(['foo' => 'bar']));
var_dump($redisClient->hGet('SetOneKey', 'hashKey'));

// Or...

$redisClient->set('SimpleKey', 'SimpleValue');
var_dump($redisClient->get('SimpleKey'));

$redisClient->putConnection();  // Connection must be released from pool
```
### Redis Methods
Currently following methods are mapped in RedisClient:
- set https://redis.io/commands/set/
- get https://redis.io/commands/get/
- hSet https://redis.io/commands/hset/ // Good for saving json documents
- hGet https://redis.io/commands/hget/
- hGetAll https://redis.io/commands/hgetall/
- initTransaction and endTransaction (multi and exec in Redis) https://redis.io/docs/manual/transactions/

Any other method can be added to client or used directly through connection object. You are welcome to map new methods that fit your needs.
> Redis commands: https://redis.io/commands/
