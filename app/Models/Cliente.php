<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $primaryKey = 'id';

    public function sedes()
    {
        return $this->hasMany(Sede::class, 'codigo_cliente', 'codigo_cliente');
    }

    public function equipos()
    {
        return $this->hasMany(Equipo::class, 'codigo_cliente', 'codigo_cliente');
    }
}
