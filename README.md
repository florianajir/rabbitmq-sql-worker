# Rabbitmq-sql-bundle

## About

The RabbitMqSqlBundle is a symfony bundle to provide rabbitmq message persistence for your application using the php-amqplib/rabbitmq-bundle and doctrine/dbal libraries.

You just need to configure the mapping in yml and execute a command, a simple and scalable rabbitmq to sql consumer to persist your entities:

```shellScript
php app/console rabbitmq:consumer -w sql
```

## Features

* mapping yml config (doctrine like)
* Insert records
* Update records 
* Relational records : oneToOne, oneToMany, manyToOne, manyToMany
* Update, Delete relations
* Foreign keys support

## Examples

Following example shows you the consuming process to persist in database a simple subscriber from an asynchronous message data.

RabbitMQ incoming message data:

```json
{
  "name" : "Rogger Rabbit",
  "email" : "subscriber@acme.corp",
  "Groups": [ { "slug": "subscriber" } ]
}
```

SQL requests output:

```sql
INSERT INTO `members` (`name`, `email`) VALUES ("Rogger Rabbit", "subscriber@acme.corp");
INSERT INTO `member_group` (`member_id`, `group_id`) VALUES (3, 2);
```

> Take more inspiration from [Examples documentation](Resources/doc/examples.md)

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