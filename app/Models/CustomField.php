<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserCustomField;

class CustomField extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'campaign_category_id',
        'campaign_tree_id',
        'name',
        'value',
        'data_type'
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
     * Get the user_custome_field
     */
    public function user_custome_field()
    {
        return $this->belongsTo(UserCustomField::class);
    }
}
