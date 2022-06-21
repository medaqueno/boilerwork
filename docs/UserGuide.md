
## Pasos iniciales a tener en cuenta al comenzar un proyecto.

- Enumerar y aislar qué acciones y/o eventos vamos a necesitar realizar, qué acciones nos pide negocio. Esto debe realizarse junto a negocio y UX.
- Definir y valorar qué dominio/s y contextos van a intervernir y dónde encajan esas acciones.
- https://franiglesias.github.io/the-way-to-ddd-1/
- https://franiglesias.github.io/the-way-to-ddd-2/
- https://wkrzywiec.medium.com/ports-adapters-architecture-on-example-19cab9e93be7



## Pasos para crear un endpoint que ejecute un comando (modificar el estado del sistema)

### 1. Crear Contexto
Nombrar el directorio del contexto en el que vamos a trabajar en **app/Core/`<NombreDeContexto>`** del que colgarán las diferentes capas de la aplicación.

### 2. Crear puerto de entrada.
Crear el puerto de entrada HTTP como punto de partida de la operación que se va a realizar. Siempre serán clases invocables para garantizar que solo realizan una única acción.

**app/Core/Users/UI/Ports/Http/RegisterUserPort.php**

``` php
#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\Users\UI\Ports\Http;

use App\Core\BC\Application\RegisterUserCommand;
use Boilerwork\System\Http\Request;
use Boilerwork\System\Http\Response;
use Boilerwork\UI\AbstractHTTPPort;
use Psr\Http\Message\ResponseInterface;

final class RegisterUserPort extends AbstractHTTPPort
{
    public function __invoke(Request $request, array $vars): ResponseInterface
    {
    }
}

```
> El objeto *Request* contiene todos los datos enviados por el cliente, y *$vars* los valores de la url que puedan ser dinámicos (PATCH /item/`{ID}`/modify)


### 3. Añadir una entrada en el router HTTP: 
**routes/httpApi.php**
``` php
use App\Core\Users\UI\Ports\Http\RegisterUserPort;

return [
    ['POST', '/registerUser', RegisterUserPort::class],
];
```
### 4. Crear comando
Crear el comando para la acción que queremos realizar incluyendo los atributos estrictamente necesarios para completarse en el constructor (siempre readonly, un comando es inmutable). Evitar en la medida de lo posible incluir atributos opcionales, y por supuesto nunca realizar acciones distintas dependiendo del contenido del request.

**app/Core/Users/Application/RegisterUserCommand.php**
``` php
#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\Users\Application;

use Boilerwork\Application\CommandInterface;

final class RegisterUserCommand implements CommandInterface
{
    public function __construct(
        public readonly string $id,
        public readonly string $email,
        public readonly string $username,
    ) {
    }
}
```

>Como norma general, los ID, no los generará el backend, serán enviados por el propio cliente bajo un formato estándar (UUID v4). El cliente es quien indica qué se hace y con qué datos. Esto le permite funcionar de forma asíncrona y no necesitar una respuesta del backend.

### 5. Enviar comando al *CommandBus*
Instanciamos el comando recien creado en el Port y lo envíamos al *CommandBus* para su ejecución. También incluimos la respuesta. 

``` php
#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\Users\UI\Ports\Http;

use App\Core\Users\Application\RegisterUserCommand;
use Boilerwork\System\Http\Request;
use Boilerwork\System\Http\Response;
use Boilerwork\UI\AbstractHTTPPort;
use Psr\Http\Message\ResponseInterface;

final class RegisterUserPort extends AbstractHTTPPort
{
    public function __invoke(Request $request, array $vars): ResponseInterface
    {
        $this->command()->handle(
            new RegisterUserCommand(
                id: $request->input('id'),
                email: $request->input('email'),
                username: $request->input('username')
            ),
        );

        return Response::empty(201); // 201 HTTP Header Created
    }
}
```
> Un comando NUNCA devuelve datos en caso de éxito. Si la operación fracasa, una excepción será lanzada en el sistema e incluirá las razones del error. Puede existir alguna excepción en la que un comando devuelva datos, pero debe estar muy justificada, ejemplo típico: Autenticación de segundo paso.

