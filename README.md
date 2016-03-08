Snotra
======

Snotra is a symfony powered application which provides an AMQP worker to consume and persist entities from RabbitMQ messages.
 
Snotra is able to persist data in SQL database or Elasticsearch indexes.

## Workers usage

SQL database persistence using doctrine DBAL:

```bash
php app/console rabbitmq:consumer -w sql
```

The SQL worker provide as well insert as update operations from a configured identifier. It also support "doctrine-like" relationnal table joins.


ElasticSearch index persistence:

```bash
php app/console rabbitmq:consumer -w elasticsearch
```

## Incoming messages

RabbitMQ incoming messages will be consume from this format :

```json
{
    "type": "article",
    "data": "\"description\": \"json escaped content\""
}
```

## Installation

Just git clone the project ;)

## Documentation

Read the [Snotra project wiki](https://github.com/1001Pharmacies/snotra/wiki) to get more documentation about workers and configuration.
