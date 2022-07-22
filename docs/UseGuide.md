## Pasos iniciales a tener en cuenta al comenzar un proyecto.

-   Enumerar y aislar qué acciones y/o eventos vamos a necesitar realizar, qué acciones nos pide negocio. Esto debe realizarse junto a negocio y UX.
-   Definir y valorar qué dominio/s y contextos van a intervernir y dónde encajan esas acciones.
-   https://franiglesias.github.io/the-way-to-ddd-1/
-   https://franiglesias.github.io/the-way-to-ddd-2/
-   https://wkrzywiec.medium.com/ports-adapters-architecture-on-example-19cab9e93be7

    ![DDD](https://res.cloudinary.com/practicaldev/image/fetch/s--Q9i0UONi--/c_limit%2Cf_auto%2Cfl_progressive%2Cq_auto%2Cw_880/https://dev-to-uploads.s3.amazonaws.com/uploads/articles/uwu96thjcto0vj3b3j2a.jpg)

---

## Pasos para crear un endpoint que ejecute un comando (modificar el estado del sistema)


La estructura de carpetas es la siguiente:
```
.
├─ project_service                                   => Root Project Folder
├── docker                                           => Docker related configs
├── docs                                             => Documention about project
└── src                                              => Source Code
    ├── app                                          => Application specific code. All Business logic lies here
    │   ├── Core      
    │   │   ├── BoundedContext                       => Bounded Context
    │   │   │   ├── Application
    │   │   │   │   ├── DomainName                   => Application layer 
    │   │   │   │   │   ├── Jobs                     => Background Jobs
    │   │   │   │   │   ├── Services                 => Helper services
    │   │   │   │   │   ├── CommandFiles.php         => Commands
    │   │   │   │   │   └── CommandHandlerFiles.php  => Command handlers
    │   │   │   │   │
    │   │   │   ├── Domain
    │   │   │   │   ├── DomainName                   => Domain layer 
    │   │   │   │   │   ├── Events                   => Events
    │   │   │   │   │   ├── Services                 => Helper services
    │   │   │   │   │   ├── ValueObjects             => Value objects relative to this domain
    │   │   │   │   │   ├── Aggregate-Entity.php
    │   │   │   │   │   └── Repository.php           => Repository Interface
    │   │   │   │   │
    │   │   │   ├── Infra                            => Infrastructure layer. Include Adapters to external services.
    │   │   │   │   ├── Mappers                      => Mappers/Transformers data from/to external Services or UI
    │   │   │   │   ├── Persistence                  => Repository implementations, SQL, NoSQL, InMemory, etc.
    │   │   │   │   └── Projections                  => Projections store data in read models
    │   │   │   │  
    │   │   │   └── UI                               => User Interface Layer (Also named Delivery)
    │   │   │       ├── Ports                        => Controllers that receive external data.
    │   │   │       │   ├── Http
    │   │   │       │   │   ├── DomainName
    │   │   │       │   │   └── OtherDomainName
    │   │   │       │   └── CLI
    │   │   │       └── ViewModels                   => Retrieve data from Read Models
    │   │   └── Shared                               => Shared files across Bounded Contexts and Domains
    │   │  
    │   └── Shared                                  => Shared files used by Application
    │       └── Providers                           => ContainerBindings, Jobs, Messaging. 
    │     
    ├── bootstrap                    => Files that start up the application.
    ├── logs                       
    ├── migrations                   => Dumps and files that create persistence schemas and DBs
    ├── public                       
    ├── routes                       => HTTP and Websocket Endpoint Mapping routes
    ├── tests                        => Test files. Should be structured in the same way that app/Core
    └── vendor
````


### 1. Crear Contexto y Dominios

Nombrar el directorio del contexto en el que vamos a trabajar en **app/Core/`<NombreDeContexto>`** del que colgarán las diferentes capas de la aplicación.

### 2. Crear puerto de entrada.

Crear el puerto de entrada HTTP como punto de partida de la operación que se va a realizar. Siempre serán clases invocables para garantizar que solo realizan una única acción.

**app/Core/BC/UI/Ports/Http/ExamplePort.php**

```php
#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\UI\Ports\Http\ExampleDomain;

use App\Core\BC\Application\ExampleCommand\ExampleDomain;
use Boilerwork\System\Http\AbstractHttpPort;
use Boilerwork\System\Http\Request;
use Boilerwork\System\Http\Response;
use Psr\Http\Message\ResponseInterface;

final class ExamplePort extends AbstractHttpPort
{
    public function __invoke(Request $request, array $vars): ResponseInterface
    {
    }
}
```

> El objeto _Request_ contiene todos los datos enviados por el cliente, y _$vars_ los valores de la url que puedan ser dinámicos (PUT /item/`{ID}`/modify)

### 3. Añadir una entrada en el router HTTP:

**routes/httpApi.php**

Cada entrada del router contiene un array con:

```
['<HttpMethod>', '<relativeRoute>', [<ArrayOfPermissionStrings>], ['<ArrayOfFullyQualifiedNameMiddlewareClasses>']]
```

```php
use App\Core\BC\UI\Ports\Http\ExampleDomain\ExamplePort;

return [
    // [METHOD', 'URI', 'TARGET_CLASS', [PERMISSIONS], [MIDDLEWARE]]

    ['POST', '/example', ExamplePort::class, ['Public'], []],
];
```

### 4. Crear comando

Crear el comando para la acción que queremos realizar incluyendo los atributos estrictamente necesarios para completarse en el constructor (siempre readonly, un comando es inmutable). Evitar en la medida de lo posible incluir atributos opcionales, y por supuesto nunca realizar acciones distintas dependiendo del contenido del request.

**app/Core/BC/Application/ExampleDomain/ExampleCommand.php**

```php
#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Application\ExampleDomain;

use Boilerwork\Application\CommandInterface;

/**
 * @see \App\Core\BC\Application\ExampleDomain\ExampleCommandHandler
 */
final class ExampleCommand implements CommandInterface
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $region,
    ) {
    }
}
```

> Como norma general, los ID, no los generará el backend, serán enviados por el propio cliente bajo un formato estándar (UUID v4). El cliente es quien indica qué se hace y con qué datos. Esto le permite funcionar de forma asíncrona y no necesitar una respuesta del backend.

### 5. Enviar comando al _CommandBus_

Instanciamos el comando recien creado en el Port y lo envíamos al _CommandBus_ para su ejecución. También incluimos la respuesta.

```php
#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\UI\Ports\Http\ExampleDomain;

