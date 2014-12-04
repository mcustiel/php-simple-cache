php-simple-cache
================

What is it
==========

php-simple-cache is a PHP Cache library with the minimum functionalities needed that abstracts different cache mechanisms. It is thought to be performant, flexible and easy to use without the need of a containing framework.

Currently the cache systems abstracted by php-simple-cache are:
* **Serialized files ("file" driver)**: Uses files saved in a directory containing the serialized data.
* **Memcache ("memcache" driver)**: Uses php's memcache extension through \Memcache class to access a memcached server.
* **Redis ("phpredis" driver)**: Uses php's phpredis extension through \Redis class

This library is thought to be used with or without a dependency injection system, that's why the constructor of each driver allows the injection of it's dependencies. If you don't use a dependency injection system, maybe you just want to use a provided factory class that instantiates each driver by it's name, but you need to call init method to configure the driver after it's instantiated.

Installation
============

###Download code
This library supports PSR-4 so you can just download the code and map your autoloader to use it.

###Composer
php-simple-config also supports composer, it isn't yet published into packagist but it could be downloaded using something like this:

```javascript
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/mcustiel/php-simple-cache"
        }
    ],
    "require": {
        "mcustiel/php-simple-cache": "dev-master"
    }
}
```

How to use it
=============

###Driver instantiation

There's a variety of ways to instantiate the drivers, as an example **memcache** driver is used:

####Simple instantiation

```php
$cacheManager = new \Mcustiel\SimpleCache\Drivers\memcache\Cache();
$config = new \stdClass();
$config->host = 'yourmemcached.host.com';
$config->port = 11211;
$config->timeout = 1;
$cacheManager->init($config);
```
Or with your own Memcache object (this technique could also be used with a dependency injection system.

```php
$connection = new \Memcache();
$connection->connect();
$cacheManager = new \Mcustiel\SimpleCache\Drivers\memcache\Cache($connection);
// No init() call required here.
```

####Using provided factory class

```php
use \Mcustiel\SimpleCache\SimpleCache;

$factory = new SimpleCache();
$cacheManager = $factory->getCacheManager('memcache');
$config = new \stdClass();
$config->host = 'yourmemcached.host.com';
$config->port = 11211;
$config->timeout = 1;
$cacheManager->init($config);
```

###Caching and retrieving data

Using php-simple-cache for caching data and retrieve it after is really simple, the idea is (as in most cache systems) to check if data is cached, if not generate the data and cache it.

```php
use \Mcustiel\SimpleCache\Types\Key;

$key = new Key('cached-data-key');
$data = $cacheManager->get($key);
if ($data === null) {
	$data = someHeavyProcessThatGeneratesDataThatCanBeCached();
	$cacheManager->set($key, $data);
}
```

###Removing cached data

When there is cached data that isn't needed to be cached anymore, just use the delete method:

```php
use \Mcustiel\SimpleCache\Types\Key;

$key = new Key('cached-data-key');
$cacheManager->delete($key);
```

###Driver-specific configurations

Each driver has a unique configuration specification, the best way to avoid this is to inject the dependencies into each driver but, if you need to use it the init() method, these are the config parameter for each driver:

#### Memcache

```php
$config = new \stdClass()
$config->host = 'yourmemcached.host.com';
$config->port = 11211;
$config->timeoutInSeconds = 1;
```

#### Phpredis

```php
$config = new \stdClass()
$config->host = 'yourredis.host.com';
$config->port = 6379;
$config->timeoutInSeconds = 1;
$config->password = 'yourRedisPasswd';
$config->database = 'dbName';
```

#### File

```php
$config = new \stdClass()
$config->filesPath = '/path/to/cache/files';
```

### Considerations

Please have in mind that each driver is different in it's implementation. There's no much difference between phpredis and memcache drivers, but file driver does not auto expire cache.
For the file case I added support for timeout but that makes it a slow cache, so for the future I have as a TODO, to create a "garbage collector" script that processes expired files and remove the logic to delete expired files in get method. 
Use file driver just in very rare cases in which you don't have access to memcache or redis.
