<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $table = 'appointment';
    protected $fillable = [
        'id',
        'user_asigned',
        'title',
        'slug',
        'description',
        'date_start',
        'date_end',
        'hour_start',
        'hour_end',
        'scheduled',
        'finished'
    ];

    public const FIELD = [
        'user_asigned' => 'USUARIO ASIGNADO',
        'scheduled' => 'CAMPO "AGENDADO"',
        'finished' => 'CAMPO "FINALIZADO"',
        'priority' => 'PRIORIDAD',
        'description' => 'DESCRIPCION',
        'duration' => 'DURACION',
        'title' => 'TITULO',
        'slug' => 'SLUG',
        'date_start' => 'FECHA INICIO',
        'date_end' => 'FECHA TERMINO',
        'hour_start' => 'HORA INICIO',
        'hour_end' => 'HORA TERMINO',
    ];

    public function activities(){
        return $this->hasMany(AppointmentActivity::class,'appointment_id','id');
    }

    public function assigned(){
        return $this->hasOne(User::class,'id','user_asigned');
    }

    public function saveAndReturnChanges(){
        $original = $this->getOriginal();

        $this->save();

        $changes = [
            'old'=>[],
            'new'=>[],
            'fields'=>''
        ];

        $index = 1;
        $max_index = count($this->getChanges()) - 1;

        foreach ($this->getChanges() as $key => $value) {
            if($key == 'updated_at') {
                $index++;
                continue;
            }
            if($key == 'slug') {
                $changes['oldSlug'] = $original[$key];
            }

            array_push($changes['old'], [ $key => $original[$key] ]);
            array_push($changes['new'], [ $key => $value ]);

            if( $index == $max_index ){
                $changes['fields'] = $changes['fields'] . self::FIELD[$key];
                break;
            }

            $changes['fields'] = $changes['fields'] . self::FIELD[$key] . ', ';
            $index++;
        }

        return $changes;
    }
}
