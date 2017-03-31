Programmes Pages Service
========================

A library for powering /programmes pages.

Uses Doctrine to read data from the ProgrammesDB.

Installation
-----

Add this repository to your composer.json and add `bbc/programmes-pages-service`
as a dependency

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:bbc/rmp-php-radionav-client.git"
        }
    ],
    "require": {
        "bbc/programmes-pages-service": "*"
    }
}
```

### Symfony Setup

Add the `doctrine/doctrine-bundle`, `doctrine/doctrine-cache-bundle` and 
`` bundles to your composer.json.

Add the following types to your Doctrine DBAL config (in config.yml), under the
`dbal` key:

```yaml
doctrine:
    dbal:
        types:
            date_partial: BBC\ProgrammesPagesService\Data\ProgrammesDb\Type\DatePartialType
```

Add the following entity mapping and filters to your Doctring ORM config (in
config.yml), under the `orm` key:

```yaml
doctrine:
    orm:
        mappings:
            ProgrammesPagesService:
                type: annotation
                dir: "%kernel.root_dir%/../vendor/bbc/programmes-pages-service/src/Data/ProgrammesDb/Entity"
                is_bundle: false
                prefix: BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity
        filters:
            embargoed_filter:
                class: BBC\ProgrammesPagesService\Data\ProgrammesDb\Filter\EmbargoedFilter
                enabled: true
        dql:
            string_functions:
                MATCH_AGAINST: BBC\ProgrammesPagesService\Data\ProgrammesDb\Functions\MatchAgainst
                GROUP_CONCAT: BBC\ProgrammesPagesService\Data\ProgrammesDb\Functions\GroupConcat
            datetime_functions:
                YEAR: BBC\ProgrammesPagesService\Data\ProgrammesDb\Functions\Year
                MONTH: BBC\ProgrammesPagesService\Data\ProgrammesDb\Functions\Month
                DAY: BBC\ProgrammesPagesService\Data\ProgrammesDb\Functions\Day
```

Add the doctrine extensions configuration (in config.yml), under the
`stof_doctrine_extensions` key:

```yaml
stof_doctrine_extensions:
    orm:
        default:
            tree: true
            timestampable: true
```

Define services in the DI container to swiftly get at our(in
services.yml):

```yaml
services:
    pps.mapper_factory:
        class: BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\MapperFactory
        public: false

    pps.domain_service_factory:
        class: BBC\ProgrammesPagesService\Service\ServiceFactory
        arguments:
            - '@doctrine.orm.default_entity_manager'
            - '@pps.mapper_factory'

    pps.programmes_service:
        class: BBC\ProgrammesPagesService\Service\ProgrammesService
        factory: [ '@pps.domain_service_factory', 'getProgrammesService']
```

You can repeat the `pps.programmes_service` definition, changing the
'getProgrammesService' method for each of the services you want to access, to
save you having to request it through the ServiceFactory every time.


Usage
-----

Services make requests to the database layer then transforms the database
objects into a set of Domain Objects.

TODO.

Development
-----------

Install development dependencies with `composer install`.

Run tests and code sniffer with `script/test`.


License
-------

This repository is available under the terms of the Apache 2.0 license.
View the LICENSE file for more information.

Copyright (c) 2017 BBC
