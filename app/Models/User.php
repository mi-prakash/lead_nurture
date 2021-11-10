<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Session;
use App\Models\MessageQueue;
use App\Models\UserCampaign;
use App\Models\UserCustomField;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'identifier',
        'name',
        'email',
        'password',
        'is_admin'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setImpersonating($id)
    {
        Session::forget('impersonate');
        Session::put('impersonate', $id);
    }

    public function stopImpersonating()
    {
        Session::forget('impersonate');
    }

    public function isImpersonating()
    {
        return Session::has('impersonate');
    }

    /**
     * Get the message_queues
     */
    public function message_queues()
    {
        return $this->hasMany(MessageQueue::class, 'user_id', 'id');
    }

    /**
     * Get the user_campaigns
     */
    public function user_campaigns()
    {
        return $this->hasMany(UserCampaign::class, 'user_id', 'id');
    }

    /**
     * Get the user_custome_fields
     */
    public function user_custome_fields()
    {
        return $this->hasMany(UserCustomField::class, 'user_id', 'id');
    }
}
