<?php

declare(strict_types=1);


namespace Pixelant\Demander\Utility;


use TYPO3\CMS\Core\Database\Query\Expression\CompositeExpression;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

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

    }

    /**
     * Returns tablename-fieldname as [tablename, fieldname]
     *
     * @param string $string
     * @return array|null
     */
    public static function propertyNameToTableAndFieldName(string $string): ?array
    {

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

    }

    /**
     * Converts a demand array into a composite query expression
     *
     * @param array $array
     * @param QueryBuilder $queryBuilder
     * @return CompositeExpression
     */
    public static function toExpression(array $array, QueryBuilder $queryBuilder): CompositeExpression
    {

    }
}
