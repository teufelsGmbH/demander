<?php

declare(strict_types=1);

namespace Pixelant\Demander\Utility;

use TYPO3\CMS\Core\Database\Query\Expression\CompositeExpression;
use TYPO3\CMS\Core\Database\Query\Expression\ExpressionBuilder;

/**
 * Utility for processing and modifying demand arrays.
 */
class DemandArrayUtility
{
    /**
     * Returns a string with tablename-fieldname.
     *
     * @param string $table
     * @param string $field
     * @return string
     */
    public static function tableAndFieldNameToPropertyName(string $table, string $field): string
    {
        return $table . '-' . $field;
    }

    /**
     * Returns tablename-fieldname as [tablename, fieldname].
     *
     * @param string $string
     * @return array|null
     */
    public static function propertyNameToTableAndFieldName(string $string): ?array
    {
        return explode('-', $string);
    }

    /**
     * Converts a demand array into a composite query expression.
     *
     * @param array $properties
     * @param ExpressionBuilder $expressionBuilder
     * @param string $conjunction
     * @return CompositeExpression
     */
    public static function toExpression(array $properties, ExpressionBuilder $expressionBuilder, string $conjunction = 'and'): CompositeExpression
    {
        $expressionsArr = [];

        if (is_array($properties[array_key_first($properties)])) {
            foreach ($properties as  $property) {
                $expressionsArr[] = self::toExpression($property, $expressionBuilder);
            }
        } else {
            $fieldName = $properties['alias'] . '.' . $properties['field'];
            $tempRestrictions = [];
            $tempConjunction = '';

            if ($properties['additionalRestriction']) {
                foreach ($properties['additionalRestriction'] as $key => $additionalRestriction) {
                    [$table, $field] = self::propertyNameToTableAndFieldName($key);
                    $tempFieldName = $table . '.' . $field;
                    $tempConjunction = $additionalRestriction['conjunction'];
                    $tempRestrictions[] = self::convertRestrictionToExpression($tempFieldName, $additionalRestriction, $expressionBuilder);
                }
            }

            if (!empty($tempRestrictions)) {
                $tempRestrictions[] = self::convertRestrictionToExpression($fieldName, $properties, $expressionBuilder);

                if ($tempConjunction === 'or') {
                    $expressionsArr[] = $expressionBuilder->orX(...$tempRestrictions);
                } else {
                    $expressionsArr[] = $expressionBuilder->andX(...$tempRestrictions);
                }
            } else {
                $expressionsArr[] = self::convertRestrictionToExpression($fieldName, $properties, $expressionBuilder);
            }
        }

        if ($conjunction === 'or') {
            return $expressionBuilder->orX(...$expressionsArr);
        }

        return $expressionBuilder->andX(...$expressionsArr);
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

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if (!is_int($key)) {
                    $key = trim($key, '.');
                }
                $filteredArray[$key] = self::removeDotsFromKeys($value);
            } else {
                if (!is_int($key)) {
                    $key = trim($key, '.');
                }
                $filteredArray[$key] = $value;
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
            if (is_array($restriction)) {
                $restrictions[$key] = self::restrictionsToInt($restriction);
            } else {
                $value = (is_numeric($restriction)) ? (int)$restriction : $restriction;
                $restrictions[$key] = $value;
            }
        }

        return array_replace($restrictionsArray, $restrictions);
    }

    /**
     * @param string $fieldname
     * @param array $restrictions
     * @param ExpressionBuilder $expressionBuilder
     * @return string
     */
    public static function convertRestrictionToExpression(string $fieldname, array $restrictions, ExpressionBuilder $expressionBuilder): string
    {
        switch ($restrictions['operator']) {
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
                    $expressionBuilder->gte($fieldname, $restrictions['value']['min']),
                    $expressionBuilder->lte($fieldname, $restrictions['value']['max'])
                )->__toString();
            case 'in':
                return $expressionBuilder->in($fieldname, $restrictions['value']);
            default:
                return $fieldname;
        }
    }
}
