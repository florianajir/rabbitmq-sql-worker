# Configuration

## RabbitMQ

```yaml
# app/config/config.yml
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
    consumers:
        sql:
            connection:         default
            exchange_options:
                name:           'my_exchange'
                type:           direct
            queue_options:
                name:           'my_exchange'
            callback:           rabbitmq_sql.sql_consumer
```

### Import notice - Heartbeats ###

It's a good idea to set the ```read_write_timeout``` to 2x the heartbeat so your socket will be open. If you don't do this, or use a different multiplier, there's a risk the __consumer__ socket will timeout.

## Doctrine DBAL (SQL config)

```yaml
# app/config/config.yml
doctrine:
    dbal:
        driver:   pdo_mysql
        dbname:   my_database
        user:     root
        password: null
        charset:  UTF8
        server_version: 5.6
```

## Database Mapping

Define the `rabbitmq_sql.mapping` parameter to match with your AMQP message and database structures, more information in [Rabbitmq-sql Mapping Documentation](mapping.md)

Example:

```yaml
# app/config/parameters.yml
parameters:
    rabbitmq_ignored_types: ["trash"]
    rabbitmq_sql.mapping:
        User:
            table: user
            identifier: sku
            fields:
                sku:
                    column: sku
                    type: string
                    length: 23
                    nullable: false
                parent_id:
                    column: parent_id
                    type: int
                updated_at:
                    column: updated_at
                    type: datetime
            manyToOne:
                Sponsor:
                    targetEntity: User
                    joinColumn:
                        name: sponsor_id
                        referencedColumnName: id
            manyToMany:
                Groups:
                    targetEntity: Group
                    joinTable:
                        name: users_groups
                        joinColumn:
                            name: user_id
                            referencedColumnName: id
                        inverseJoinColumn:
                            name: group_id
                            referencedColumnName: id
            oneToMany:
                Children:
                    targetEntity: User
                    joinColumn:
                        name: parent_id
                        referencedColumnName: id
        Group:
            table: groups
            identifier: sku
            fields:
                sku:
                    column: sku
                    type: string
                    length: 23
                    nullable: false
                created_at:
                    column: created_at
                    type: datetime
                    length: 255
```

