<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Instance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'virtualserver_name',
        'host',
        'voice_port',
        'serverquery_port',
        'is_ssh',
        'serverquery_username',
        'serverquery_password',
        'client_nickname',
        'default_channel_id',
        'autostart_enabled',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'serverquery_password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_ssh' => 'boolean',
        'serverquery_password' => 'encrypted',
        'autostart_enabled' => 'boolean',
    ];

    /**
     * Get the process associated with the model.
     */
    public function process(): BelongsTo
    {
        return $this->belongsTo(InstanceProcess::class, 'id', 'instance_id');
    }
}
