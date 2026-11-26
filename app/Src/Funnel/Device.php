<?php

namespace App\Src\Funnel;

/**
 * La categoria de dispositivo de una fila de `funnel_events`, desde el
 * User-Agent del request.
 *
 * ========================================================================
 * POR QUE A MANO Y NO UN PAQUETE
 * ========================================================================
 *
 * `vendor/` esta en `.gitignore` y el deploy documentado de este repo es
 * `git pull` + `view:clear` + `config:clear`, SIN `composer install`. Un
 * paquete nuevo deployaria codigo que llama a una clase que en el server no
 * existe, y lo haria en el camino del request. Asi que el detector va a
 * mano, acotado a las tres categorias que `EventRow::DEVICES` ya cerro.
 *
 * Antes de esto la columna salia de `$request->input('device', '')` y
 * NINGUNO de los tres beacons del repo manda ese campo, asi que las 2.082
 * filas de once dias decian `other`: el 100 %. Es el mismo bug que
 * `has_gclid` y los `utm_*` --un campo del body que el cliente no manda--
 * solo que este no es denominador de nada, y por eso se arregla aparte.
 *
 * ========================================================================
 * EL ORDEN DE LAS PREGUNTAS ES LA REGLA, no un detalle de implementacion
 * ========================================================================
 *
 * Un UA de Android **telefono** dice `Mobile` y uno de Android **tablet**
 * no lo dice: es la unica senal que los separa. Entonces tablet se pregunta
 * ANTES que mobile, y "Android sin Mobile" cae en tablet. Preguntar mobile
 * primero mandaria todas las tablets de Android a `mobile` y la categoria
 * quedaria vacia sin que nada falle.
 *
 * LO QUE NO PUEDE, DECLARADO: un iPad con iPadOS 13 o mas nuevo pide el
 * sitio en modo escritorio y su UA dice literalmente `Macintosh`. Por UA es
 * indistinguible de una Mac, asi que cuenta como `desktop`. Separarlos pide
 * una senal del cliente (touch points), o sea volver a confiar en el body.
 *
 * Y UN UA AUSENTE ES `other`, NO `desktop`. Un default comodo ahi mete
 * trafico sin identificar en la categoria mas grande, que es justo donde
 * nadie lo va a notar.
 */
final class Device
{
    public static function desdeUserAgent(?string $ua): string
    {
        $ua = trim((string) $ua);

        if ($ua === '') {
            return 'other';
        }

        $esAndroid = stripos($ua, 'Android') !== false;
        $diceMobile = stripos($ua, 'Mobile') !== false;

        // 1. Tablet primero: ver el comentario de arriba.
        if (preg_match('/iPad|Tablet|Kindle|Silk|PlayBook/i', $ua)
            || ($esAndroid && !$diceMobile)) {
            return 'tablet';
        }

        // 2. Telefonos.
        if (preg_match('/iPhone|iPod|webOS|BlackBerry|IEMobile|Opera Mini/i', $ua)
            || ($esAndroid && $diceMobile)
            || $diceMobile) {
            return 'mobile';
        }

        // 3. Escritorio. `Linux` va al final porque `Linux; Android` ya salio
        //    arriba: aca solo queda el Linux de verdad.
        if (preg_match('/Windows NT|Macintosh|X11|CrOS|Linux/i', $ua)) {
            return 'desktop';
        }

        return 'other';
    }
}
