<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'taskable',
        'name',
        'description',
        'department',
        'priority',
        'status',
        'is_paused',
        'start_date',
        'limit_date',
        'start_time',
        'limit_time',
        'finished_at',
        'user_id',
        'project_id',
        'opportunity_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'limit_date' => 'date',
        'finished_at' => 'datetime',
        'start_time' => 'datetime',
        'limit_time' => 'datetime',
    ];

    // relationships
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
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
}