use App\Core\BC\Application\ExampleDomain\ExampleCommand;
use Boilerwork\System\Http\AbstractHttpPort;
use Boilerwork\System\Http\Request;
use Boilerwork\System\Http\Response;
use Psr\Http\Message\ResponseInterface;

final class ExamplePort extends AbstractHttpPort
{
    public function __invoke(Request $request, array $vars): ResponseInterface
    {
        $this->command()->handle(
            new ExampleCommand(
                exampleId: $request->input('id'),
                name: $request->input('name'),
                region: $request->input('region'),
            ),
        );

        return Response::empty(202); // 202 Accepted (it is async)
    }
}
```

> Un comando NUNCA devuelve datos en caso de éxito. Si la operación fracasa, una excepción será lanzada en el sistema e incluirá las razones del error. Puede existir alguna excepción en la que un comando devuelva datos, pero debe estar muy justificada, ejemplo típico: Autenticación de segundo paso.

> En caso de requerirse, se puede utilizar $this->command()->syncHandle para esperar a la respuesta síncrona del servidor.

### 6. Crear CommandHandler

Crear el _CommandHandler_ en la capa aplicación que se encargará de orquestar las acciones a realizar.

**app/Core/BC/Application/ExampleDomain/ExampleCommandHandler.php**

```php
#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Application\ExampleDomain;

use Boilerwork\Application\CommandHandlerInterface;
use Boilerwork\Application\CommandInterface;

/**
 * @see App\Core\BC\Application\ExampleDomain\ExampleCommand
 **/
