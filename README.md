[![Build Status](https://travis-ci.org/NicolasBadey/symfony-elasticsearch-integration.svg?branch=master)](https://travis-ci.org/NicolasBadey/symfony-elasticsearch-integration)

# symfony-elasticsearch-integration

Symfony Elasticsearch integration with elaticsearch-php library.

This bundle show a full integration of Elasticsearch :
- Search Controller
- ETL with [elasticsearch-php-etl](https://github.com/NicolasBadey/elasticsearch-php-etl)
- SearchForm mapped to a Search DTO (Data Transfer Object)
- a Transformer for build an ES query (as array) from a Search DTO
- an ElasticsearchClient
 
And also use :
- Webpack Encore/Bootstrap 4 for front
- EasyAdmin / CKeditor + ElFinder for admin
- AliceBundle for fixtures
- basic login/logout/register with Guard
- Messenger (WIP asynchronous indexOne)
- tests with PHPUnit/Prophecy/Panther
- TravisCi
- PHPStan/PHPCSFixer

## TODO
- async indexOne with Messenger
- buffered indexOne Subscriber
- VueJs integration sample