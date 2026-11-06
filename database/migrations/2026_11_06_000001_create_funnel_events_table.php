<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `funnel_events`: una fila por evento del embudo, con sesion anonima y
 * variante de experimento.
 *
 * DONDE VIVE, y por que importa: este sitio tiene UNA sola conexion `mysql`,
 * apuntada a `avocode_production` -- la base COMPARTIDA del BO. No hay una
 * base "del sitio" donde ponerla. El 2026-11-03 un SELECT largo sobre
 * `invoices` de ese mismo esquema trabo la tabla 85 minutos y tiro el
 * checkout de cuatro sitios, asi que esta tabla se agrega con tres
 * restricciones duras:
 *
 *   1. Es SOLO-AGREGAR. Ningun UPDATE. El unico DELETE es `funnel:sweep`.
 *   2. NINGUN JOIN contra `invoices` o `payments` en el camino del request.
 *   3. El lector diario es lo unico que la recorre, acotado por fecha y con
 *      indice.
 *
 * La leccion del incidente es que las consultas pesadas van a la copia
 * horaria, no que no se pueda escribir: un INSERT en una tabla nueva que nada
 * mas toca no lo reproduce.
 *
 * `customer_id` NO ES FK a proposito. `app/Models/Payment.php` se describe a
 * si mismo como "read-only mirror of the shared payments table": una FK
 * ataria el esquema de este sitio al del BO, y el dia que el BO renombre algo
 * la migracion de aca se rompe. Ademas solo viene en dos de los once eventos.
 *
 * Taxonomia (en el repo del BO): docs/taxonomia-eventos.md
 */
return new class extends Migration
{
    public function up()
    {
        Schema::create('funnel_events', function (Blueprint $table) {
            $table->id();

            // UTC, como toda la app (config/app.php). El corte a dia de
            // Europe/Bucharest lo hace el agregador, no la columna: el dia
            // del negocio arranca a las 22:00 o 21:00 UTC del dia anterior,
            // asi que guardar un "dia" aca obligaria a elegir una zona y
            // dejaria el dato atado a esa eleccion.
            $table->dateTime('occurred_at');

            // Id aleatorio de 32 hex de la cookie propia. Sin IP, sin hash
            // derivado de IP o user-agent, sin fingerprint: eso es lo que
            // sostiene que la cookie se haya declarado exenta de
            // consentimiento.
            $table->char('session_id', 32);

            // Uno de los once eventos canonicos de la taxonomia. String y no
            // enum: un evento nuevo no deberia necesitar una migracion, igual
            // que el `step` de bo_funnel_daily en el BO. El universo valido lo
            // valida el codigo y un evento fuera de la lista se rechaza.
            $table->string('event', 40);

            $table->unsignedInteger('website_id');
            $table->char('locale', 5);
            $table->string('device', 10);

            // La variante del experimento activo, nula si no hay ninguno. Es
            // COLUMNA DE CADA FILA y no una tabla de asignaciones aparte: una
            // tabla permitiria una fila de evento sin variante, que es el
            // estado del que no se puede analizar nada.
            $table->string('variant', 20)->nullable();

            $table->string('utm_source', 100)->nullable();
            $table->string('utm_medium', 100)->nullable();
            $table->string('utm_campaign', 100)->nullable();

            // PRESENCIA de gclid, no el valor. Define "sesion paga", que es el
            // denominador de la metrica norte. Guardar el gclid entero seria
            // un identificador de click que no hace falta para contar.
            $table->boolean('has_gclid')->default(false);

            // Trafico de dev/QA, para sacarlo de las tasas. Se calcula de la
            // IP contra INTERNAL_IPS y LA IP NO SE GUARDA.
            $table->boolean('is_internal')->default(false);

            // Solo en `trial_started` y `payment_succeeded`: los dos momentos
            // en que la persona ya se identifico y ya esta en `customers` de
            // todas formas. Esto vuelve la tabla PSEUDONIMA, no anonima, asi
            // que entra al regimen de borrado y de pedidos de acceso -- y por
            // eso el barrido de retencion tiene que alcanzarla.
            $table->unsignedInteger('customer_id')->nullable();

            // sha1(session_id|event|timestamp al segundo). Con el unico de
            // abajo, un reintento del cliente (doble click, retry del fetch)
            // se vuelve un no-op en vez de inflar el paso.
            $table->char('dedupe_key', 40)->nullable();

            $table->timestamp('created_at')->nullable();

            // Lo recorre el agregador diario, por rango de fecha.
            $table->index('occurred_at', 'funnel_events_occurred_at_index');
            // Para contar sesiones distintas por evento del dia.
            $table->index(['session_id', 'event'], 'funnel_events_session_event_index');
            $table->unique('dedupe_key', 'funnel_events_dedupe_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('funnel_events');
    }
};
