Snotra
======

Snotra is symfony powered project which provide an AMPQ worker to consume/persist entities from RabbitMQ messages to an SQL database or an Elasticsearch index.

The SQL worker provide as well insert as update operations from a configured identifier.

## Workers usage

SQL database persistence using doctrine DBAL:

```bash
php app/console rabbitmq:consumer -w sql
```

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
