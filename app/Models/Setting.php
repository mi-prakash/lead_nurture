<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'click_funnel_email',
        'click_funnel_api_key',
        'click_funnel_webhook_url',
        'click_funnel_name',
        'click_funnel_id',
        'acuity_user_id',
        'acuity_api_key',
        'acuity_webhook_url',
        'acuity_calendar_name',
        'acuity_calendar_id',
        'twilio_account_sid',
        'twilio_auth_token',
        'twilio_number',
        'twilio_webhook_url'
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
     * Get the user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