final class ExampleCommandHandler implements CommandHandlerInterface
{
    public function handle(CommandInterface $command): void
    {

    }
}
```

### 7. Crear Agregado

Creamos el agregado o entidad. Un agregado es el encargado de restaurar, mantener, modificar y validar su propio estado.
Un agregado es una transacción, y todo aquello que se modifique en esa transacción, debe formar parte de él.

```php
#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Domain\ExampleDomain;

final class Example extends AggregateRoot
{
    private Name $name;
    private Region $region;

    public static function create(
        string $exampleId,
        string $name,
        string $region,
    ): self {

        // Aquí haríamos aserciones para comprobar que se cumplen las reglas de negocio del agregado si fuera necesario.

        // Build Aggregate
        $example = new static(
            aggregateId: new Identity($exampleId),
        );

        $example->raise(
            new ExampleWasCreated(
                exampleId: (new Identity($exampleId))->toPrimitive(),
                name: (new Name($name))->toPrimitive(),
                region: (new Region($region))->toPrimitive(),
            )
        );

        return $example;
    }

    private function __construct(
        protected readonly Identity $aggregateId,
    ) {
    }
}

```

En este caso, la transacción a realizar es registrar un usuario. Este proceso crea una identidad nueva en el sistema, utilizamos un método estático para ello que devuelve una nueva instancia de sí mismo con la información recibida. No hacemos un new Agregado(), eso no sería explícito con la acción a realizar.

### 8. Crear Evento

Toda transacción finaliza en un evento que indica al sistema qué ha ocurrido. En este caso un usuario se ha registrado: ExampleWasCreated. Creamos ese archivo de evento que por convención requiere una serie de métodos a incluir. El evento en la gran mayoría de los casos incluirá los mismos atributos que el comando. Ya que por lógica indica lo que ha ocurrido y con qué datos. Un evento al ser algo ocurrido en el "pasado" es inmutable.

**app/Core/BC/Domain/ExampleDomain/Events/ExampleWasCreated.php**

```php
#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Domain\ExampleDomain\Events;

use Boilerwork\Domain\AbstractEvent;

final class ExampleWasCreated extends AbstractEvent
{
     protected string $topic = "example-was-created"; // always kebap-case

    public function __construct(
        public readonly string $exampleId,
        public readonly string $name,
        public readonly string $region,
    ) {
    }

    public function getAggregateId(): string
    {
        return $this->exampleId;
    }

    public function serialize(): array
    {
        return $this->wrapSerialize(
            data: [
                'exampleId' => $this->exampleId,
                'name' => $this->name,
                'region' => $this->region,
            ]
        );
    }

    public static function unserialize(array $event): self
    {
        return (new static(
            exampleId: $event['data']['exampleId'],
            name: $event['data']['name'],
            region: $event['data']['region'],
        ));
    }
    
}
```

### 9. Emitir evento en el agregado.

Ya tenemos el evento, así que lo instanciamos en método correspondiente del agregado y levantamos para su publicación posterior.

El evento al emitirse se aplica al agregado modificando su estado de facto. Con lo que además añadimos un método con la siguiente estructura por convención: apply`<NombreDelEvento>`

```php
use App\Core\BC\Domain\ExampleDomain\Events\ExampleWasCreated;
use App\Core\BC\Domain\ExampleDomain\ValueObjects\Name;
use App\Core\BC\Domain\ExampleDomain\ValueObjects\Region;
use Boilerwork\Domain\AggregateRoot;
use Boilerwork\Domain\Assert;
use Boilerwork\Domain\IsEventSourced;
use Boilerwork\Domain\IsEventSourcedTrait;
use Boilerwork\Domain\TracksEvents;
use Boilerwork\Domain\TracksEventsTrait;
use Boilerwork\Domain\ValueObjects\Identity;

final class Example extends AggregateRoot implements TracksEvents, IsEventSourced
{
    use TracksEventsTrait, IsEventSourcedTrait;

    private Name $name;
    private Region $region;

    public static function create(
        string $exampleId,
        string $name,
        string $region,
    ): self {

        // Aquí haríamos aserciones para comprobar que se cumplen las reglas de negocio del agregado si fuera necesario.

        // Build Aggregate
        $example = new static(
            aggregateId: new Identity($exampleId),
        );

        $example->raise(
            new ExampleWasCreated(
                exampleId: (new Identity($exampleId))->toPrimitive(),
                name: (new Name($name))->toPrimitive(),
                region: (new Region($region))->toPrimitive(),
            )
        );

        return $example;
    }

