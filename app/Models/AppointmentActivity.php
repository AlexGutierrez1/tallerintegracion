<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentActivity extends Model
{
    use HasFactory;

    protected $table = 'appointment_activity';

    const APPOINTMENT_CREATED = 10;
    const APPOINTMENT_CREATED_BY_LINK = 11;
    const APPOINTMENT_ARCHIVED = 20;
    const APPOINTMENT_UPDATED = 30;
    const APPOINTMENT_FINISHED = 31;
    const APPOINTMENT_UNFINISHED = 32;
    const APPOINTMENT_DELETED = 40;
    const APPOINTMENT_SCHEDULED = 50;
    const APPOINTMENT_SCHEDULED_BY_CUSTOMER = 51;

    public const ACTIVITY_EVENT = [
        self::APPOINTMENT_CREATED => 'creó la cita',
        self::APPOINTMENT_CREATED_BY_LINK => 'creó cita a través de enlace',
        self::APPOINTMENT_ARCHIVED => 'archivó la cita',
        self::APPOINTMENT_UPDATED => 'cambió: %s',
        self::APPOINTMENT_FINISHED => 'marcó como finalizada',
        self::APPOINTMENT_UNFINISHED => 'marcó como NO finalizada',
        self::APPOINTMENT_DELETED => 'eliminó la cita',
        self::APPOINTMENT_SCHEDULED => 'agendó la cita para el %s desde las %s hasta las %s horas',
        self::APPOINTMENT_SCHEDULED_BY_CUSTOMER => 'Agendado por cliente para el %s desde las %s hasta las %s',
    ];

    public function appointment() {
        return $this->belongsTo(Appointment::class)->withTrashed();
    }
}
