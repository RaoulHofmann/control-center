<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModuleInstance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'module_id',
        'name',
        'is_active',
        'display_order',
        'config',
        'cached_data',
        'last_updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'config' => 'array',
        'cached_data' => 'array',
        'last_updated_at' => 'datetime',
    ];

    /**
     * Get the module that owns the instance.
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

}
