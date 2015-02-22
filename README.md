# Doctrine-TestSet

Doctrine project have a lot of combination, but there are very few way to test all those combination.
This project aim to define a believable set of entities that convert must of combination.
For each entity there are sample data to test directly.

Created for [javiereguiluz/EasyAdminBundle](https://github.com/javiereguiluz/EasyAdminBundle)

## Test sets

Doctrine DBAL define [19 data types](http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/types.html).
Doctrine ORM define [10 association](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html).

Every entities are fully documented and follow instructions of Doctrine DBAL and Doctrine ORM.

### Ecommerce test set

Fixtures for the Ecommerce test set are mainly based on [Sample Data 1.6.1.0](http://www.magentocommerce.com/knowledge-base/entry/installing-sample-data-archive-for-magento-ce) of [Magento](http://magento.com/).

**Notice** that *every* EAN code are **fake**.

#### Covered types

The Ecommerce test set have **100%** of Doctrine DBAL's type:

 - simple_array
 - datetime
 - datetimetz
 - float
 - string
 - bigint
 - boolean
 - integer
 - text
 - blob
 - binary
 - guid
 - date
 - json_array
 - time
 - array
 - decimal
 - smallint
 - object

#### Covered Associations

The Ecommerce test set have **50%** of Doctrine ORM's association:

 - ManyToMany (bidirectional)
 - OneToOne (unidirectional)
 - ManyToOne (unidirectional)
 - OneToMany (unidirectional)
 - OneToMany (self-referencing)