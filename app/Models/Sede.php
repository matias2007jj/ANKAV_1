<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sede extends Model
{
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'codigo_cliente', 'codigo_cliente');
    }

    public function equipos()
    {
        return $this->hasMany(Equipo::class, 'codigo_sede', 'codigo_sede');
    }
}
