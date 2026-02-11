<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PassTypeSample extends Model
{
    /** @use HasFactory<\Database\Factories\PassTypeSampleFactory> */
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'owner_user_id',
        'source',
        'name',
        'description',
        'pass_type',
        'platform',
        'fields',
        'images',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fields' => 'array',
            'images' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (PassTypeSample $sample) {
            if (! $sample->getKey()) {
                $sample->setAttribute($sample->getKeyName(), (string) Str::uuid());
            }
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }
}
