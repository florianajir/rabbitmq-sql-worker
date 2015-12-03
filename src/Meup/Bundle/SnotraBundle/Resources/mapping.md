# Mapping reference

To use an SQL persistence, you have to setup a mapping file.

The mapping configuration is defined in the `app/config/mapping.yml` file. To begin you can copy and rename `mapping.yml.dist` example file.

## Entity

The entities to persist are defined by their input names under the parameters:mapping blocs.

### table

**Required if no `discriminator` property**. The database table name where will have to save entity.

### discriminator

**Required if no `table` property**. The discriminator property (sometimes named dtype) is an optionnal mapping property which can be use instead of table property. 
His role is to indicate in which table persist the entity depending upon column value.

Example:

```yaml
# app/mapping.yml
parameters:
    mapping:
        supplier:
            discriminator: dtype
```

If snotra receive this message:

```json
{
  "sku":"1234567",
  "name": "naturamind",
  "dtype":"brand"
}
```

This will trigger an insert/update sql operation in the table named `brand`

### identifier

The identifier is the field name which will be used to check an existing record to be able to effectuate update operations.

In database, the identifier should be attached to a unicity constraint.

> **This property is optionnal but strongly recommanded** to avoid some exceptions (for example if a field like id with unicity constraint in database is present in record to re-send).

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
    enabled:
        column: enabled
        fixed: 1
```

#### column

**Required**. The database column where the field have to be persisted.

#### type

The field type is used for validation. 

Possible values: *string*, *int*, *decimal*, *date*, *datetime*. (Default: *string*)

Datetime fields have to be under format *ISO8601*, example: 2015-07-16T09:47:52+0200

Date fields have to be under format yyyy-mm-dd, example: 2015-07-16

The default string format is the more permissive, it's like no validation, for other formats if value no match to declared format, an exception will be thrown.

#### length

Optional validation property to define the max length of a field. if length exceed an exception is thrown.

#### nullable

Optional boolean validation to define if a field is required or not. (Default: true)

#### value

Option to define a fixed value for the field. If defined, the value will replace the received data. (Default: null)

## Association Mapping

This chapter explains mapping associations between objects.
The associations can be one of this: oneToOne, oneToMany, manyToOne, manyToMany.

**An entity can be self-referenced.**

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
    cart:
        targetEntity: cart
        joinColumn:
            name: user_id
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
        removeReferenced: true
        references:
            lang_id:
                table: lang
                referencedColumnName: id
                where:
                    iso_code: fr
```

#### removeReferenced

The removeReferenced is a boolean property, if is set to true, all records matching with an `AND` select on references and joinColumn values will be deleted before inserts new data.

#### references

List of fields to add to new relation records, fetched from an other table with condition declared in the where clause.
In below example, records to insert in the table `product_lang` will have a `lang_id` value corresponding to the id of a `lang` record where `iso_code=fr`.

##### table

The database table where to find the foreign value.

##### referencedColumnName

The database column where to fetch the foreign value.

##### where

A condition declared in that way `column: value`.

We recommend to set a where clause with unicity result assurance otherwise the first result will be catch.

*To this date, the where property will not accept more than one conditions, feel free to contribute.*

### manyToMany

For many-to-many associations you declare a join table which contain two foreign keys. 

You can chose which entity is the owning and which the inverse side.

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

> Real many-to-many associations are less common than others because frequently you want to associate additional attributes with an association, in which case you introduce an association class.
Consequently, the direct many-to-many association disappears and is replaced by one-to-many/many-to-one associations between the 3 participating classes.
