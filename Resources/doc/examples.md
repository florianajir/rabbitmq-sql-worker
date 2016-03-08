# Examples

## Configuration

Follow the [rabbitmq-sql configuration documentation](configuration.md) 

## Mapping

Follow the [rabbitmq-sql mapping documentation](mapping.md) 

## Start consumer

```bash
php app/console rabbitmq:consumer -w sql
```

> :warning: *when you edit the configuration, be sure to restart consumer(s) to apply changes.*

## Simple Example (without relationship)

### Mapping

Below is an example of a simple User object mapping:

```yml
# app/config/mapping.yml
parameters:
    mapping: 
        User:
            table: members
            fields:
                username:
                    column: name
                    type: string
                    length: 255
                    nullable: false
                mail:
                    column: email
                    type: string
                    length: 255
                    nullable: true
```

### Incoming message

RabbitMQ incoming messages will be consume from this format :

```json
{
    "type": "User",
    "data": "{\"username\": \"Toto\", \"mail\": \"email@example.com\"}"
}
```

### SQL Triggered

```sql
INSERT INTO `members` (`name`, `email`) VALUES("Toto", "email@example.com");
```

## Advanced Mapping Example (relationships)

**Note:** with identifier mapping key set, the consumer will try to update existing records.

#### Mapping
```yml
# app/config/mapping.yml
parameters:
    mapping:
        User:
            table: user
            identifier: sku
            fields:
                sku:
                    column: sku
                    type: string
                    length: 23
                    nullable: false
                name:
                    column: name
                    type: string
                    length: 255
                parent_id:
                    column: parent_id
                    type: int
                    nullable: true
                created_at:
                    column: created_at
                    type: datetime
                    length: 255
            manyToOne:
                Address:
                    targetEntity: Address
                    joinColumn:
                        name: address_id
                        referencedColumnName: id
            oneToOne:
                Customer:
                    targetEntity: Customer
                    joinColumn:
                        name: customer_id
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
        Address:
            table: address
            identifier: sku
            fields:
                sku:
                    column: sku
                    type: string
                    length: 23
                    nullable: false
                postal_code:
                    column: postal_code
                    type: string
                    length: 5
                city:
                    column: city
                    type: string
                    length: 255
        Customer:
            table: customer
            identifier: sku
            fields:
                sku:
                    column: sku
                    type: string
                    length: 23
                    nullable: false
                email:
                    column: email
                    type: string
                    length: 255
                last_purchased:
                    column: last_purchased
                    type: datetime
                    length: 255
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
#### Message data

```json
{
  "sku": "sku_user",
  "name": "user1",
  "Address": {
    "sku": "sku_address",
    "postal_code": "34000",
    "city": "Montpellier"
  },
  "Customer": {
    "sku": "sku_customer",
    "email": "foo@bar.com",
    "last_purchased": "2015-06-26T22:22:00+0200"
  },
  "Groups": [
    {
      "sku": "group1",
      "created_at": "2015-06-01T22:22:00+0200"
    },
    {
      "sku": "group2",
      "created_at": "2015-06-01T22:22:00+0200"
    }
  ],
  "Children": [
    {
      "sku": "sku_child1",
      "name": "child1",
      "created_at": "2015-06-01T22:22:00+0200"
    },
    {
      "sku": "sku_child2",
      "name": "child2",
      "created_at": "2015-06-01T22:22:00+0200"
    }
  ]
}
```

#### SQL Triggered

The program will look if the identifier mapping key is defined and then if record exists to know if it will perform insert or update operations.

##### Non existing records

```sql
INSERT INTO customer (sku, email, last_purchased) VALUES ("sku_customer", "foo@bar.com", "2015-06-26T22:22:00+0200");
INSERT INTO address (sku, postal_code, city) VALUES ("sku_address", "34000", "Montpellier");
INSERT INTO user (sku, name, customer_id, address_id) VALUES ("sku_user", "user1", :customer_id, :address_id);
INSERT INTO user (sku, name, created_at, parent_id) VALUES ("sku_child1", "child1", "2015-06-01T22:22:00+0200", :user_id);
INSERT INTO user (sku, name, created_at, parent_id) VALUES ("sku_child2", "child2", "2015-06-01T22:22:00+0200", :user_id);
DELETE FROM users_groups WHERE user_id = :user_id;
INSERT INTO groups (sku, created_at) VALUES ("group1", "2015-06-01T22:22:00+0200");
INSERT INTO users_groups (user_id, group_id) VALUES (:user_id, :group1_id);
INSERT INTO groups (sku, created_at) VALUES ("group2", "2015-06-01T22:22:00+0200");
INSERT INTO users_groups (user_id, group_id) VALUES (:user_id, :group2_id);
``` 

##### Existing records

```sql
UPDATE customer SET sku = "sku_customer", email = "foo@bar.com", last_purchased = "2015-06-26T22:22:00+0200" WHERE sku = "sku_customer";
UPDATE address SET sku = "sku_address", postal_code = "34000", city = "Montpellier" WHERE sku = "sku_address"
UPDATE user SET sku = "sku_user", name = "user1", customer_id = :customer_id, address_id = :address_id WHERE sku = "sku_user";
UPDATE user SET sku = "sku_child1", name = "child1", created_at = "2015-06-01T22:22:00+0200", parent_id = :user_id WHERE sku = "sku_child1";
UPDATE user SET sku = "sku_child2", name = "child2", created_at = "2015-06-01T22:22:00+0200", parent_id = :user_id WHERE sku = "sku_child2";
DELETE FROM users_groups WHERE user_id = :user_id;
UPDATE groups SET sku = "group1", created_at = "2015-06-01T22:22:00+0200" WHERE sku = "group1";
INSERT INTO users_groups (user_id, group_id) VALUES (:user_id, :group1_id);
UPDATE groups SET sku = "group2", created_at = "2015-06-01T22:22:00+0200" WHERE sku = "group2";
INSERT INTO users_groups (user_id, group_id) VALUES (:user_id, :group2_id);
``` 