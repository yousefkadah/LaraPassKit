<?php

namespace App\Services;

use App\Models\PassTypeSample;
use Illuminate\Database\Eloquent\Builder;

class PassTypeSampleService
{
    /**
     * @param  array{pass_type?: string, platform?: string, source?: string}  $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listForUser(int $userId, array $filters, int $perPage)
    {
        $query = PassTypeSample::query();

        $source = $filters['source'] ?? 'all';
        if ($source === 'system') {
            $query->where('source', 'system');
        } elseif ($source === 'user') {
            $query->where('source', 'user')->where('owner_user_id', $userId);
        } else {
            $query->where(function (Builder $builder) use ($userId): void {
                $builder
                    ->where('source', 'system')
                    ->orWhere(function (Builder $nested) use ($userId): void {
                        $nested->where('source', 'user')->where('owner_user_id', $userId);
                    });
            });
        }

        if (! empty($filters['pass_type'])) {
            $query->where('pass_type', $filters['pass_type']);
        }

        if (! empty($filters['platform'])) {
            $platform = $filters['platform'];
            $query->where(function (Builder $builder) use ($platform): void {
                $builder->where('platform', $platform)->orWhereNull('platform');
            });
            $query->orderByRaw('case when platform is null then 1 else 0 end');
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function createForUser(int $userId, array $payload): PassTypeSample
    {
        return PassTypeSample::create([
            ...$payload,
            'owner_user_id' => $userId,
            'source' => 'user',
        ]);
    }
}
