<?php

namespace App\Src\Funnel;

/**
 * En que brazo del experimento cae una sesion.
 *
 * SIN ESTADO Y SIN BASE DE DATOS. No hay tabla de asignaciones, y es una
 * decision del diseño, no una simplificacion: una tabla aparte permitiria que
 * exista una fila de evento SIN variante --porque la asignacion fallo, porque
 * llego despues, porque se perdio-- y una fila de evento sin variante es una
 * fila que no se puede analizar. Derivando la variante del id de sesion, cada
 * fila la tiene por construccion.
 *
 * EL HASH ES `md5` Y NO `crc32`, y esto esta medido en este grupo: el BO lo
 * aprendio con `EfwRefundExperiment`, donde `crc32` reparti 79/21 sobre los
 * ids de una rafaga. `crc32` es un checksum LINEAL, y los ids de un lote
 * comparten prefijos y sufijos largos, asi que su salida queda correlacionada.
 * Nuestros ids de sesion son aleatorios, asi que el riesgo es menor, pero no
 * hay razon para elegir el hash que ya se sabe que falla.
 *
 * LA SAL ES EL NOMBRE DEL EXPERIMENTO, y eso es lo que hace que dos tests
 * sucesivos no compartan la particion. Sin la sal, los que respondieron mejor
 * al brazo A del primer test volverian a quedar juntos en el segundo, y la
 * diferencia se leeria como efecto del cambio nuevo cuando es herencia del
 * anterior.
 *
 * NO BALANCEA LA DERIVA, a diferencia de `EfwRefundExperiment` en el BO. Alla
 * hace falta porque los casos llegan de a puñados (17 EFWs en un dia) y con n
 * chico ningun hash sostiene 50/50. Aca el n son miles de sesiones por dia, y
 * el test del 2 % sobre 100.000 muestra que el hash solo alcanza -- balancear
 * exigiria leer cuantos hubo antes, o sea estado, o sea justamente lo que esta
 * clase evita.
 *
 * Diseño (repo del BO):
 * docs/superpowers/specs/2026-11-05-eventos-por-sesion-y-experimentos-design.md
 */
final class VariantAssigner
{
    /**
     * Devuelve el brazo, o cadena vacia cuando no hay experimento que asignar.
     *
     * La cadena vacia --y no un brazo por default-- es importante: una fila
     * con variante cuando no habia experimento corriendo arrastraria filas de
     * un periodo sin test al analisis del siguiente.
     *
     * @param string[] $brazos
     */
    public static function for(string $sessionId, string $experimento, array $brazos): string
    {
        if ($sessionId === '' || $experimento === '' || $brazos === []) {
            return '';
        }

        $brazos = array_values($brazos);

        // 8 hexa = 32 bits. El sesgo del modulo a esa escala es despreciable
        // para 2, 3 o 4 brazos, y hexdec de mas digitos pasaria a float en
        // 32 bits.
        $n = (int) hexdec(substr(md5($sessionId . '|' . $experimento), 0, 8));

        return (string) $brazos[$n % count($brazos)];
    }
}
