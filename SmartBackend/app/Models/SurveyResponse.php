<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SurveyResponse extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'favorite_framework',
        'experience_level',
        'programming_languages',
        'teamwork_rating',
        'agile_experience',
        'completed_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'programming_languages' => 'array',
        'agile_experience' => 'boolean',
        'teamwork_rating' => 'integer',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the survey response.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the survey response is completed.
     */
    public function isCompleted(): bool
    {
        return !is_null($this->completed_at);
    }

    /**
     * Mark the survey response as completed.
     */
    public function markAsCompleted(): void
    {
        $this->update(['completed_at' => now()]);
    }
}
