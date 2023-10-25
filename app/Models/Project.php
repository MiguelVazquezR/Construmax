<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Project extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'service_type',
        'description',
        'currency',
        'address',
        'invoice_type',
        'is_strict',
        'is_internal',
        'budget',
        'start_date',
        'limit_date',
        'finished_at',
        'project_group_id',
        'user_id',
        'opportunity_id',
        'owner_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'limit_date' => 'date',
        'finished_at' => 'datetime',
    ];

    // relationships
    public function projectGroup()
    {
        return $this->belongsTo(ProjectGroup::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }

    public function opportunity()
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
        ->withPivot([
            'id',
            'permissions',
        ])->withTimestamps();
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}