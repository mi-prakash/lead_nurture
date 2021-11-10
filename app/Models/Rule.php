<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CampaignMessage;

class Rule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'message_id',
        'execute_when',
        'removed',
        'add_to_campaign',
        'expression_value',
        'category',
        'instant_reply',
        'ordering'
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
     * Get the campaign_message
     */
    public function campaign_message()
    {
        return $this->belongsTo(CampaignMessage::class, 'id', 'message_id');
    }
}
