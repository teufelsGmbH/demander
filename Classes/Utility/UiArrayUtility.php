<?php

declare(strict_types=1);

namespace Pixelant\Demander\Utility;

/**
 * Utility for processing and modifying ui arrays.
 */
class UiArrayUtility
{
    /**
     * Returns tablename-fieldname as [tablename, fieldname].
     *
     * @param string $string
     * @return array|null
     */
    public static function propertyNameToTableAndFieldName(string $string): ?array
    {
        return DemandArrayUtility::propertyNameToTableAndFieldName($string);
    }

    /**
     * Removes dots at the end of array keys when config fetches from TypoScript.
     *
     * @param array $array
     * @return array
     */
    public static function removeDotsFromKeys(array $array): array
    {
        return DemandArrayUtility::removeDotsFromKeys($array);
    }

    /**
     * Looping through restrictions and looks for numeric values to transform it into integers.
     *
     * @param array $restrictionsArray
     * @return array
     */
    public static function restrictionsToInt(array $restrictionsArray): array
    {
        return DemandArrayUtility::restrictionsToInt($restrictionsArray);
    }

    /**
     * Override TCA configuration with given configuration.
     *
     * @param array $overrideProperties Array of properties that needs to be override.
     * @param array $defaultProperties Array of default properties.
     * @return array Returns whole TCA configuration array with override settings.
     */
    public static function overrideProperties(array $overrideProperties, array $defaultProperties): array
    {
        $overrideProperties = self::restrictionsToInt($overrideProperties);
        $filteredArray = [];

        foreach ($overrideProperties as $key => $property) {
            if (array_key_exists($key, $defaultProperties)) {
                $filteredArray[$key] = $property;
            }
        }

        return array_replace_recursive($defaultProperties, $filteredArray);
    }
}
