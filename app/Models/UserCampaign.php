<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CampaignCategory;
use App\Models\CampaignTree;
use App\Models\Campaign;
use App\Models\User;

class UserCampaign extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'campaign_category_id',
        'campaign_tree_id',
        'campaign_id',
        'status'
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
     * Get the campaigns
     */
    public function campaigns()
    {
        return $this->hasMany(Campaign::class, 'id', 'campaign_id');
    }

    /**
     * Get the user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the campaign_category
     */
    public function campaign_category()
    {
        return $this->belongsTo(CampaignCategory::class);
    }

    /**
     * Get the campaign_tree
     */
    public function campaign_tree()
    {
        return $this->belongsTo(CampaignTree::class);
    }
}
