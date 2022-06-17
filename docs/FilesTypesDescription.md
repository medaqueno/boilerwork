# Descripción de la estructura de carpetas

Descripción rápida de la estructura de carpetas en app:

- UI ([ver](#ui-layer))
	- Contiene los puertos de entrada.
	- Es la capa que recibe las peticiones del exterior del sistema, HTTP, AMPQ, WebSockets, etc. y ejecutan los comandos recibidos por el UI o por eventos en el sistema.
	- Contiene si procede los ViewModels que ejecutan queries directas a las persistencias de lectura (nunca deben llegar a interactuar con la lógica del sistema)
	- Interactúa con la capa Application mediante un CommandBus.
	- Devuelve las respuestas al cliente.

- Application ([ver](#application-layer))
	- Orquestan la las decisiones de la aplicación (no modifican el estado de nada, solo trasladan al dominio lo que el UI quiere hacer)
	- Publican eventos del agregado.
	- Mediante archivos "servicios de aplicación" pueden realizar peticiones a otros dominios o contextos. (No debería ser frecuente, ya que si el dominio está bien diseñado esto ocurre muy pocas veces)
	- Contiene comandos y manejadores de comandos.
	- Contiene los jobs o tasks que funcionan como crones, vienen a tener la misma finalidad que un handler.

- Domain ([ver](#domain-layer))
	- Contiene las entidades y agregados
	- Incluye toda la lógica de negocio se ejecuta en esta capa: Todo lo que afecte, modifique o valide el estado de una entidad o agregado.
	- Incluye los eventos de dominio. Se emite uno por cada acción que se complete.
	- Incluye los servicios de dominio. Si una comprobación es excesivamente compleja, la podemos extraer a un archivo externo para mejorar la legibilidad y encapsulación del agregado aunque por defecto, todo debe ir en el agregado o entidad.
	- Incluye los ValueObjects que podrán componer los agregados. Solo se diferencian de los agregados o entidades en que carece de ID. Ejemplo Identities, Money, Country, Email...
	- Incluye los interfaces o contratos de lo que el agregado o entidad necesita para su funcionamiento y serán implementados en la Infra mediante Inyección de Dependencias.

- Infra ([ver](#infra-layer))
	- De forma general, contiene las implementaciones y adaptadores para la comunicación o interacción con sistemas externos, puede ser desde los repositorios de una base de datos, las llamadas a cualquier actor externo al contexto o API, o el mapeo de objetos procedentes de terceros.
	- Contiene las proyecciones que generan vistas denormalizadas de lectura al subscribirse a distintos eventos del sistema. (No modifican el estado del sistema).



# Descripción de los distintos tipos de archivos/clases

## UI Layer
### Ports
- Único punto de entrada para recibir los comandos del exterior del sistema: HTTP, Messaging, WebSockets, etc.
- Si procede devuelven un objeto del tipo Psr\Http\Message\ResponseInterface al cliente utilizando el HTTP Status adecuado.
- Interactúa con la capa Application mediante un *CommandBus*.
- Como normal general siempre tendrán un comportamiento asíncrono.
- Realizan una única acción.

### ViewModels
- Reciben las peticiones de lectura de los clientes.
- Ejecutan queries directas contra las bases de datos de lectura recuperando exactamente los datos que requiere la vista de cliente.
- Podrían formatear los datos, aunque lo ideal sería que se guardaran ya formateados/calculados mediante las proyecciones.
- Devuelven un objeto del tipo Psr\Http\Message\ResponseInterface con el HTTP Status 200 o >=400 si ha ocurriedo algún error.

## Application Layer
### Commands
- Contienen los atributos estrictamente necesarios para la ejecución de la acción.
- Son inmutables. Sus atributos son de **solo lectura**.
- Los atributos serán primitivas exclusivamente.
- Los nombres de los comandos deben ser verbos imperativos en tiempo presente qeu describan claramente la acción/transacción a ejecutar: *RegisterUserCommand*, *PublishBudgetCommand*...

### CommandHandlers
- Son los responsables de orquestar las acciones que requiere la aplicación para completar el comando recibido (Trasladan al dominio lo que el UI quiere hacer).
- Siempre habrá un Handler por cada Command.
- Se ejecutan automáticamente desde el *CommandBus*.
- Un CommandHandler NUNCA devuelve datos en caso de éxito. Si la operación fracasa, una excepción será lanzada en el sistema e incluirá las razones del error. Puede existir alguna excepción en la que un comando devuelva datos, pero debe estar muy justificada, ejemplo típico: Autenticación de segundo paso.
- Como norma general todas las operaciones con comandos son asíncronas.
- Solo pueden modificar el estado de un agregado. Si necesitamos modificar más de un agregado en la misma operación, tenemos que revisar el diseño del dominio. Una transacción = Un agregado = Un Handler. Los suscriptores a los eventos de dominio serán los encargados de provocar los *side effects* en el sistema. Pero eso serán distintas transacciones.

### Jobs/Tasks
- Funcionan como crones que se ejecutan en background de forma periódica.
- Tienen las mismas funciones que un CommandHandler.

### Application Services
- Realizan peticiones a otros dominios o contextos para recuperar información relevante para la transacción. (No debería ser frecuente, ya que si el dominio está bien diseñado esto ocurre muy pocas veces).

## Domain Layer

### Events
- Toda transacción emitirá al menos un evento.
- Contienen la información sobre algo que ha ocurrido en el dominio.
- Siempre deben ser nombrados mediante un verbo en tiempo pasado: *UserHasRegistered*, *BudgetHasBeenPublished*
- Contienen por defecto fecha y versión del evento así como referencia al agregado/transacción que los ha emitido.
- Son inmutables y sus atributos son siempre primitivas, y por lo general coincidirán con los de los comandos.
- En el patrón *EventSourcing*, los serán persistidos y serán la *golden source* del estado del sistema.

### ValueObjects
- Contienen un valor y permiten el tipado de los objetos.
- No tienen identidad propia, eso les diferencia de un agregado/entidad.
- Son los responsables de mantener la consistencia y validez de sus propios atributos.
- Son inmutables. En caso de cualquier modificación se debe devolver un nuevo VO.
- Pueden contener métodos para la extracción o validación específica de ciertos valores.
- Pueden estar compuestos por varios atributos (primitivas) o también por otros ValueObjects.
- Ejemplo pseudo code:
    ```
    Money
        attributes:
            amount <float> 300.10
            code <string> EUR
            currency <string> euro

        methods:
            toPrimitive(): float -> Devuelve el valor como primitiva
            currency(): string -> Devuelve el valor de currency
            ...
    ```

### Agregados
- Consiste en una o más *entidades* o *value objects* que pueden modificarse en una única transacción.Todo lo que cambie en una sola transacción, forma parte del agregado.
- Su estado siempre debe ser consistente. Contienen toda la lógica del dominio y protegen sus valores mediante validaciones y reglas de negocio (*invariants*).
- Se crea, guarda y recupera como un todo.
- Un agregado no es un mero aglutinador de objetos y atributos, todos ellos conforman un concepto de dominio.
- Contiene una entidad principal que lo identifica y a la que se referencia desde el exterior, a la que llamamos *aggregate root*.
- Contienen todos los métodos existentes que puedan modificar su estado.
- Debe recuperarse de la persistencia su último estado conocido, y entonces aplicar los cambios requeridos por el comando.
- Emiten eventos una vez han modificado su estado correctamente.
- En cada transacción solo puede modificarse un agregado.
- Deben ser lo más reducidos posible para facilitar su utilización y mantenimiento.
- Se pueden utilizar factories para su creación inicial, pero a ese factory siempre se le llamará desde el propio agregado.

### Domain Contracts/Interfaces
- Establecen las acciones que se podrán realizar sobre un agregado. Guardar, Obtener, RecuperarHistoria, Borrar, etc.
- Serán implementados en la capa de Infra.

### Domain Services
- Contienen validaciones o reglas de negocio.
- Si una comprobación es excesivamente compleja, la podemos extraer a un *domain service* para mejorar la legibilidad y encapsulación del agregado aunque por defecto, todo debe ir en el agregado o entidad.

## Infra Layer

### Repositorios
- Responsables de tratar con las bases de datos, cache o APIs de terceros.
- Implementan un interface/contrato de la capa de dominio.
- Si no encuentran datos devuelven:
	- Si buscamos un solo registro: null
    - Si buscamos más de un registro (colección): array vacío
- Sólo pueden lanzar excepciones relativas a errores de sus operación como errores de conexión.
- Debe existir un Repositorio para operaciones de lectura y otro de escritura. Los repositorios de Escritura no devolverán ningún dato. -> void

### Mappers
- Responsables del mapeo de datos procedentes del exterior (un tercero, otro contexto o dominio)
- No deberían formatear ningún dato del cliente. El cliente debe enviar los datos en el formato correcto.
- Funcionan como ACL (Anti Corruption Layer)

### Proyecciones
- Generan vistas denormalizadas de lectura al subscribirse a distintos eventos del sistema. (No modifican el estado del sistema, solo las persistencias de lectura)
