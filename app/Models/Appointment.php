<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AppointmentLog;
use App\Models\Lead;
use App\Models\User;

class Appointment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'appointment_id',
        'user_id',
        'lead_id',
        'date',
        'date_time',
        'time',
        'end_time',
        'converted_time',
        'converted_end_time',
        'date_created',
        'date_time_created',
        'price',
        'price_sold',
        'paid',
        'amount_paid',
        'type',
        'appointment_type_id',
        'calendar_id',
        'timezone',
        'calendar_timezone',
        'canceled',
        'can_client_cancel',
        'can_client_reschedule',
        'status',
        'json_data'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'date:Y/m/d H:i',
        'updated_at' => 'date:Y/m/d H:i'
    ];

    /**
     * Get the lead
     */
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * Get the appointment log
     */
    public function appointmentLogs()
    {
        return $this->hasMany(AppointmentLog::class);
    }

    /**
     * Get the user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}