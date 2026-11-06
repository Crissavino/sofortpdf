<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Una fila de `funnel_events`. Ver la migracion para por que la tabla vive en
 * el esquema compartido y con que restricciones.
 *
 * SIN RELACIONES A PROPOSITO. Un `belongsTo(Customer::class)` invitaria a
 * joinear contra la tabla compartida desde el camino del request, que es
 * exactamente la restriccion 2 de la migracion. Quien necesite cruzar esto
 * con `payments` lo hace en el chequeo de salida de B3, fuera del request.
 *
 * `updated_at` deshabilitado: la tabla es solo-agregar, asi que una columna
 * que solo existe para registrar modificaciones seria una columna que miente.
 */
class FunnelEvent extends Model
{
    protected $table = 'funnel_events';

    public $timestamps = false;

    protected $fillable = [
        'occurred_at',
        'session_id',
        'event',
        'website_id',
        'locale',
        'device',
        'variant',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'has_gclid',
        'is_internal',
        'customer_id',
        'dedupe_key',
        'created_at',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'has_gclid' => 'boolean',
        'is_internal' => 'boolean',
    ];
}
