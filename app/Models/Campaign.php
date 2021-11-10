<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CampaignCategory;
use App\Models\CampaignTree;
use App\Models\LeadCampaign;
use App\Models\UserCampaign;

class Campaign extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'campaign_tree_id',
        'name',
        'description',
        'is_reminder',
        'before_hours'
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
     * Get the campaign_tree
     */
    public function campaign_tree()
    {
        return $this->belongsTo(CampaignTree::class);
    }

    /**
     * Get the lead_campaigns
     */
    public function lead_campaigns()
    {
        return $this->hasMany(LeadCampaign::class, 'campaign_id', 'id');
    }

    /**
     * Get the user_campaign
     */
    public function user_campaign()
    {
        return $this->belongsTo(UserCampaign::class);
    }
}
