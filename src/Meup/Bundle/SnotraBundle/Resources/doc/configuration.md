# Configuration

Snotra consume a `rabbitMQ` message queue and persist them.

You can configure sql or elasticsearch persistence or both of them.

## RabbitMQ

```yaml
# app/config/parameters.yml
parameters:
    rabbitmq_host:          localhost
    rabbitmq_port:          5672
    rabbitmq_user:          guest
    rabbitmq_password:      guest
    rabbitmq_vhost:         /
    rabbitmq_lazy:          false
    rabbitmq_exchange:      meup_entity_edition
    rabbitmq_exchange_type: direct
    rabbitmq_queue:         snotra_entity_edition
```

## SQL

If you choose to use `sql` worker you will need to fill this parameters: 

```yaml
# app/config/parameters.yml
parameters:
    database_driver:        pdo_mysql
    database_host:          localhost
    database_port:          3306
    database_name:          my_database
    database_user:          my_user
    database_password:      my_password
```

The **mapping file** need to be configured to fit aMQP message to your database structure, this file is located at `app/config/mapping.yml`, you can found more information in [Mapping documentation](sql/mapping.md)

## Elastic-search

To use the `elasticsearch` worker you have to configure the `app/config/elasticsearch` file.

A template file is included yet, adjust it to your needs.
