<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

abstract class BaseRepository
{
    protected $model;
    protected $cacheTime = 3600; // 1 hour
    protected $cachePrefix;

    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->cachePrefix = strtolower(class_basename($model));
    }

    /**
     * Get all records
     */
    public function all($columns = ['*']): Collection
    {
        $cacheKey = $this->getCacheKey('all', $columns);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($columns) {
            return $this->model->select($columns)->get();
        });
    }

    /**
     * Get paginated records
     */
    public function paginate($perPage = 15, $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->select($columns)->paginate($perPage);
    }

    /**
     * Find record by ID
     */
    public function find($id, $columns = ['*']): ?Model
    {
        $cacheKey = $this->getCacheKey('find', [$id, $columns]);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($id, $columns) {
            return $this->model->select($columns)->find($id);
        });
    }

    /**
     * Find record by ID or fail
     */
    public function findOrFail($id, $columns = ['*']): Model
    {
        return $this->model->select($columns)->findOrFail($id);
    }

    /**
     * Find by specific field
     */
    public function findBy($field, $value, $columns = ['*']): ?Model
    {
        $cacheKey = $this->getCacheKey('findBy', [$field, $value, $columns]);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($field, $value, $columns) {
            return $this->model->select($columns)->where($field, $value)->first();
        });
    }

    /**
     * Get records by specific field
     */
    public function getBy($field, $value, $columns = ['*']): Collection
    {
        $cacheKey = $this->getCacheKey('getBy', [$field, $value, $columns]);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($field, $value, $columns) {
            return $this->model->select($columns)->where($field, $value)->get();
        });
    }

    /**
     * Create new record
     */
    public function create(array $data): Model
    {
        DB::beginTransaction();
        
        try {
            $model = $this->model->create($data);
            
            $this->clearCache();
            
            DB::commit();
            
            return $model;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update record
     */
    public function update($id, array $data): Model
    {
        DB::beginTransaction();
        
        try {
            $model = $this->findOrFail($id);
            $model->update($data);
            
            $this->clearCache();
            
            DB::commit();
            
            return $model->fresh();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update or create record
     */
    public function updateOrCreate(array $attributes, array $values = []): Model
    {
        DB::beginTransaction();
        
        try {
            $model = $this->model->updateOrCreate($attributes, $values);
            
            $this->clearCache();
            
            DB::commit();
            
            return $model;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Delete record
     */
    public function delete($id): bool
    {
        DB::beginTransaction();
        
        try {
            $model = $this->findOrFail($id);
            $deleted = $model->delete();
            
            $this->clearCache();
            
            DB::commit();
            
            return $deleted;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Bulk delete
     */
    public function bulkDelete(array $ids): int
    {
        DB::beginTransaction();
        
        try {
            $deleted = $this->model->whereIn('id', $ids)->delete();
            
            $this->clearCache();
            
            DB::commit();
            
            return $deleted;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Get count
     */
    public function count(): int
    {
        $cacheKey = $this->getCacheKey('count');
        
        return Cache::remember($cacheKey, $this->cacheTime, function () {
            return $this->model->count();
        });
    }

    /**
     * Check if record exists
     */
    public function exists($id): bool
    {
        return $this->model->where('id', $id)->exists();
    }

    /**
     * Get latest records
     */
    public function latest($limit = 10, $columns = ['*']): Collection
    {
        $cacheKey = $this->getCacheKey('latest', [$limit, $columns]);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($limit, $columns) {
            return $this->model->select($columns)->latest()->limit($limit)->get();
        });
    }

    /**
     * Get oldest records
     */
    public function oldest($limit = 10, $columns = ['*']): Collection
    {
        $cacheKey = $this->getCacheKey('oldest', [$limit, $columns]);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($limit, $columns) {
            return $this->model->select($columns)->oldest()->limit($limit)->get();
        });
    }

    /**
     * Search records
     */
    public function search($query, $fields = [], $limit = null): Collection
    {
        $modelQuery = $this->model->newQuery();
        
        if (!empty($fields)) {
            $modelQuery->where(function ($q) use ($query, $fields) {
                foreach ($fields as $field) {
                    $q->orWhere($field, 'LIKE', "%{$query}%");
                }
            });
        }
        
        if ($limit) {
            $modelQuery->limit($limit);
        }
        
        return $modelQuery->get();
    }

    /**
     * Get records with relationships
     */
    public function with($relations): self
    {
        $this->model = $this->model->with($relations);
        return $this;
    }

    /**
     * Apply where condition
     */
    public function where($field, $operator = null, $value = null): self
    {
        $this->model = $this->model->where($field, $operator, $value);
        return $this;
    }

    /**
     * Apply whereIn condition
     */
    public function whereIn($field, array $values): self
    {
        $this->model = $this->model->whereIn($field, $values);
        return $this;
    }

    /**
     * Apply orderBy
     */
    public function orderBy($field, $direction = 'asc'): self
    {
        $this->model = $this->model->orderBy($field, $direction);
        return $this;
    }

    /**
     * Apply limit
     */
    public function limit($limit): self
    {
        $this->model = $this->model->limit($limit);
        return $this;
    }

    /**
     * Get query builder
     */
    public function query()
    {
        return $this->model->newQuery();
    }

    /**
     * Get model instance
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Reset model to original state
     */
    public function resetModel(): self
    {
        $this->model = new $this->model;
        return $this;
    }

    /**
     * Generate cache key
     */
    protected function getCacheKey($method, $params = []): string
    {
        $key = $this->cachePrefix . '.' . $method;
        
        if (!empty($params)) {
            $key .= '.' . md5(serialize($params));
        }
        
        return $key;
    }

    /**
     * Clear all cache for this model
     */
    protected function clearCache(): void
    {
        $cacheDriver = config('cache.default');
        
        // Handle different cache drivers
        if ($cacheDriver === 'redis') {
            $pattern = $this->cachePrefix . '.*';
            
            // Get all cache keys matching pattern
            $keys = Cache::getRedis()->keys($pattern);
            
            foreach ($keys as $key) {
                Cache::forget(str_replace(config('cache.prefix') . ':', '', $key));
            }
        } else {
            // For non-Redis drivers (database, file, etc.), we maintain a list
            // of common cache keys that we know about and clear them individually
            $commonMethods = ['all', 'latest', 'oldest', 'statistics', 'count'];
            
            foreach ($commonMethods as $method) {
                // Clear cache keys that don't have parameters
                Cache::forget($this->getCacheKey($method));
                
                // For parameterized cache keys, we can't efficiently clear all variations
                // so we rely on TTL expiration. In production, consider using cache tags.
            }
            
            // Clear some common parameterized cache patterns
            // This is a best-effort approach for database/file cache drivers
            $commonParams = [
                [['*']], // for 'all' method with columns
                [[10, ['*']]], // for 'latest' method with default params
                [[15, ['*']]], // for pagination
            ];
            
            foreach ($commonMethods as $method) {
                foreach ($commonParams as $params) {
                    Cache::forget($this->getCacheKey($method, $params));
                }
            }
        }
    }

    /**
     * Get statistics
     */
    public function getStatistics(): array
    {
        $cacheKey = $this->getCacheKey('statistics');
        
        return Cache::remember($cacheKey, $this->cacheTime, function () {
            return [
                'total' => $this->model->count(),
                'today' => $this->model->whereDate('created_at', today())->count(),
                'this_week' => $this->model->whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count(),
                'this_month' => $this->model->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'last_month' => $this->model->whereMonth('created_at', now()->subMonth()->month)
                    ->whereYear('created_at', now()->subMonth()->year)
                    ->count(),
            ];
        });
    }

    /**
     * Bulk update
     */
    public function bulkUpdate(array $ids, array $data): int
    {
        DB::beginTransaction();
        
        try {
            $updated = $this->model->whereIn('id', $ids)->update($data);
            
            $this->clearCache();
            
            DB::commit();
            
            return $updated;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}