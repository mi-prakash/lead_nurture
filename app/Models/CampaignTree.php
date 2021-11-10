<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CampaignCategory;
use App\Models\Campaign;

class CampaignTree extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'campaign_category_id',
        'name',
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
     * Get the campaign_category
     */
    public function campaign_category()
    {
        return $this->belongsTo(CampaignCategory::class);
    }

    /**
     * Get the campaigns
     */
    public function campaigns()
    {
        return $this->hasMany(Campaign::class, 'campaign_tree_id', 'id');
    }
}
