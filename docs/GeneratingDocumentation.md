# Generar documentación

(En desarrollo)

Se redactará por el responsable del desarrollo y validará por todas las partes (Negocio, UX, IT) antes de comenzar a picar una línea de código. Obviamente ciertos puntos podrán evolucionar durante el desarrollo, pero cualquier modificación concerniente a procesos conllevará una revisión y evaluación completa de nuevo de todos los puntos.

- Index

    - Propósito,
    - Objetivos,
    - Terminología
    - Posibles convenciones del contexto que habrá que tener en cuenta en el desarrollo.
    - Versión de la documentación y fecha de aprobación.
    - Desarrolladores implicados y fechas en las que participaron.


- First Run.

    - Qué hay que hacer para la puesta en marcha del servicio.
    - Migraciones de base de datos iniciales.
    - Seeding
    - Infra necesaria.
    - Datos iniciales útiles, etc.


- Aggregates, Entities, Value Objects, DTOs.

    - Descripción de todos los objetos utilizados, sus atributos, tipados, valores válidos en caso de ser Enums o Data Sets
    - Normas de validación aplicadas.
    - Si se extienden Value Objects genéricos como el Email, hay que especificarlo.
    - Procesado del input. Si se realiza alguna transformación previa del input antes de su almacenamiento.


- Actions and Processes

    Cada comportamiento existente en el sistema incluirá:

    - Título descriptivo del proceso/acción/comportamiento que se quiere lograr. Hay que intentar que estas acciones estén orientadas a comportamientos, no a lo que ocurre con un dato, ejemplo: 
        - Crear un usuario -> Poco aclaratorio, ¿sería posible que el usuario se cree y flote sólo en el espacio del sistema?
        - Añadir un Usuario a un Branch. -> Define mucho mejor qué es lo que esperamos que ocurra, y ayuda a la comprensión sobre qué atributos y entidades podrán intervenir.
    - Síncrona o Asíncrona.
    - Descripción y finalidad de la acción.
    - Permisos que son necesarios para ejecutarla. (El permiso máximo CanOperateAll no se incluirá ya que ese siempre podrá hacer todo)
    - Eventos que se desencadenan y podrán ser escuchados por cualquier otra parte del sistema.
    - Reglas de negocio que se aplican.
        - Ejemplo: Un Tenant no puede ser creado si ya existe otro Tenant con el mismo CIF, o un Tenant solo puede tener un máximo de 5 Branches.
    - Input necesario para su ejecución.
    - Puertos (interfaces exteriores) en los que estará disponible (HTTP, CLI, Background Job....)

También se incluirán las suscripciones a eventos y mensajes del contexto.

Nuestro código debe ser una traducción/narración literal de todo lo que pongamos aquí. Nada que no esté señalado en este documento, ocurrirá.
En Postman o en otra parte de la documentación se podrán ver los Endpoints o comandos CLI que invocarán esas acciones.

- Projections/Views

    - Nombre de la vista
    - Puertos desde los que será accesible.
    - Permisos necesarios (podrá ser pública)
    - Respuestas de ejemplo.

- Test Coverage

    - Nombre del test
    - Aserciones realizadas en cada uno.

- Permissions

    - Listado de permisos específicos del contexto.
