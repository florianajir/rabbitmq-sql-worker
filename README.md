# Rabbitmq-sql-bundle

## About

The RabbitMqSqlBundle incorporates messaging persistance in your application via RabbitMQ using the php-amqplib/RabbitMqBundle and Doctrine DBAL libraries.

The bundle implements a Doctrine SQL consumer providing amqp message persistance with entity relational support. All you have to do is configure your RabbitMQ and your database and launch your consumer as:

```shellScript
php app/console rabbitmq:consumer -w sql
```

## Examples

The SQL worker provide as well insert as update operations from a configured identifier. It also support "doctrine-like" relationnal table joins.

> Take inspiration from [Examples documentation](Resources/doc/examples.md)

## License

This bundle is under the MIT license. See the complete license in the bundle:

    Resources/meta/LICENSE

## Installation ##

### For Symfony Framework >= 2.3

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
        new OldSound\RabbitMqBundle\OldSoundRabbitMqBundle(),
        new FlorianAjir\RabbitMqSqlBundle\FlorianAjirRabbitMqSqlBundle(),
    );
}
```

## Configuration

You have to configure the rabbitmq and the database and define message structures and database mapping.

* [Rabbitmq-sql Configuration](Resources/doc/configuration.md)
* [Mapping Documentation](Resources/doc/configuration.md)

Enjoy !