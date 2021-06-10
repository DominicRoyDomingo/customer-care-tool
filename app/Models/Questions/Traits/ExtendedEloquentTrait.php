<?php

namespace App\Models\Questions\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait ExtendedEloquentTrait
 * @package App\Traits
 */
trait ExtendedEloquentTrait
{
    /**
     * @param Builder $query
     * @param $column
     * @param string $direction
     * @param array $allowedColumns
     * @return \Illuminate\Database\Concerns\BuildsQueries|Builder|mixed
     */
    public function scopeOrderParam(Builder $query, $column, $direction = 'asc', $allowedColumns = [])
    {
        return $query->when(in_array($column, $allowedColumns), function(Builder $q) use($column, $direction){
            return $q->orderBy($column, !empty($direction) && $direction == 'desc' ? 'desc': 'asc');
        });
    }
}
