<?php

declare(strict_types=1);


namespace Pixelant\Demander\Utility;


use TYPO3\CMS\Core\Database\Query\Expression\CompositeExpression;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Expression\ExpressionBuilder;

/**
 * Utility for processing and modifying demand arrays
 */
class DemandArrayUtility
{
    /**
     * Returns a string with tablename-fieldname
     *
     * @param string $table
     * @param string $field
     * @return string
     */
    public static function tableAndFieldNameToPropertyName(string $table, string $field): string
    {
        return $table.'-'.$field;
    }

    /**
     * Returns tablename-fieldname as [tablename, fieldname]
     *
     * @param string $string
     * @return array|null
     */
    public static function propertyNameToTableAndFieldName(string $string): ?array
    {
        return explode('-', $string);
    }

    /**
     * Merges two demand arrays intelligently and recursively
     *
     * If 'operand' => '-', two items in 'values' => [max, min] are maintained
     * If 'operand' => 'in', items are appended to 'values' => [...] (unique values)
     *
     * @param array ...$arrays
     * @return array
     */
    public static function merge(array ...$arrays): array
    {

    }

    /**
     * Filter a demand array to include only requests tables and their relevant relations
     *
     * @param array $tables Array of tables, where array key is table alias and value is a table name
     * @param array $array The demand array
     * @return array Filtered demand array
     */
    public static function filterByTables(array $tables, array $array): array
    {
        $filteredArray = [];

        foreach ($tables as $alias => $table) {
            foreach ($array as $tableFieldAlias => $values){
                if ($values['operator']){
                    $tableName = explode('-', $tableFieldAlias)[0];

                    if ($tableName === $table){
                        $filteredArray[$tableFieldAlias] = $values;
                    }
                }else{
                    $filteredArray[$tableFieldAlias] = DemandArrayUtility::filterByTables($tables,$values);
                }
            }
        }

        return $filteredArray;
    }

    /**
     * Converts a demand array into a composite query expression
     *
     * @param array $array
     * @param ExpressionBuilder $expressionBuilder
     * @param string $conjunction
     * @return CompositeExpression
     */
    public static function toExpression(array $array, ExpressionBuilder $expressionBuilder, string $conjunction = ''): CompositeExpression
    {
        $expressionsArr = [];

        foreach ($array as $fieldKey => $field){
            foreach ($field as $properties) {
                $expressionsArr[] = DemandArrayUtility::convertRestrictionToExpression($fieldKey, $properties, $expressionBuilder);
            }
        }

        switch ($conjunction){
            case 'or':
                return $expressionBuilder->orX(...$expressionsArr);
            default:
                return $expressionBuilder->andX(...$expressionsArr);
        }
    }

    /**
     * Removes dots at the end of array keys when config fetches from TypoScript.
     *
     * @param array $array
     * @return array
     */
    public static function removeDotsFromKeys(array $array): array
    {
        $filteredArray = [];

        foreach ($array as $key => $value){
            if ($value['operator']){
                $key = trim($key, '.');
                $filteredArray[$key] = $value;
            }else{
                $key = trim($key, '.');
                $filteredArray[$key] = DemandArrayUtility::removeDotsFromKeys($value);
            }
        }
        return $filteredArray;
    }

    /**
     * Looping through restrictions and looks for numeric values to transform it into integers.
     *
     * @param array $restrictionsArray
     * @return array
     */
    public static function restrictionsToInt(array $restrictionsArray): array
    {
        $restrictions = [];

        foreach ($restrictionsArray as $key => $restriction) {
            if ($restriction['value']) {
                $value = (is_numeric($restriction['value'])) ? (int)$restriction['value'] : $restriction['value'];
                $restriction = array_replace($restriction, ['value' => $value]);
                $restrictions[$key] = $restriction;
            }else{
                $restrictions[$key] = DemandArrayUtility::restrictionsToInt($restriction);
            }
        }

        return $restrictions;
    }

    /**
     * @param string $fieldname
     * @param array $restrictions
     * @param ExpressionBuilder $expressionBuilder
     * @return string
     */
    public static function convertRestrictionToExpression(string $fieldname, array $restrictions, ExpressionBuilder $expressionBuilder): string
    {
        switch ($restrictions['operator']){
            case $expressionBuilder::EQ:
                return $expressionBuilder->eq($fieldname, $restrictions['value']);
            case $expressionBuilder::GT:
                return $expressionBuilder->gt($fieldname, $restrictions['value']);
            case $expressionBuilder::GTE:
                return $expressionBuilder->gte($fieldname, $restrictions['value']);
            case $expressionBuilder::LT:
                return $expressionBuilder->lt($fieldname, $restrictions['value']);
            case $expressionBuilder::LTE:
                return $expressionBuilder->lte($fieldname, $restrictions['value']);
            case '-':
                return $expressionBuilder->andX(
                    $expressionBuilder->gte($fieldname, $restrictions['value'][0]),
                    $expressionBuilder->lte($fieldname, $restrictions['value'][1])
                )->__toString();
            default:
                return $fieldname;
        }
    }

    /**
     * Resolve field properties from alias.
     *
     * @param array $alias
     * @param null $rootKey
     * @return array
     */
    public static function getFieldPropertiesFromAlias(array $alias, $rootKey = null): array
    {
        $fieldProperties = [];

        if ($rootKey !== null){
            foreach ($alias as $key => $subAlias){
                $fieldName = DemandArrayUtility::getFieldPropertiesFromAlias([$key])[0];
                $fieldProperties[$rootKey][$fieldName] = [$subAlias];
            }
            return $fieldProperties;
        }
        $fieldProperties[] = explode('-',  $alias[0])[1];
        return $fieldProperties;
    }
}
