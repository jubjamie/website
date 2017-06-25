<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait ChecksJoins
{
    /**
     * Check if a query has already been joined to a table.
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param                                       $tableName
     * @return bool
     */
    public function alreadyJoined(Builder $query, $tableName)
    {
        $joins = $query->getQuery()->joins;
        if($joins == null) {
            return false;
        }
        foreach($joins as $join) {
            if($join->table == $tableName) {
                return true;
            }
        }
        return false;
    }
}