    // Este método se llama automáticamente después de ejecutar $example->raise()
    protected function applyExampleWasCreated(ExampleWasCreated $event): void
    {
        $this->exampleId = new Identity($event->exampleId);
        $this->name = new Name($event->name);
        $this->region = new Region($event->region);
    }

    private function __construct(
        protected readonly Identity $aggregateId,
    ) {
    }
```

Como se puede observar, cada atributo es en realidad un ValueObject. La razón de que sea así y no primitivas es que cada ValueObject contiene sus propias reglas de negocio y validación y asegura que el dato siempre sea válido y por extensión, el estado del sistema también lo será. Desacoplando y centralizando esa responsabilidad en sí mismos.
A continuación el ValueObject Name como ejemplo:

**app/Core/BC/Domain/ExampleDomain/ValueObjects/Name.php**

```php
#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Domain\ExampleDomain\ValueObjects;

use Boilerwork\Domain\ValueObjects\ValueObject;
use Boilerwork\Domain\Assert;

/**
 * @internal
 **/
final class Name extends ValueObject
{
    public function __construct(
        private string $value
    ) {
        // Ejecutamos las aserciones necesarias para asegurar la validez de su estado.
        // los dos últimos atributos de cada comprobación son el "texto para humanos" y el código del error específico a devolver en la excepción de validación que se dispara que ayudará a su identificación en logs y respuestas.
        Assert::lazy()->tryAll()
            ->that($value)
            ->string('Value must be a string', 'exampleName.invalidType')
            ->maxLength(32, 'Value must be 32 characters length', 'exampleName.invalidLength')
            ->verifyNow();
    }

    // Método por conveniencia que nos permite comparar ValueObjects. Un VO carece de ID por lo que puede ser necesario
    // en un momento dado compararse con otro.
    public function equals(ValueObject $object): bool
    {
        return $this->value === $object->value && $object instanceof self;
    }

    // Método por conveniencia que devuelve siempre el valor del Value Object como primitiva.
    public function toPrimitive(): string
    {
        return $this->value;
    }
}

```

> Un ValueObject puede también contener métodos para devolver o validar las distintas partes que lo compongan, pero nunca para manipularlo después de haber sido creado, es inmutable. Si queremos que tenga otros valores instanciaremos un nuevo ValueObject.
> Un Value Object puede normalizar un valor en su constructor, por ejemplo convirtiendo el valor recibido a mayúsculas.
> También puede contener los posibles valores a modo de Enum/estáticos que permite en caso de requerirlo.

> La documentación relativa a la librería de aserciones se encuentra en: https://github.com/beberlei/assert

### 10. Invocar la acción recien creada en el CommandHandler

Ya tenemos los métodos listos en el agregado. Así que lo llamamos desde el handler.

```php
#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Application\ExampleDomain;

use Boilerwork\Application\CommandHandlerInterface;
use Boilerwork\Application\CommandInterface;

/**
 * @see App\Core\BC\Application\ExampleDomain\ExampleCommand
 **/
final class ExampleCommandHandler implements CommandHandlerInterface
{
    public function handle(CommandInterface $command): void
    {
        $example = Example::create(
            exampleId: $command->exampleId,
            name: $command->name,
            region: $command->region,
        );
    }
}
```

Ya tenemos el agregado **$example** en memoria en el estado final deseado. Desde este momento todas las operaciones y lógica de negocio que garantiza la consistencia de los datos ha finalizado. A partir de aquí podemos realizar las sucesivas operaciones que queramos con $example: persistencia, envío a sistemas externos, etc.

### 11. Persistencia. Creación de contratos/interfaces.

Creamos un contrato/interfaz de este dominio para indicar qué operaciones estarán permitidas. En este ejemplo vamos a utilizar EventSourcing, solo necesitamos dos operaciones: Insertar (Eventos) y recuperar el historial de Eventos (lo que nos permitirá reconstruir el último estado más actualizado de $example en otras transacciones diferentes).
Extendemos el interfaz EventStore que nos indica lo necesario para realizar EventSourcing.

**app/Core/BC/Domain/ExampleDomain/ExampleRepository.php**

```php
#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Domain\ExampleDomain;

use Boilerwork\Domain\IsEventSourced;
use Boilerwork\Domain\ValueObjects\Identity;
use Boilerwork\Infra\Persistence\EventStore;

interface ExampleRepository extends EventStore 
{
    public function reconstituteHistoryFor(Identity $id): Example|IsEventSourced; // Return union types to accomplish interface and IDE typehinting
}
```

### 12. Inyectar Contrato/Interfaz.

Inyectamos el interfaz en el handler. Y añadimos la operación a realizar en este caso la inserción: Append.

```php
final class ExampleCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private ExampleRepository $exampleRepository
    ) {
    }

    public function handle(CommandInterface $command): void
    {
        $example = Example::create(
            exampleId: $command->exampleId,
            name: $command->name,
            region: $command->region,
        );

        $this->exampleRepository->append($example);
    }
}
```

### 13. Implementación del interfaz. Creación del repositorio.

Necesitamos implementar el interfaz, para la persistencia que hayamos elegido, por ejemplo, guardar en PostgreSQL. 
La base de desarrollo, tiene ya preparada esta implementación del patrón _EventSourcing_ y que será siempre igual para todos los agregados y dominios teniendo ya en cuenta _versionados_ y posibles _race conditions_.

**app/Core/BC/Infra/Persistence/ExamplePostgreSQLRepository.php**

```php
#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Infra\Persistence;

