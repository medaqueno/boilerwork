
## Guía de uso de Kafka

- Mensaje: Contiene key, value y opcionalmente headers.
    - Pueden configurarse para asignarse a una partición
- Producer: Es quien publica el mensaje,
    - Tiene la posibilidad de ser configurado para ser idempotente y que sus publicaciones solo se escriban una única vez por configuración (enable.idempotence', 'true'). En cualquier caso el sistema debería estar preparado para ser idempotente por sí mismo y aunque ejecute la misma acción más de una vez, siempre deje el sistema en el mismo estado.
    - El producer tiene la posibilidad de manejar Acks, y que en caso de que falle la entrega de mensajes, pueda lanzar una excepción.
    - Las publicaciones puede ser transaccionales.
    - Puede especificar pariticiones específicas en caso de ser requerido. Pero se crean automáticamente si no se indica nada.
- Topic: a lo que se se subscribe un consumer y publica un producer. Contienen los mensajes
- Partition: Son divisiones dentro de los topics y son las **"unidades de paralelismo"**. Una partición solo "sirve" mensajes de uno en uno, si quieres consumo paralelo, hay que crear más particiones (regla que no falla, una por consumer o grupo de consumers)
- ConsumerGroup: Agrupa consumers, y se le asigna una partición, con la particularidad de que un mensaje solo se sirve a un grupo una vez, útil por ejemplo si tienes varios workers (o instancias replicadas) de un mismo servicio para trabajar más rápidamente y así no procesan el mismo mensaje varias veces. Por eso hay que asignar siempre a cada consumer un id de grupo. Un grupo puede ser un único consumer, será lo más típico.
    - Si por ejemplo queremos que un mismo microservicio escuche el mismo evento pero acabe en dos suscriptores distintos que hacen cosas distintas, habría que asignar un grupo o una partición distinta a cada uno.
- Offset: Ejemplo claro, cada topic contiene un "array" de mensajes, el offset es el index del array.

### Enlaces de interés
- https://kafka.apache.org/documentation/
- https://docs.confluent.io/platform/current/build-applications.html
- https://stackoverflow.com/questions/38024514/understanding-kafka-topics-and-partitions?rq=1 -> los dos principales respuestas aclaran cosas
- https://dzone.com/articles/20-best-practices-for-working-with-apache-kafka-at
