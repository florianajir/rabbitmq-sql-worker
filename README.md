Snotra
======

Worker who publishes the content from a RabbitMQ queue to a set of database.

Snotra can be configured to use a set of Elastic Search index as well as MySQL database.

This worker will read AMQP messages containing item data and insert or update in Elastic Search indexes or SQL database.

## Workers usage

Persist data in SQL database (doctrine DBAL):

```bash
php app/console rabbitmq:consumer -w sql
```

Persist data in ElasticSearch index:

```bash
php app/console rabbitmq:consumer -w elasticsearch
```

## Incoming messages

RabbitMQ incoming messages will be consume from this format :

{
    "type": "article",
    "data": "\"description\": \"json escaped content\""
}

## Installation

Just git clone the project ;)

## Documentation

- [SQL Mapping](app/Resources/doc/mapping.md)
- [WIKI](https://github.com/1001Pharmacies/snotra/wiki)