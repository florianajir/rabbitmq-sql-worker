Snotra
======

Snotra is a symfony powered application which provides an AMQP worker to consume and persist entities from RabbitMQ messages.
 
Snotra is able to persist data in SQL database or Elasticsearch indexes.

## Installation ##

### For Symfony Framework >= 2.3 ###

Require the bundle and its dependencies with composer:

```bash
$ composer require florianajir/rabbitmq-sql-bundle
```

Register the bundle:

```php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        new FlorianAjir\RabbitMqSqlBundle\FlorianAjirRabbitMqSqlBundle(),
    );
}
```

Enjoy !

## Usage ##

Add the `old_sound_rabbit_mq` section in your configuration file:

```yaml
old_sound_rabbit_mq:
    connections:
        default:
            host:     'localhost'
            port:     5672
            user:     'guest'
            password: 'guest'
            vhost:    '/'
            lazy:     false
            connection_timeout: 3
            read_write_timeout: 3

            # requires php-amqplib v2.4.1+ and PHP5.4+
            keepalive: false

            # requires php-amqplib v2.4.1+
            heartbeat: 0
    producers:
        upload_picture:
            connection:       default
            exchange_options: {name: 'upload-picture', type: direct}
    consumers:
        upload_picture:
            connection:       default
            exchange_options: {name: 'upload-picture', type: direct}
            queue_options:    {name: 'upload-picture'}
            callback:         upload_picture_service
```

Here we configure the connection service and the message endpoints that our application will have. In this example your service container will contain the service `old_sound_rabbit_mq.upload_picture_producer` and `old_sound_rabbit_mq.upload_picture_consumer`. The later expects that there's a service called `upload_picture_service`.


### Important notice - Lazy Connections ###

In a Symfony environment all services are fully bootstrapped for each request, from version >= 2.3 you can declare
a service as lazy ([Lazy Services](http://symfony.com/doc/master/components/dependency_injection/lazy_services.html)).
This bundle still doesn't support new Lazy Services feature but you can set `lazy: true` in your connection
configuration to avoid unnecessary connections to your message broker in every request.
It's extremely recommended to use lazy connections because performance reasons, nevertheless lazy option is disabled
by default to avoid possible breaks in applications already using this bundle.

### Import notice - Heartbeats ###

It's a good idea to set the ```read_write_timeout``` to 2x the heartbeat so your socket will be open. If you don't do this, or use a different multiplier, there's a risk the __consumer__ socket will timeout.

## Producers, Consumers, What? ##

In a messaging application, the process sending messages to the broker is called __producer__ while the process receiving those messages is called __consumer__. In your application you will have several of them that you can list under their respective entries in the configuration.

### Producer ###

A producer will be used to send messages to the server. In the AMQP Model, messages are sent to an __exchange__, this means that in the configuration for a producer you will have to specify the connection options along with the exchange options, which usually will be the name of the exchange and the type of it.

## Workers usage

SQL database persistence using doctrine DBAL:

```bash
php app/console rabbitmq:consumer -w sql
```

The SQL worker provide as well insert as update operations from a configured identifier. It also support "doctrine-like" relationnal table joins.

## Incoming messages

RabbitMQ incoming messages will be consume from this format :

```json
{
    "type": "article",
    "data": "{\"description\": \"json escaped content\"}"
}
```

## Documentation

Read the [project wiki](https://github.com/1001Pharmacies/snotra/wiki) to get more documentation about workers and configuration.
