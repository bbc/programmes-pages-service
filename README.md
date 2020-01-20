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
        default_entity_manager: default
        entity_managers:
            default:
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

Define services in the DI container to swiftly get at them (in services.yml):

```yaml
services:
    # MapperFactory takes an array of options to configure how to map entities
    pps.mapper_factory:
        class: BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\MapperFactory
        public: false
        arguments:
            - []

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


#### Timezone support
By default, Doctrine2 assumes all DateTimes are in UTC.  
You can force all dates going in to the DB to be converted to UTC and all DateTimes coming out to have UTC set by adding the following configuration to your YAML files.
```yaml
doctrine:
    dbal:
        types:
            datetime: BBC\ProgrammesPagesService\Data\ProgrammesDb\Type\UtcDateTimeType
            datetimetz: BBC\ProgrammesPagesService\Data\ProgrammesDb\Type\UtcDateTimeType
```

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
View the [LICENSE file](LICENSE) for more information.

Copyright (c) 2017 BBC


New Yaml
--------

in services.yml

```yaml

framework:
    cache:
        pools:
            cache.programmes:
                adapter: 'cache.adapter.psr6'
                provider: cache.null_provider
services:
    cache.null_provider:
        class: Symfony\Component\Cache\Adapter\NullAdapter

    BBC\ProgrammesCachingLibrary\Cache:
        arguments:
            - '@cache.null_provider'
            - 'nullcache'

#    BBC\ProgrammesCachingLibrary\Cache:
#        arguments: ['@cache.programmes', 'programmes-frontend.%cosmos_component_release%']

    BBC\ProgrammesCachingLibrary\CacheWithResilience:
        arguments:
            - '@logger'
            - '@cache.programmes'
            - 'programmes-frontend.%cosmos_component_release%'
            - '%app.cache.resilient_cache_time%'
            - []
            - ['Doctrine\DBAL\Exception\DriverException']

    BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\MapperFactory: ~

    BBC\ProgrammesPagesService\Service\ServiceFactory:
        public: true
        autowire: true
        arguments:
            - '@doctrine.orm.faucet_entity_manager'
            - '@BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\MapperFactory'
            - '@BBC\ProgrammesCachingLibrary\Cache'

    BBC\ProgrammesPagesService\Service\SegmentsService:
        public: true
        factory: ['@BBC\ProgrammesPagesService\Service\ServiceFactory', 'getSegmentsService']


    BBC\ProgrammesPagesService\Service\SegmentEventsService:
        public: true
        factory: ['@BBC\ProgrammesPagesService\Service\ServiceFactory', 'getSegmentEventsService']

    BBC\ProgrammesPagesService\Service\AtozTitlesService:
        public: true
        factory: ['@BBC\ProgrammesPagesService\Service\ServiceFactory', 'getAtozTitlesService']

    BBC\ProgrammesPagesService\Service\CategoriesService:
        public: true
        factory: ['@BBC\ProgrammesPagesService\Service\ServiceFactory', 'getCategoriesService']

    BBC\ProgrammesPagesService\Service\BroadcastsService:
        public: true
        factory: ['@BBC\ProgrammesPagesService\Service\ServiceFactory', 'getBroadcastsService']

    BBC\ProgrammesPagesService\Service\CollapsedBroadcastsService:
        public: true
        factory: ['@BBC\ProgrammesPagesService\Service\ServiceFactory', 'getCollapsedBroadcastsService']

    BBC\ProgrammesPagesService\Service\ContributorsService:
        public: true
        factory: ['@BBC\ProgrammesPagesService\Service\ServiceFactory', 'getContributorsService']

    BBC\ProgrammesPagesService\Service\ContributionsService:
        public: true
        factory: ['@BBC\ProgrammesPagesService\Service\ServiceFactory', 'getContributionsService']

    BBC\ProgrammesPagesService\Service\CoreEntitiesService:
        public: true
        factory: ['@BBC\ProgrammesPagesService\Service\ServiceFactory', 'getCoreEntitiesService']

    BBC\ProgrammesPagesService\Service\GroupsService:
        public: true
        factory: ['@BBC\ProgrammesPagesService\Service\ServiceFactory', 'getGroupsService']

    BBC\ProgrammesPagesService\Service\ImagesService:
        public: true
        factory: ['@BBC\ProgrammesPagesService\Service\ServiceFactory', 'getImagesService']

    BBC\ProgrammesPagesService\Service\NetworksService:
        public: true
        factory: ['@BBC\ProgrammesPagesService\Service\ServiceFactory', 'getNetworksService']

    BBC\ProgrammesPagesService\Service\MasterBrandsService:
        public: true
        factory: ['@BBC\ProgrammesPagesService\Service\ServiceFactory', 'getMasterBrandsService']

    BBC\ProgrammesPagesService\Service\PodcastsService:
        public: true
        factory: ['@BBC\ProgrammesPagesService\Service\ServiceFactory', 'getPodcastsService']

    BBC\ProgrammesPagesService\Service\ProgrammesService:
        public: true
        factory: ['@BBC\ProgrammesPagesService\Service\ServiceFactory', 'getProgrammesService']

    BBC\ProgrammesPagesService\Service\ProgrammesAggregationService:
        public: true
        factory: ['@BBC\ProgrammesPagesService\Service\ServiceFactory', 'getProgrammesAggregationService']

    BBC\ProgrammesPagesService\Service\PromotionsService:
        public: true
        factory: ['@BBC\ProgrammesPagesService\Service\ServiceFactory', 'getPromotionsService']

    BBC\ProgrammesPagesService\Service\RelatedLinksService:
        public: true
        factory: ['@BBC\ProgrammesPagesService\Service\ServiceFactory', 'getRelatedLinksService']

    BBC\ProgrammesPagesService\Service\ServicesService:
        public: true
        factory: ['@BBC\ProgrammesPagesService\Service\ServiceFactory', 'getServicesService']

    BBC\ProgrammesPagesService\Service\VersionsService:
        public: true
        factory: ['@BBC\ProgrammesPagesService\Service\ServiceFactory', 'getVersionsService']

```