use App\Core\BC\Domain\ExampleDomain\ExampleRepository;
use Boilerwork\Infra\Persistence\Adapters\PostgreSQL\PostgreSQLEventStoreAdapter;

final class ExamplePostgreSQLRepository extends PostgreSQLEventStoreAdapter implements ExampleRepository
{
}
```

> En un entorno más tradicional tipo CRUD, implementaríamos los create, read, update, delete. O nombres de operaciones concretas que contienen por ejemplo las queries necesarias. (Existen los siguientes en la base: PostgreSQLReadsClient, PostgreSQLWritesClient y RedisClient)

### 14. Bind Interfaz <-> Repositorio en el contenedor.

Mediante inyección de dependencias hacemos un _binding_ del interfaz **ExampleRepository** con **ExamplePostgreSQLRepository**. Todos estos bindings se realizan en:

**app/Shared/Providers/ContainerBindingsProvider.php**

```php
#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Shared\Providers;

use App\Core\BC\Domain\ExampleDomain\ExampleRepository;
use App\Core\BC\Infra\Persistence\ExamplePostgreSQLRepository;

final class ContainerBindingsProvider
{
    private array $services = [
        [ExampleRepository::class, 'bind', ExamplePostgreSQLRepository::class],
        //
        // Default system bindings
        [\Boilerwork\System\Messaging\MessagingClientInterface::class, 'bind', \Boilerwork\System\Messaging\Adapters\KafkaMessageClientAdapter::class],
        [\Boilerwork\Infra\Persistence\Adapters\PostgreSQL\PostgreSQLWritesPool::class, 'singleton', null], // Start PostgreSQL Connection Pools Read and Writes to be used by services
        [\Boilerwork\Infra\Persistence\Adapters\PostgreSQL\PostgreSQLReadsPool::class, 'singleton', null], // Start PostgreSQL Connection Pools Read and Writes to be used by services
    ];

    // .....
```

> Si queremos cambiar la implementación, modificamos el binding sin necesidad de editar la capa de dominio o aplicación.

### 15. Publicación de los eventos emitidos.

Por último y una vez la persistencia ha sido completada correctamente, informamos al resto del sistema de los eventos que han ocurrido desde el handler.

```php
final class ExampleCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private ExampleRepository $exampleRepository
    ) {
    }

    public function handle(CommandInterface $command): void
    {
        $example = Example::create(
            exampleId: $command->exampleId,
            name: $command->name,
            region: $command->region,
        );

        $this->exampleRepository->append($example);
        
        eventsPublisher()->releaseEvents();
    }
}
```
