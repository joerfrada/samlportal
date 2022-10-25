<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'tb_sg_usuarios';

    protected $primaryKey = 'usuario_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'usuario', 'nombres', 'apellidos', 'email', 'activo',
    ];

    public function crud_usuarios(Usuario $user) {
        $db = DB::insert("insert into tb_sg_usuarios (usuario,nombres,apellidos,email,activo) values (?,?,?,?,?)", 
                        [
                            $user->usuario,
                            $user->nombres,
                            $user->apellidos,
                            $user->email,
                            'S',
                        ]);
        return $db;
    }
}
