# Boilerwork

(In development)

The objective of this repository is to provide a project start point applying some basic patterns.

### Inspiration:

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

### Patterns

-   CQRS (Command Query Responsibility Segregation)
-   Domain Driven Design
-   Event Driven (recommended)
-   Event Sourcing (optional, and recommended)
-   Value Objects
-   Invariants validation/protection through assertion in Domain layer
-   Repositories
-   Specification Pattern

### Features included

-   Swoole blazing fast performance and concurrency through Coroutines [Open Swoole](https://openswoole.com)
-   Dotenv
-   Job Scheduler
-   Websocket client
-   Websocket server
-   HTTP Server - Routing
-   TCP/UDP Server
-   Basic Logging
-   Domain Event Publisher
-   Subscribe to internal Domain Events
-   Dependency Injection Container
-   Command Bus
-   PSR Request and Response Wrappers
-   Projections (pending)
-   Read models (pending)
-   Adapters to Persistence (pending): InMemory (completed), Redis (completed),, PostgreSQL (completed), Mongo, MySQL.
-   Transactions (completed where apply)
-   Opentelemetry (pending)
-   Database Migrations (pending)
-   Middleware (pending, maybe will not be included)
-   Adapter to RabbitMQ: Pub/Sub, Queues
-   Scheduler to subscribe using a provider to messaging. Queues/exchanges -> Classes/subscribers
-   Examples: Commands, Queries, persist In Memory, tests.

### Basic Request Lifecycle

#### Command. Write Operation

Client -> request endpoint (POST, PUT, PATCH) -> Routing -> UI Port -> build and trigger Command through Command Bus. -> Handle Command -> Init transaction -> Apply all business logic in Aggregate (invariants, use aggregate services, specifications, domain event creation, update aggregate state) -> persist aggregate events (or Aggregate/Entity if not using ES) -> end transaction -> release/publish domain events. -> Return nothing, void.

Async -> Projection builders subscribers, listen to event publishing -> build projection -> persist projection.

#### Query. Read Operation

Client -> request endpoint (GET) -> Routing -> UI Port -> Build Read Model -> Retrieve Projection in Read Model from Persistence -> Map retrieved data -> return Read Model

#### Http Routing

#### Job Scheduler

#### Commands

#### Value Objects

#### Aggregate

#### Events (Internal Subscribers)

#### ES

## Naming conventions

## Tests

PhpUnit tests may be included in src/tests folder. deminy/counit is installed as dev dependency if needed to allow some specific tests under Swoole.
Run:

```sh
./vendor/bin/phpunit --color --testdox tests
```

A pre-commit git hook exists, and has been moved to git repository. Execute (only works if git > 2.9.0):

```sh
git config --local core.hooksPath .githooks/
```

## QA

```sh
vendor/bin/phpcs --standard=PSR12 --parallel=4 -p app
vendor/bin/phpstan analyse app tests --level=9
vendor/bin/parallel-lint -j 4 --exclude .git --exclude vendor ./
```
