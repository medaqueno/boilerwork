# Boilerwork

(In development)

The objective of this repository is to provide a project start point applying some basic patterns.

## Index

1. [Guía de instalación](./docs/InstallationGuide.md)
2. [Descripción de la estructura de carpetas y archivos](./docs/FilesTypesDescription.md)
3. [Pasos para comenzar con un endpoint](./docs/UseGuide.md)
4. [Testing y Análisis](./docs/TestingAndAnalysis.md)
4. [Git Best Practices](./docs/GitBestPractices.md)
5. [Kafka Guide](./docs/KafkaGuide.md)
5. [Redis Guide](./docs/RedisGuide.md)


## Inspiration:

-   CQRS Documents by Greg Young - https://cqrs.files.wordpress.com/2010/11/cqrs_documents.pdf
-   https://wkrzywiec.medium.com/ports-adapters-architecture-on-example-19cab9e93be7
-   https://herbertograca.com/2017/11/16/explicit-architecture-01-ddd-hexagonal-onion-clean-cqrs-how-i-put-it-all-together/
-   https://franiglesias.github.io/tag/good-practices/
-   https://matthiasnoback.nl
-   https://cqrs.wordpress.com/documents/cqrs-and-event-sourcing-synergy/ Lots of info in this site
-   https://lostechies.com/gabrielschenker/2015/05/25/ddd-the-aggregate/
-   https://lostechies.com/gabrielschenker/2015/06/06/event-sourcing-applied-the-aggregate/ and a bunch of articles in this site.
-   Book https://www.amazon.es/Domain-Driven-Design-English-Carlos-Buenosvinos-ebook/dp/B06ZYRPHMC
-   Book Strategic Monoliths and Microservices: Driving Innovation Using Purposeful Architecture - Tomasz Jaskula
-   Book A Philosophy of Software Design - John Ousterhout
-   Hundreds of articles, posts, tweets, comments conversations... and experiences
-   Good Practices: https://beberlei.de/2020/02/25/clean_code_object_calisthenics_rules_i_try_to_follow.html

## Patterns

-   CQRS (Command Query Responsibility Segregation)
-   Domain Driven Design
-   Event Driven
-   Event Sourcing (optional, but recommended)
-   Value Objects
-   Invariants validation/protection through assertion in Domain layer
-   Repositories
-   Specification Pattern

## Features included

-   Swoole blazing fast performance and concurrency through Coroutines [Open Swoole](https://openswoole.com)
-   Dotenv
-   Job Scheduler
-   Websocket client
-   Websocket server
-   HTTP Server - Routing
-   TCP/UDP Server
-   Basic Logging
-   Domain Event Publisher
-   Scheduler to subscribe using a provider to messaging.
-   Dependency Injection Container
-   Command Bus
-   PSR Request and Response Wrappers
-   Projections 
-   Read models 
-   Adapters to Persistence : InMemory , Redis , PostgreSQL 
-   Transactions (completed where apply)
-   Opentelemetry (pending)
-   Database Migrations (pending)
-   Middleware for HTTP Ports
-   Adapter to RabbitMQ: Pub/Sub, Queues (Should be revised)
-   Adapter to Kafka: Pub/Sub, Topics
-   Examples: Commands, Queries, persist In Memory, tests.

## Basic Request Lifecycle

### Command. Write Operation

Client -> request endpoint (POST, PUT, PATCH) -> Routing -> UI Port -> build and trigger Command through Command Bus. -> Handle Command -> Init transaction -> Apply all business logic in Aggregate (invariants, use aggregate services, specifications, domain event creation, update aggregate state) -> persist aggregate events (or Aggregate/Entity if not using ES) -> end transaction -> release/publish domain events. -> Return nothing, void.

Async -> Projection builders subscribers, listen to event publishing -> build projection -> persist projection.

### Query. Read Operation

Client -> request endpoint (GET) -> Routing -> UI Port -> Build Read Model -> Retrieve Projection in Read Model from Persistence -> Map retrieved data -> return Read Model


### Http Routing

### Job Scheduler

### Commands

### Value Objects

### Aggregate

### Events (Internal Subscribers)

### ES

## Naming conventions
