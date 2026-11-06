<?php

namespace App\Src\Funnel;

use InvalidArgumentException;

/**
 * Cuanta muestra hace falta por brazo, y cuantos dias es eso.
 *
 * ========================================================================
 * PARA QUE SIRVE DE VERDAD
 * ========================================================================
 *
 * No es para reportar un numero al final: es para contestar ANTES de empezar
 * si el test se puede correr. El brief de Ryan le prohibe declarar un ganador
 * sin el tamaño de muestra calculado, y hasta hoy no tenia con que
 * calcularlo -- o sea era una regla que no podia computar, que en este
 * sistema equivale a una regla que no existe.
 *
 * Y la respuesta esperada para este sitio es NO. Medido el 2026-11-05:
 * sofortpdf tiene 109 clicks pagos y 3,9 conversiones por dia. Que la
 * calculadora diga "no corras este test" es el mecanismo funcionando, no una
 * falla -- es la decision D8 del diseño.
 *
 * ========================================================================
 * LA FORMULA, Y POR QUE ESTA
 * ========================================================================
 *
 * Aproximacion normal para la diferencia de dos proporciones, brazos iguales:
 *
 *   n = (z[1-a/2] + z[potencia])^2 * (p1(1-p1) + p2(1-p2)) / (p2-p1)^2
 *
 * El `mde` es RELATIVO (0,20 = "detectar un 20 % de mejora"), porque asi se
 * piensa un experimento: "quiero ver si esto sube la conversion un 20 %", no
 * "quiero ver si la sube 2 puntos". p2 = p1 * (1 + mde).
 *
 * `contract-kit` tiene un `chi2()` en `ab:results`: se mira para no divergir
 * de vocabulario, pero no sirve para esto -- chi-cuadrado contesta "¿la
 * diferencia que vi es significativa?" y esto contesta "¿cuanta muestra
 * necesito para poder contestar eso?". Son las dos puntas del mismo problema.
 */
final class SampleSize
{
    /**
     * @param float $baseRate tasa actual, entre 0 y 1 exclusivos
     * @param float $mde      efecto minimo detectable RELATIVO (0,20 = 20 %)
     * @param float $alpha    nivel de significancia, a dos colas
     * @param float $power    potencia
     * @return int filas por brazo
     */
    public static function perArm(float $baseRate, float $mde, float $alpha, float $power): int
    {
        if ($baseRate <= 0.0 || $baseRate >= 1.0) {
            throw new InvalidArgumentException("baseRate tiene que estar entre 0 y 1, vino: {$baseRate}");
        }
        if ($mde <= 0.0) {
            // Sin efecto minimo la muestra es infinita. Devolver un numero
            // enorme seria peor que tirar: alguien lo tomaria por respuesta.
            throw new InvalidArgumentException("mde tiene que ser > 0, vino: {$mde}");
        }
        if ($alpha <= 0.0 || $alpha >= 1.0 || $power <= 0.0 || $power >= 1.0) {
            throw new InvalidArgumentException('alpha y power tienen que estar entre 0 y 1');
        }

        $p1 = $baseRate;
        $p2 = $baseRate * (1 + $mde);

        if ($p2 >= 1.0) {
            throw new InvalidArgumentException(
                "mde {$mde} sobre una base de {$baseRate} da una tasa de " . round($p2, 4) . ', que no existe'
            );
        }

        $z = self::z(1 - $alpha / 2) + self::z($power);
        $varianza = $p1 * (1 - $p1) + $p2 * (1 - $p2);

        return (int) ceil(($z ** 2) * $varianza / (($p2 - $p1) ** 2));
    }

    /**
     * Cuantos dias de calendario son esas filas, con el trafico que hay.
     *
     * Es la traduccion que de verdad se lee: "3.840 por brazo" no le dice
     * nada a nadie, "71 dias" si.
     */
    public static function daysNeeded(int $porBrazo, int $brazos, float $porDia): ?int
    {
        if ($porDia <= 0.0 || $brazos < 1) {
            return null;
        }

        return (int) ceil($porBrazo * $brazos / $porDia);
    }

    /**
     * Cuantil de la normal estandar (inversa de la CDF), por la aproximacion
     * racional de Acklam. Error relativo < 1,15e-9, de sobra: la incertidumbre
     * de la tasa base manda ordenes de magnitud mas que esto.
     *
     * Hay un test contra los cuatro valores de tabla que se usan en la
     * practica, porque si esta aproximacion se degrada todo lo demas sigue
     * devolviendo numeros plausibles y equivocados.
     */
    public static function z(float $p): float
    {
        if ($p <= 0.0 || $p >= 1.0) {
            throw new InvalidArgumentException("z() espera 0 < p < 1, vino: {$p}");
        }

        $a = [-3.969683028665376e+01, 2.209460984245205e+02, -2.759285104469687e+02,
              1.383577518672690e+02, -3.066479806614716e+01, 2.506628277459239e+00];
        $b = [-5.447609879822406e+01, 1.615858368580409e+02, -1.556989798598866e+02,
              6.680131188771972e+01, -1.328068155288572e+01];
        $c = [-7.784894002430293e-03, -3.223964580411365e-01, -2.400758277161838e+00,
              -2.549732539343734e+00, 4.374664141464968e+00, 2.938163982698783e+00];
        $d = [7.784695709041462e-03, 3.224671290700398e-01, 2.445134137142996e+00,
              3.754408661907416e+00];

        $plow = 0.02425;
        $phigh = 1 - $plow;

        if ($p < $plow) {
            $q = sqrt(-2 * log($p));

            return ((((($c[0] * $q + $c[1]) * $q + $c[2]) * $q + $c[3]) * $q + $c[4]) * $q + $c[5])
                / (((($d[0] * $q + $d[1]) * $q + $d[2]) * $q + $d[3]) * $q + 1);
        }

        if ($p > $phigh) {
            $q = sqrt(-2 * log(1 - $p));

            return -((((($c[0] * $q + $c[1]) * $q + $c[2]) * $q + $c[3]) * $q + $c[4]) * $q + $c[5])
                / (((($d[0] * $q + $d[1]) * $q + $d[2]) * $q + $d[3]) * $q + 1);
        }

        $q = $p - 0.5;
        $r = $q * $q;

        return ((((($a[0] * $r + $a[1]) * $r + $a[2]) * $r + $a[3]) * $r + $a[4]) * $r + $a[5]) * $q
            / ((((($b[0] * $r + $b[1]) * $r + $b[2]) * $r + $b[3]) * $r + $b[4]) * $r + 1);
    }
}