> En caso de requerirse de forma justificada, se puede llegar a utilizar $this->command()->syncHandle para esperar a la respuesta síncrona del servidor.

### 6. Crear CommandHandler
Crear el *CommandHandler* en la capa aplicación que se encargará de orquestar las acciones a realizar.

**app/Core/Users/Application/RegisterUserCommandHandler.php**
``` php
#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\Users\Application;

use Boilerwork\Application\CommandHandlerInterface;
use Boilerwork\Application\CommandInterface;

/**
 * @see App\Core\BC\Application\RegisterUserCommand
 **/
final class RegisterUserCommandHandler implements CommandHandlerInterface
{
    public function handle(CommandInterface $command): void
    {

    }
}
```
### 7. Añadir enlace para documentar.
En el comando añadimos la documentación relacionándolo con el handler. Esto facilita la navegación entre archivos en los IDEs.
``` php
#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\Users\Application;

use Boilerwork\Application\CommandInterface;

/**
 * @see \App\Core\Users\Application\RegisterUserCommandHandler
 */
final class RegisterUserCommand implements CommandInterface
{
    public function __construct(
        public readonly string $id,
        public readonly string $email,
        public readonly string $username,
    ) {
    }
}
```

### 8. Crear Agregado
Creamos el agregado o entidad. Un agregado es el encargado de restaurar, mantener, modificar y validar su propio estado. 
Un agregado es una transacción, y todo aquello que se modifique en esa transacción, debe formar parte de él.
``` php
#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\Users\Domain;

final class User extends AggregateRoot
{
protected function __construct(
        protected readonly Identity $aggregateId,
    ) {
    }

    public static function register(
        string $userId,
        string $email,
        string $username
    ): self {

        // Aquí haríamos aserciones para comprobar que se cumplen las reglas de negocio si fuera necesario.

        $user = new static(
            aggregateId: new Identity($userId),
        );

        return $user;
    }
}

```
En este caso, la transacción a realizar es registrar un usuario. Este proceso crea una identidad nueva en el sistema, utilizamos un método estático para ello que devuelve una nueva instancia de sí mismo con la información recibida. No hacemos un new Agregado(), eso no sería explícito con la acción a realizar.


### 9. Crear Evento 
Toda transacción finaliza en un evento que indica al sistema qué ha ocurrido. En este caso un usuario se ha registrado: UserHasRegistered. Creamos ese archivo de evento que por convención requiere una serie de métodos a incluir. El evento en la gran mayoría de los casos incluirá los mismos atributos que el comando. Ya que por lógica indica lo que ha ocurrido y con qué datos. Un evento al ser algo ocurrido en el "pasado" es inmutable.

**app/Core/Users/Domain/Events/UserHasRegistered.php**

``` php
#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\Users\Domain\Events;

use Boilerwork\Domain\AbstractEvent;
use Boilerwork\Domain\DomainEvent;

final class UserHasRegistered extends AbstractEvent implements DomainEvent
{
    protected bool $isPublic = true;

    protected ?string $topic = 'example/topicName';

    protected ?string $queue = null;

    public function __construct(
        public readonly string $userId,
        public readonly string $email,
        public readonly string $username,
    ) {
    }

    public function getAggregateId(): string
    {
        return $this->userId;  // Helper para obtener el agregado relacionado con la transacción.
    }

    public function serialize(): array
    {
        return $this->wrapSerialize(
            data: [
                'userId' => $this->userId,
                'email' => $this->email,
                'username' => $this->username,
            ]
        );
    }

    public static function unserialize(array $event): self
    {
        return (new static(
            userId: $event['data']['userId'],
            email: $event['data']['email'],
            username: $event['data']['username'],
        ));
    }
}
```

### 10. Emitir evento en el agregado.

Ya tenemos el evento, así que lo instanciamos en método correspondiente del agregado y levantamos para su publicación posterior. 

El evento al emitirse se aplica al agregado modificando su estado de facto. Con lo que además añadimos un método con la siguiente estructura por convención: apply`<NombreDelEvento>`

