# Mapping reference

To use an SQL persistence, you have to setup a mapping file.

The mapping configuration is defined in the `app/config/mapping.yml` file. To begin you can copy and rename `mapping.yml.dist` example file.

## Entity

The entities to persist are defined by their input names under the parameters:mapping blocs.

### table

**Required**. The database table name where will have to save entity.

```yaml
table: category 
```

### identifier

The identifier is the field name which will be used to check an existing record to be able to effectuate update operations.

In database, the identifier should be attached to a unicity constraint.

**This property is optionnal but strongly recommanded** to avoid some exceptions (for example if a field like id with unicity constraint in database is present in record to re-send).

```yaml
identifier: sku
```

### fields

Field list by input name of entity attributes. Allows to map a field to a column with different name as well as declare validation rules.

```yaml
fields:
    sku:
        column: sku
        type: string
        length: 23
        nullable: false
    name:
        column: name
```

#### column

**Required**. The database column where the field have to be persisted.

```yaml
column: sku
```

#### type

The field type is used for validation. 

Possible values: *string*, *int*, *decimal*, *date*, *datetime*. (Default: *string*)

Datetime fields have to be under format *ISO8601*, example: 2015-07-16T09:47:52+0200

Date fields have to be under format yyyy-mm-dd, example: 2015-07-16

```yaml
type: datetime
```

The default string format is the more permissive, it's like no validation, for other formats if value no match to declared format, an exception will be thrown.

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

The many-to-one association is likely the same as the One-To-One.

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

The one-to-many association is a little more complex by allowing referencing foreign_key from an other table.

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

For many-to-many associations you declare a join table which contain two foreign keys. You can chose which entity is the owning and which the inverse side.
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
