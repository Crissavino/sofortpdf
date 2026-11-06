<?php

namespace App\Src\Funnel;

use InvalidArgumentException;

/**
 * Arma una fila de `funnel_events` a partir de datos ya extraidos del
 * request. PURA: no lee el request, no toca la DB, no lee config.
 *
 * Esta partida asi a proposito. Los tests de este repo corren contra la
 * conexion real (`phpunit.xml` tiene la de sqlite comentada), asi que toda la
 * logica que se quiera testear tiene que vivir afuera de la escritura. Es el
 * mismo patron que `Frr::rowsFrom` en el BO.
 *
 * TRES COSAS QUE ESTA CLASE GARANTIZA, y cada una tiene su test:
 *
 * 1. NO HAY PII EN LA FILA. No existe columna de IP ni de user-agent, y si
 *    quien llama las manda, se descartan. La IP se usa UNA vez, afuera, para
 *    calcular `is_internal`, y no se persiste. Eso es lo que sostiene que la
 *    cookie de sesion se haya declarado exenta de consentimiento.
 *
 * 2. `customer_id` SOLO en `trial_started` y `payment_succeeded`. En los otros
 *    nueve eventos la columna queda nula aunque venga con valor. Son los dos
 *    momentos en que la persona ya se identifico y ya esta en `customers`; en
 *    el resto, guardarla convertiria una sesion anonima en pseudonima sin
 *    necesidad.
 *
 * 3. UN REINTENTO NO INFLA EL PASO. El `dedupe_key` es
 *    sha1(session|evento|segundo) y la tabla tiene un unico sobre esa columna,
 *    asi que un doble click o un retry del fetch del cliente se vuelve un
 *    no-op en el INSERT.
 *
 * Y RECORTA EN VEZ DE FALLAR: un `utm_campaign` de 5 KB es trivial de mandar
 * en una URL, y con MySQL en modo estricto el INSERT tiraria. Como la
 * escritura cuelga del camino del request, un dato decorativo demasiado largo
 * no puede ser un error.
 *
 * Taxonomia (repo del BO): docs/taxonomia-eventos.md
 */
final class EventRow
{
    /**
     * Los once eventos canonicos, en el orden del embudo. El orden importa
     * para leerlos, no para la validacion.
     */
    public const EVENTOS = [
        'landing_view',
        'tool_started',
        'file_uploaded',
        'processing_completed',
        'paywall_view',
        'checkout_view',
        'payment_attempted',
        'payment_succeeded',
        'payment_failed',
        'trial_started',
        'error_shown',
    ];

    /**
     * Lo que ESTE sitio emite hoy -> el evento canonico.
     *
     * Existe porque los nombres del cliente son anteriores a la taxonomia y
     * cambiarlos en el front seria tocar el blade del modal de pago por una
     * cuestion de nombres. La tabla de mapeo es la del documento de taxonomia
     * (repo del BO, docs/taxonomia-eventos.md): el rollout a los otros cuatro
     * sitios es completar su columna de esa tabla, no reinterpretar nada.
     *
     * Un evento del cliente que no este aca NO se escribe, y se registra: es
     * la misma regla que el 422 del BO ante un step desconocido.
     */
    public const DEL_CLIENTE = [
        'file_uploaded' => 'file_uploaded',
        'payment_modal_opened' => 'checkout_view',
        'payment_attempted' => 'payment_attempted',
    ];

    /** Los unicos dos eventos que pueden llevar `customer_id`. */
    public const EVENTOS_CON_CLIENTE = ['trial_started', 'payment_succeeded'];

    /** Categorias cerradas: una cuarta que aparece sola parte las tasas. */
    public const DEVICES = ['desktop', 'mobile', 'tablet'];

    /** Largos de las columnas, para recortar antes del INSERT. */
    private const LARGOS = [
        'locale' => 5,
        'device' => 10,
        'variant' => 20,
        'utm_source' => 100,
        'utm_medium' => 100,
        'utm_campaign' => 100,
    ];

    /**
     * Traduce el nombre que manda el cliente al evento canonico, o null si
     * ese nombre no esta mapeado.
     */
    public static function canonico(string $delCliente): ?string
    {
        return self::DEL_CLIENTE[$delCliente] ?? null;
    }

    /**
     * @param array<string,mixed> $entrada
     * @return array<string,mixed> la fila lista para insertar
     */
    public static function build(array $entrada): array
    {
        $evento = (string) ($entrada['event'] ?? '');

        if (!in_array($evento, self::EVENTOS, true)) {
            // Ruidoso a proposito, igual que el 422 del BO ante un step
            // desconocido: un typo tiene que fallar y no crear un evento
            // nuevo que despues aparece vacio en el embudo.
            throw new InvalidArgumentException(
                "evento fuera de la taxonomia: '{$evento}'. Validos: " . implode(', ', self::EVENTOS)
            );
        }

        $sesion = (string) ($entrada['session_id'] ?? '');
        $cuando = (string) ($entrada['occurred_at'] ?? '');

        $fila = [
            'occurred_at' => $cuando,
            'session_id' => $sesion,
            'event' => $evento,
            'website_id' => (int) ($entrada['website_id'] ?? 0),
            'locale' => self::recortar('locale', (string) ($entrada['locale'] ?? '')),
            'device' => self::device((string) ($entrada['device'] ?? '')),
            'variant' => self::recortarONulo('variant', $entrada['variant'] ?? null),
            'utm_source' => self::recortarONulo('utm_source', $entrada['utm_source'] ?? null),
            'utm_medium' => self::recortarONulo('utm_medium', $entrada['utm_medium'] ?? null),
            'utm_campaign' => self::recortarONulo('utm_campaign', $entrada['utm_campaign'] ?? null),
            'has_gclid' => (bool) ($entrada['has_gclid'] ?? false),
            'is_internal' => (bool) ($entrada['is_internal'] ?? false),
            'customer_id' => self::cliente($evento, $entrada['customer_id'] ?? null),
            'dedupe_key' => sha1($sesion . '|' . $evento . '|' . $cuando),
        ];

        return $fila;
    }

    /** Nulo cuando el evento no es uno de los dos que pueden llevarlo. */
    private static function cliente(string $evento, $valor): ?int
    {
        if ($valor === null || !in_array($evento, self::EVENTOS_CON_CLIENTE, true)) {
            return null;
        }

        return (int) $valor;
    }

    /**
     * 'other' y no la cadena cruda: un valor libre en esta columna aparece en
     * el panel como una categoria nueva de dispositivo que nadie definio.
     */
    private static function device(string $valor): string
    {
        return in_array($valor, self::DEVICES, true) ? $valor : 'other';
    }

    private static function recortar(string $columna, string $valor): string
    {
        return mb_substr($valor, 0, self::LARGOS[$columna]);
    }

    private static function recortarONulo(string $columna, $valor): ?string
    {
        if ($valor === null || $valor === '') {
            return null;
        }

        return self::recortar($columna, (string) $valor);
    }
}