``` php
final class User extends AggregateRoot implements TracksEvents, IsEventSourced
{
    use TracksEventsTrait, IsEventSourcedTrait;

    private UserStatus $status;
    private UserEmail $email;
    private UserName $username;

    protected function __construct(
        protected readonly Identity $aggregateId,
    ) {
    }

    public static function register(
        string $userId,
        string $email,
        string $username
    ): self {

    	// Aquí haríamos aserciones para comprobar que se cumplen las reglas de negocio si fuera necesario.

        $user = new static(
            aggregateId: new Identity($userId),
        );

        $user->raise(
            new UserHasRegistered(
                userId: (new Identity($userId))->toPrimitive(),
                email: (new UserEmail($email))->toPrimitive(),
                username: (new UserName($username))->toPrimitive(),
            )
        );

        return $user;
    }

    protected function applyUserHasRegistered(UserHasRegistered $event): void
    {
        $this->email = new UserEmail($event->email);
        $this->username = new UserName($event->username);
        $this->status = new UserStatus(UserStatus::USER_STATUS_INITIAL);
    }
```

> Como en este caso queremos que User utilice eventos y sea persistido mediante *EventSourcing*, añadimos los dos interfaces: **TracksEvents**, **IsEventSourced** y los traits que implementan (**TracksEventsTrait**, **IsEventSourcedTrait**) los métodos necesarios para cumplir con ellos.

Como se puede observar, cada atributo es en realidad un ValueObject. La razón de que sea así y no primitivas es que cada ValueObject contiene sus propias reglas de negocio y validación y asegura que el dato siempre sea correcto. Desacoplando y centralizando esa responsabilidad en sí mismos.
A continuación el ValueObject UserName como ejemplo:

**app/Core/Users/Domain/ValueObjects/UserName.php**
```php
#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\Users\Domain\ValueObjects;

use Boilerwork\Domain\ValueObjects\ValueObject;
use Boilerwork\Domain\Assert;

/**
 * @internal
 **/
class UserName extends ValueObject
{
    public function __construct(
        public readonly string $value
    ) {
        // Ejecutamos las aserciones necesarias para asegurar la validez de su estado.
        // los dos últimos atributos de cada comprobación son el "texto para humanos" y el código del error específico a devolver en la excepción de validación que se dispara
        Assert::lazy()->tryAll()
            ->that($value)
            ->string('Value must be a string', 'userName.invalidType')
            ->betweenLength(4, 20, 'Value must be between 4 and 20 characters, both included', 'userName.invalidLength')
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
> Un ValueObject puede también contener métodos para devolver o validar las distintas partes que lo compongan, pero nunca para manipularlo, es inmutable. Si queremos que tenga otros valores instanciaremos un nuevo ValueObject.
También puede contener los posibles valores a modo de Enum/estáticos que permite en caso de requerirlo.

>La documentación relativa a la librería de aserciones se encuentra en: https://github.com/beberlei/assert

### 11. Invocar la acción recien creada en el CommandHandler
Ya tenemos los métodos listos en el agregado. Así que lo llamamos desde el handler.
```php
final class RegisterUserCommandHandler implements CommandHandlerInterface
{
    public function handle(CommandInterface $command): void
    {
      $user = User::register(
            userId: $command->id,
            email: $command->email,
            username: $command->username,
        );
    }
}
```

Ya tenemos el agregado **$user** en memoria en el estado final deseado. Desde este momento todas las operaciones y lógica de negocio que garantiza la consistencia de los datos ha finalizado. A partir de aquí podemos realizar las sucesivas operaciones que queramos con $user: persistencia, envío a sistemas externos, etc.

### 12. Persistencia. Creación de contratos/interfaces.
Creamos un contrato/interfaz de este dominio para indicar qué operaciones estarán permitidas. En este ejemplo vamos a utilizar EventSourcing, solo necesitamos dos operaciones: Insertar (Eventos) y recuperar el historial de Eventos (lo que nos permitirá reconstruir el último estado más actualizado de $user en otras transacciones diferentes).
Como ya existe un interfaz EventStore que indica estos dos métodos, solo sobreescribimos *reconstituteHistoryFor* para añadir mediante Union Type el agregado concreto que se va a devolver, de forma que los IDEs interpreten correctamente el type hinting.

**app/Core/Users/Domain/UserRepository.php**
```php
#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Domain;

