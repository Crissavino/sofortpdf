<?php

/*
|--------------------------------------------------------------------------
| Embudo por sesion (fases B2/B3 del plan de tracking)
|--------------------------------------------------------------------------
|
| Los contadores diarios que este sitio ya le manda al BO salian de un archivo
| de texto rotado. Desde B3 salen de la tabla `funnel_events`, que guarda una
| fila por evento con una sesion anonima y la variante de experimento -- la
| granularidad que los contadores agregados no pueden dar y sin la cual no hay
| experimento posible.
|
| Diseño y taxonomia, en el repo del BO:
|   docs/superpowers/specs/2026-11-05-eventos-por-sesion-y-experimentos-design.md
|   docs/taxonomia-eventos.md
|
*/

return [
    /*
    | ARRANCA APAGADO, y no es prudencia de mas: con `enabled` en false no se
    | setea la cookie ni se escribe una sola fila, asi que el camino del
    | request queda identico al de antes del deploy. Eso es lo que permite
    | deployar las dos tareas que tocan el grupo `web` y la ruta de eventos
    | sin riesgo, y dejar el riesgo concentrado en el flip -- que es una linea
    | y se revierte con otra.
    |
    | El orden es: deployar apagado, smoke de los cinco locales, prender.
    */
    'enabled' => env('FUNNEL_EVENTS_ENABLED', false),

    /*
    | Dias que vive una fila, y tambien la vida de la cookie: una cookie de
    | medicion que dura mas que los datos que mide no tiene para que existir.
    |
    | 180 es el valor que el Anexo B dejo marcado [REVISAR] desde septiembre.
    | SIGUE PENDIENTE de Cristian; cuando se confirme, se cambia ACA y en
    | ningun otro lado -- el barrido de retencion y la cookie leen los dos de
    | este valor, asi que no puede haber dos numeros distintos.
    */
    'retention_days' => (int) env('FUNNEL_EVENTS_RETENTION_DAYS', 180),

    /*
    | La cookie de la sesion anonima. LA SETEA EL SERVIDOR, nunca un script
    | del cliente: el sitio carga Cookiebot con `data-blockingmode="auto"`
    | (layouts/app.blade.php), que bloquea cookies puestas desde scripts hasta
    | el consentimiento. Una cookie de medicion puesta desde GTM se perderia
    | en los visitantes que no aceptan y el denominador de la metrica norte
    | saldria incompleto sin que nada avise.
    |
    | Se declaro EXENTA de consentimiento (Cristian, 2026-11-05): es medicion
    | de primera parte, sin compartir con terceros, con un id aleatorio y sin
    | IP ni fingerprint. Esa ultima parte es la que sostiene la exencion, asi
    | que no se afloja.
    */
    'cookie' => env('FUNNEL_EVENTS_COOKIE', 'fe_sid'),

    /*
    | El experimento activo, o vacio si no hay ninguno. Cadena vacia = ningun
    | brazo asignado y la columna `variant` queda nula.
    |
    | Un solo experimento a la vez a proposito: dos tests simultaneos sobre el
    | mismo embudo se contaminan entre si, y la metrica norte no tiene volumen
    | para repartirse en cuatro brazos (medido el 2026-11-05: 3,9
    | conversiones por dia en este sitio).
    */
    'experiment' => env('FUNNEL_EVENTS_EXPERIMENT', ''),

    /*
    | Los brazos del experimento activo. El asignador reparte las sesiones
    | entre estos por hash estable del id de sesion.
    */
    'arms' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('FUNNEL_EVENTS_ARMS', 'A,B'))
    ))),
];
