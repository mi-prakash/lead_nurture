<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\RuleExpression;
use App\Models\User;

class MessageRuleCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'added_by'
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
     * Get the rule expressions
     */
    public function expressions()
    {
        return $this->hasMany(RuleExpression::class, 'message_rule_category_id', 'id');
    }

    /**
     * Get the user
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'added_by', 'id');
    }    
}