use Boilerwork\Domain\IsEventSourced;
use Boilerwork\Domain\ValueObjects\Identity;
use Boilerwork\Infra\Persistence\EventStore;

interface UserRepository extends EventStore
{
   // sobreescribimos getAggregateHistoryFor para concretar que devuelve el tipo User.
   public function reconstituteHistoryFor(Identity $id): User|IsEventSourced; // Return union types to accomplish interface and IDE typehinting
}
```

### 13. Inyectar Contrato/Interfaz.
Inyectamos el interfaz en el handler. Y añadimos la operación a realizar en este caso la inserción: Append.
```php
final class RegisterUserCommandHandler implements CommandHandlerInterface
{
 public function __construct(private UserRepository $userRepository)
    {
    }

    public function handle(CommandInterface $command): void
    {
      $user = User::register(
            userId: $command->id,
            email: $command->email,
            username: $command->username,
        );

        $this->userRepository->append($user);
    }
}
```
### 14. Implementación del interfaz. Creación del repositorio.
Necesitamos implementar el interfaz, para la persistencia que hayamos elegido, por ejemplo, guardar en PostgreSQL. La base de desarrollo, tiene ya preparada esta implementación del patrón *EventSourcing* para PostgreSQL (Boilerwork\Infra\Persistence\PostgreSQLEventStore) y que será siempre igual para todos los agregados y dominios teniendo en cuenta *versionados* y posibles *race conditions*. 

**app/Core/Users/Infra/Persistence/UserPostgreSQLRepository.php**
```php
#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Infra\Persistence;

use App\Core\BC\Domain\UserRepository;
use Boilerwork\Infra\Persistence\PostgreSQLEventStore;

final class UserPostgreSQLRepository extends PostgreSQLEventStore implements UserRepository
{
}
```
> En un entorno más tradicional tipo CRUD, implementaríamos los create, read, update, delete. O nombres de operaciones concretas que contienen por ejemplo las queries necesarias. (Existen los siguientes en la base: PostgreSQLReadsClient, PostgreSQLWritesClient y RedisClient)

### 15. Bind Interfaz <-> Repositorio en el contenedor.
Mediante inyección de dependencias hacemos un *binding* del interfaz **UserRepository** con **UserPostgreSQLRepository**. Todos estos bindings se realizan en:

**app/Shared/Providers/ContainerBindingsProvider.php**
```php
namespace App\Shared\Providers;

final class ContainerBindingsProvider
{
    private array $services = [

        [\App\Core\Users\Domain\UserRepository::class, 'bind', \App\Core\Users\Infra\Persistence\UserPostgreSQLRepository::class],  // Añadimos esta línea

        [\Boilerwork\System\Clients\PostgreSQLWritesPool::class, 'singleton', null], // Start PostgreSQL Connection Pools Read and Writes to be used by services
        [\Boilerwork\System\Clients\PostgreSQLReadsPool::class, 'singleton', null], // Start PostgreSQL Connection Pools Read and Writes to be used by services
        [\Boilerwork\System\Clients\RedisPool::class, 'singleton', null],   // Start Redis Connection Pool to be used by services
        [\Boilerwork\System\Clients\MessagePool::class, 'singleton', null], // Start Message Connection Pool to be used by services
    ];

   ......
```
> Si queremos cambiar la implementación, modificamos el binding sin necesidad de editar la capa de dominio o aplicación.


### 16. Publicación de lo eventos emitidos.
Por último y una vez la persistencia ha sido completada correctamente, informamos al resto del sistema de los eventos que han ocurrido desde el handler.

```php
final class RegisterUserCommandHandler implements CommandHandlerInterface
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function handle(CommandInterface $command): void
    {
        $user = User::register(
            userId: $command->id,
            email: $command->email,
            username: $command->username,
        );

        $this->userRepository->append($user);

        eventsPublisher()->releaseEvents(); // Publica los eventos dentro del propio dominio o al exterior si han sido señalados como públicos.
    }
}
```
