<?php

namespace Joligoms\SortRelations;

use Laravel\Nova\Nova;

/**
 * Trait SortRelations
 * Based on https://github.com/newtongamajr/nova-sort-relations/tree/bugfix repository
 * @package Joligoms\SortRelations
 */
trait SortRelations
{
    /**
     * The relations that should be "joined" to the index query, making the fields with relations sortable.
     *
     * @var array
     */
    public static $sortRelations = [];

    /**
     * Apply any applicable orderings to the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string $column
     * @param  string $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected static function applyRelationOrderings(string $column, string $direction, $query)
    {
        $sortInformation = static::$sortRelations[$column];

        $resourceModel = $query->getModel();
        $relations = explode('.', $sortInformation['relation']);

        $segmentModel = $resourceModel;

        foreach ($relations as $key => $segmentRelation) {
            $segmentModel = $segmentModel->{$segmentRelation}();

            $foreignKey =  method_exists($segmentModel, "getForeignKeyName")
                ? $segmentModel->getForeignKeyName()
                : $segmentModel->getForeignPivotKeyName();
            $ownerKey = $segmentModel->getOwnerKeyName();

            $child = $segmentModel->getChild();
            $segmentModel = $segmentModel->getModel();
            $query->leftJoin($segmentModel->getConnection()->getDatabaseName() . '.' . $segmentModel->getTable(), $child->qualifyColumn($foreignKey), '=', $segmentModel->qualifyColumn($ownerKey));
        } 

        $sortingModel = $segmentModel;

        $sortingTitle = isset($sortInformation['title']) ? $sortInformation['title'] : Nova::resourceForModel($sortingModel)::$title;
        $query->select($resourceModel->qualifyColumn('*'), $sortingModel->qualifyColumn($sortingTitle).' as '.$column);

        if (is_string($sortInformation['columns'])) {
            $qualified = $sortingModel->qualifyColumn($sortInformation['columns']);
            $query->orderBy($qualified, $direction);
        }
        if (is_array($sortInformation['columns'])) {
            foreach ($sortInformation['columns'] as $orderColumn) {
                $qualified = $sortingModel->qualifyColumn($orderColumn);
                $query->orderBy($qualified, $direction);
            }
        }

        return $query;
    }

    /**
     * Apply any applicable orderings to the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  array $orderings
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected static function applyOrderings($query, array $orderings)
    {
        if (empty($orderings)) {
            return empty($query->orders)
                ? $query->latest($query->getModel()->getQualifiedKeyName())
                : $query;
        }

        $sortRelations = static::$sortRelations;

        foreach ($orderings as $column => $direction) {
            if (is_null($direction))
                $direction = 'asc';
            if (array_key_exists($column, $sortRelations)) {
                $query = self::applyRelationOrderings($column, $direction, $query);
            } else {
                $query->orderBy($column, $direction);
            }
        }

        return $query;
    }
}
