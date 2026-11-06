<?php

namespace App\Src\Funnel;

/**
 * El id de la sesion anonima del embudo: 32 caracteres hexadecimales en una
 * cookie first-party.
 *
 * ES ALEATORIO Y NADA MAS. No se deriva de la IP, ni del user-agent, ni de
 * ninguna combinacion de los dos, y no hay fingerprint en ningun lado. Eso no
 * es una preferencia: es lo que sostiene que la cookie se haya declarado
 * EXENTA de consentimiento el 2026-11-05. Una cookie que identificara al
 * visitante por sus caracteristicas seria perfilado, y entonces tendria que
 * esperar al consentimiento de Cookiebot -- con lo que el denominador de la
 * metrica norte contaria solo a los que aceptan y dejaria de ser comparable
 * mes a mes.
 *
 * `normalize()` NUNCA TIRA. El valor lo manda el cliente, asi que es hostil
 * por definicion; cualquier cosa que no sea un id valido se reemplaza por uno
 * nuevo. Un evento de embudo no vale una pagina caida.
 *
 * Diseño (repo del BO):
 * docs/superpowers/specs/2026-11-05-eventos-por-sesion-y-experimentos-design.md
 */
final class SessionCookie
{
    /** 32 hexa: 128 bits, el mismo largo que un uuid sin guiones. */
    private const LARGO = 32;

    /**
     * Si el valor que vino es un id valido lo devuelve igual; si no, acuña uno
     * nuevo. Es la unica forma de obtener un id, para que no haya dos lugares
     * que decidan que es valido.
     */
    public static function normalize(?string $valor): string
    {
        return self::esValido($valor) ? (string) $valor : self::acunar();
    }

    /**
     * Minusculas a proposito: `ctype_xdigit` acepta mayusculas, y si se
     * aceptaran, `AAAA...` y `aaaa...` serian dos sesiones distintas para el
     * mismo valor. Con un solo caso posible, el id es canonico.
     */
    public static function esValido(?string $valor): bool
    {
        return is_string($valor)
            && strlen($valor) === self::LARGO
            && ctype_xdigit($valor)
            && $valor === strtolower($valor);
    }

    /**
     * `random_bytes` y no `uniqid()` ni `mt_rand()`: los dos ultimos son
     * predecibles a partir del tiempo, y un id adivinable permitiria que
     * alguien se agregue a la sesion de otro -- no es un secreto de
     * autenticacion, pero contaminaria el conteo.
     */
    private static function acunar(): string
    {
        return bin2hex(random_bytes(self::LARGO / 2));
    }
}
