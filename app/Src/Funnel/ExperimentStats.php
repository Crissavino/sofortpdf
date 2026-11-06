<?php

namespace App\Src\Funnel;

/**
 * El resultado de un experimento de dos brazos, y --lo que importa-- SI se
 * puede declarar.
 *
 * ========================================================================
 * INFORMA, NO DECLARA
 * ========================================================================
 *
 * Es la decision C6 del diseño. El brief de Ryan le prohibe declarar un
 * ganador sin el tamaño de muestra calculado y con menos de dos semanas de
 * datos; hasta ahora eso era una frase en un markdown, o sea una regla que el
 * rol no podia computar. Aca es un booleano con su motivo.
 *
 * `declarable` es false en tres casos, y los tres son reales:
 *
 *   1. UN BRAZO VACIO. Pasa cuando el experimento se configuro mal o cuando
 *      arranco hace dos dias. El de la izquierda tambien cuenta: el brazo de
 *      control con n=0 es lo que mato el A/B de EFW en el BO -- sus dos unicos
 *      casos eran los dos que se reembolsaron a mano.
 *   2. MUESTRA INSUFICIENTE. El caso de este sitio. Sin esta regla, 50 contra
 *      60 conversiones se leen como un ganador del 20 %.
 *   3. GUARDIAS INMADURAS. `frr` madura ~22 dias despues del trial y
 *      `dispute_rate_30d` a 30, o sea DESPUES de que el test cierra. Declarar
 *      antes es declarar sin saber si el brazo que gano en conversion sube las
 *      disputas -- que es exactamente la perdida que la metrica norte de Ryan
 *      existe para evitar ("un cambio que sube trials pero baja el FRR o sube
 *      disputas es una perdida").
 *
 * Y `significativo` es OTRA cosa que `declarable`: se puede tener muestra de
 * sobra (declarable) y una diferencia que no se distingue del ruido (no
 * significativo). Separarlos evita la lectura de "no es significativo, asi que
 * hace falta mas muestra", que es falsa cuando la muestra ya alcanzo: ahi lo
 * que se midio es que el cambio NO sirve.
 */
final class ExperimentStats
{
    /** z de 1,96: intervalo del 95 %, dos colas. */
    private const Z_95 = 1.959963984540054;

    /**
     * @param array{nombre:string,sesiones:int,conversiones:int,guardias_maduras:bool} $a
     * @param array{nombre:string,sesiones:int,conversiones:int,guardias_maduras:bool} $b
     * @param int $muestraRequerida por brazo, de SampleSize::perArm()
     * @return array{tasa_a:float,tasa_b:float,diff:float,ci_low:float,ci_high:float,significativo:bool,declarable:bool,motivo:string}
     */
    public static function compare(array $a, array $b, int $muestraRequerida): array
    {
        $nA = max(0, (int) $a['sesiones']);
        $nB = max(0, (int) $b['sesiones']);
        $pA = $nA > 0 ? (int) $a['conversiones'] / $nA : 0.0;
        $pB = $nB > 0 ? (int) $b['conversiones'] / $nB : 0.0;

        $diff = $pB - $pA;

        // Error estandar de la diferencia de dos proporciones independientes.
        $se = ($nA > 0 && $nB > 0)
            ? sqrt($pA * (1 - $pA) / $nA + $pB * (1 - $pB) / $nB)
            : 0.0;

        $low = $diff - self::Z_95 * $se;
        $high = $diff + self::Z_95 * $se;

        [$declarable, $motivo] = self::declarable($a, $b, $nA, $nB, $muestraRequerida);

        return [
            'tasa_a' => $pA,
            'tasa_b' => $pB,
            'diff' => $diff,
            'ci_low' => $low,
            'ci_high' => $high,
            // El intervalo no cruza el cero. Con se=0 (algun brazo vacio) no
            // hay nada que afirmar.
            'significativo' => $se > 0.0 && (($low > 0 && $high > 0) || ($low < 0 && $high < 0)),
            'declarable' => $declarable,
            'motivo' => $motivo,
        ];
    }

    /** @return array{0:bool,1:string} */
    private static function declarable(array $a, array $b, int $nA, int $nB, int $requerida): array
    {
        $vacios = [];
        if ($nA === 0) { $vacios[] = (string) $a['nombre']; }
        if ($nB === 0) { $vacios[] = (string) $b['nombre']; }

        if ($vacios !== []) {
            return [false, 'brazo(s) sin sesiones: ' . implode(', ', $vacios)];
        }

        if ($nA < $requerida || $nB < $requerida) {
            return [false, sprintf(
                'muestra insuficiente: %s tiene %d y %s tiene %d, hacen falta %d por brazo',
                $a['nombre'], $nA, $b['nombre'], $nB, $requerida
            )];
        }

        $inmaduros = [];
        if (!($a['guardias_maduras'] ?? false)) { $inmaduros[] = (string) $a['nombre']; }
        if (!($b['guardias_maduras'] ?? false)) { $inmaduros[] = (string) $b['nombre']; }

        if ($inmaduros !== []) {
            return [false, 'guardias inmaduras (frr ~22 dias, dispute_rate_30d 30) en: ' . implode(', ', $inmaduros)];
        }

        return [true, 'muestra alcanzada y guardias maduras'];
    }
}
