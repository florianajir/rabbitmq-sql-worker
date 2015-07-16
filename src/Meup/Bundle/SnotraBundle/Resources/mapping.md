# mapping.yml reference (SQL persistence)

The mapping configuration is defined in the `app/config/mapping.yml` file.

## Entities

The entities to persist are declared by their input name under parameters:mapping head lines.

### table

The table name is required for each entities.

```yaml
table: category 
```

### identifier

The identifier is the field name which will be used to identify a record existence to allow update operations. 

This property is optionnal but strongly recommanded to avoid some exceptions. This column in database should have a unique constraint.

```yaml
identifier: sku
```

### fields

Fields are describe by their input name. 

```yaml
fields:
    id:
        ...
    name:
        ...
```

#### column

Required property. The database column where the field must be persisted.

```yaml
column: sku
```

#### type

The field type is used for validation. Possible values: string, int, decimal, date, datetime. (Default: string)

Datetime format ISO8601 example: 2015-07-16T09:47:52+0200

Date format example: 2015-07-16

```yaml
type: datetime
```

#### length

Optional validation property to define the max length of a field. if length exceed an exception is thrown.

#### nullable

Optional boolean validation to define if a field is required or not. (Default: true)

## Association Mapping

This chapter explains mapping associations between objects.
The associations can be one of this: oneToOne, oneToMany, manyToOne, manyToMany.
An entity can be self-referenced.

### manyToOne

A many-to-one association is the most common association between objects.

This association is declared by his input name and must be linked to a declared entity by the targetEntity property.

joinColumn mapping describes the foreign key name and the referenced column name.

```yaml
manyToOne:
    banners:
        targetEntity: banner
        joinColumn:
            name: campaign_id
            referencedColumnName: id
```

### oneToOne

The Many-To-One association is likely the same as the One-To-One.

You can define a self-referencing one-to-one relationships

```yaml
oneToOne:
    user:
        targetEntity: user
        joinColumn:
            name: _id
            referencedColumnName: id
```

### oneToMany

The One-To-Many association is a little more complex by allowing referencing foreign_key from an other table.

```yaml
oneToMany:
    fr:
        targetEntity: product_lang
        joinColumn:
            name: product_id
            referencedColumnName: id
        references:
            lang_id:
                table: lang
                referencedColumnName: id
                where:
                    iso_code: fr
        removeReferenced: true
```
#### references

List of fields to add to new relation records, fetched from an other table with condition declared in the where clause.
In below example, records to insert in the table `product_lang` will have a `lang_id` value corresponding to the id of a `lang` record where `iso_code=fr`.

##### where

A condition declared in that way `column: value`. 
We recommend to set a where clause with unicity result assurance otherwise the first result will be catch.

*To this date, the where property will not accept more than one conditions, feel free to contribute.*

#### removeReferenced

The removeReferenced is a boolean property, if is set to true, all records matching with an `AND` select on references and joinColumn values will be deleted before inserts new data.

### manyToMany

For Many-To-Many associations you declare a join table which contain two foreign keys. You can chose which entity is the owning and which the inverse side.
Real many-to-many associations are less common than others because frequently you want to associate additional attributes with an association, in which case you introduce an association class. 
Consequently, the direct many-to-many association disappears and is replaced by one-to-many/many-to-one associations between the 3 participating classes.

```yaml
manyToMany:
    categories:
        targetEntity: Category
        joinTable:
            name: product_category
            joinColumn:
                name: product_id
                referencedColumnName: id
            inverseJoinColumn:
                name: category_id
                referencedColumnName: id
```
