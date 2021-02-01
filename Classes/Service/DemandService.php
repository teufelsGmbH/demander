<?php

declare(strict_types=1);

namespace Pixelant\Demander\Service;

use Pixelant\Demander\DemandProvider\DemandProviderInterface;
use Pixelant\Demander\Utility\ConfigurationUtility;
use Pixelant\Demander\Utility\DemandArrayUtility;
use Pixelant\Demander\Utility\UiArrayUtility;
use TYPO3\CMS\Core\Database\Query\Expression\CompositeExpression;
use TYPO3\CMS\Core\Database\Query\Expression\ExpressionBuilder;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Main API entry point for using demands from the Demander Extension.
 */
class DemandService implements \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * Get active demand restrictions using configured DemandProviders.
     *
     * @param array $tables Array of tables, where array key is table alias and value is a table name
     * @param ExpressionBuilder $expressionBuilder
     * @return CompositeExpression
     */
    public function getRestrictions(
        array $tables,
        ExpressionBuilder $expressionBuilder
    ): CompositeExpression {
        $demandProviders = $this->getConfiguredDemandProviders();

        return $this->getRestrictionsFromDemandProviders($demandProviders, $tables, $expressionBuilder);
    }

    /**
     * Get active demand restrictions using provided DemandProviders.
     *
     * @param array<DemandProviderInterface> $demandProviders Array of FQCNs
     * @param array $tables Array of tables, where array key is table alias and value is a table name
     * @param ExpressionBuilder $expressionBuilder
     * @return CompositeExpression
     */
    public function getRestrictionsFromDemandProviders(
        array $demandProviders,
        array $tables,
        ExpressionBuilder $expressionBuilder
    ): CompositeExpression {
        $demandArray = [];

        foreach ($demandProviders as $demandProvider) {
            ArrayUtility::mergeRecursiveWithOverrule($demandArray, $demandProvider->getDemand());
        }

        return $this->getRestrictionsFromDemandArray($demandArray, $tables, $expressionBuilder);
    }

    /**
     * Get active demand restrictions using provided DemandProviders.
     *
     * @param array $demandArray Demand array
     * @param array $tables Array of tables, where array key is table alias and value is a table name
     * @param ExpressionBuilder $expressionBuilder
     * @return CompositeExpression
     */
    public function getRestrictionsFromDemandArray(
        array $demandArray,
        array $tables,
        ExpressionBuilder $expressionBuilder
    ): CompositeExpression {
        $demandArray = DemandArrayUtility::restrictionsToInt($demandArray);
        $properties = $this->getPropertiesForDemandedTables($tables, $demandArray);
        $expressions = [];

        foreach ($properties as $key => $property) {
            if ($key === 'or' || $key === 'and') {
                $expressions[] = DemandArrayUtility::toExpression($properties[$key], $expressionBuilder, $key);
            } else {
                $expressions[] = DemandArrayUtility::toExpression($property, $expressionBuilder);
            }
        }

        $defaultConjunction = ConfigurationUtility::getExtensionConfiguration()['defaultConjunction'];

        if ($defaultConjunction === 'or') {
            return $expressionBuilder->orX(...$expressions);
        }

        return $expressionBuilder->andX(...$expressions);
    }

    /**
     * Returns an array of UI configurations for $propertyNames.
     *
     * @see DemandService::getUiConfigurationForProperty()
     * @param array $propertyNames Property names as [tablename-fieldname, tablename-fieldname]
     * @return array
     */
    public function getUiConfigurationForProperties(array $propertyNames): array
    {
        $uiConfiguration = [];

        foreach ($propertyNames as $propertyName) {
            $uiConfiguration[$propertyName] = $this->getUiConfigurationForProperty($propertyName);
        }

        return $uiConfiguration;
    }

    /**
     * Returns a UI configuration array for $propertyName.
     *
     * This array is based on the TCA, but overridden by values in TypoScript: `config.tx_demander.ui`.
     *
     * The array serves as a basis for rendering frontend filtering forms.
     *
     * A configuration array for a slider for selecting values 1-100 could look like this:
     *
     * [
     *     'label' => 'Field label',
     *     'type' => 'slider',
     *     'min' => 1,
     *     'max' => 100,
     * ]
     *
     * A configuration array for a drop-down menu could look like this:
     *
     * [
     *     'label' => 'Field label',
     *     'type' => 'select',
     *     'values' => [
     *         'a' => 'Option A',
     *         'b' => 'Option B',
     *         'c' => 'Option C',
     *     ]
     * ]
     *
     * @param string $propertyName
     * @return array
     */
    public function getUiConfigurationForProperty(string $propertyName): array
    {
        [$table, $field] = UiArrayUtility::propertyNameToTableAndFieldName($propertyName);
        $tcaConfiguration = $GLOBALS['TCA'][$table]['columns'][$field];

        if (!$tcaConfiguration) {
            return [];
        }

        $config = ConfigurationUtility::getExtensionConfiguration()[$table][$field];

        return UiArrayUtility::overrideProperties($config, $tcaConfiguration);
    }

    /**
     * Returns an array of outer bounds (i.e. min/max values) for the property names.
     *
     * @see DemandService::getInnerBoundsForProperty()
     * @param array $propertyNames Property names as [tablename-fieldname, tablename-fieldname]
     * @return array
     */
    public function getOuterBoundsForProperties(array $propertyNames): array
    {
        $outerBounds = [];

        foreach ($propertyNames as $propertyName) {
            $outerBounds[$propertyName] = $this->getOuterBoundsForProperty($propertyName);
        }

        return $outerBounds;
    }

    /**
     * Return outer bounds (i.e. min/max values) for the property without any demand restrictions.
     *
     * For a slider, an array with [min, max] would be correct output.
     * For a drop-down or checkboxes, and array of all available values as key-value pairs would be correct output.
     * For freetext fields, we can't return any value.
     *
     * @param string $propertyName tablename-fieldname
     * @return array of values
     */
    public function getOuterBoundsForProperty(string $propertyName): array
    {
        $outerBounds = [];
        $uiConfiguration = $this->getUiConfigurationForProperty($propertyName);
        $type = $uiConfiguration['config']['type'];

        if ($type === 'select' || $type === 'check' || $type === 'radio') {
            $outerBounds = $uiConfiguration['config']['items'];
        }

        return $outerBounds;
    }

    public function getInnerBoundsForProperties(array $propertyNames): array
    {
        $innerBounds = [];

        foreach ($propertyNames as $propertyName) {
            $innerBounds[$propertyName] = $this->getInnerBoundsForProperty($propertyName);
        }

        return $innerBounds;
    }

    /**
     * Return inner bounds (i.e. currently active restriction values) for $propertyName.
     *
     * For a slider, an array with [selected min, selected max] would be correct output.
     * For a drop-down or checkboxes, and array of all selected values would be correct output.
     * For freetext fields, the current value would be correct output.
     *
     * @param string $propertyName
     * @return array
     */
    public function getInnerBoundsForProperty(string $propertyName): array
    {
        $innerBounds = [];
        $demands = $this->getDemandsFromDemandProviders();
        $restrictions = array_column($demands, $propertyName);

        foreach ($restrictions as $restriction) {
            if (is_array($restriction['value'])) {
                return $restriction['value'];
            }
            $innerBounds[] = $restriction['value'];
        }

        return $innerBounds;
    }

    /**
     * Returns DemandProvider objects configured in TypoScript, in order of execution.
     *
     * @return array<DemandProviderInterface>
     */
    protected function getConfiguredDemandProviders(): array
    {
        $config = ConfigurationUtility::getExtensionConfiguration();
        $demandProviders = [];

        foreach ($config['demandProviders'] as $id => $demandProvider) {
            $demandProviders[$id] = GeneralUtility::makeInstance($demandProvider);
        }

        return $demandProviders;
    }

    /**
     * Returns summary array with all demands from demand providers.
     *
     * @return array
     */
    protected function getDemandsFromDemandProviders(): array
    {
        $demands = [];
        $demandProviders = $this->getConfiguredDemandProviders();

        foreach ($demandProviders as $id => $object) {
            $demand = $object->getDemand();
            $demands = array_merge_recursive($demands, $demand);
        }

        return $demands;
    }

    /**
     * Resolving properties from tables given in demands.
     *
     * @param array $tables
     * @param array $demands
     * @return array
     */
    protected function getPropertiesForDemandedTables(array $tables, array $demands): array
    {
        $properties = ConfigurationUtility::getExtensionConfiguration()['properties'];
        $demandedProperties = [];

        if (!$demandedProperties['or'] && !$demandedProperties['and']) {
            if ($demands['or'] || $demands['and']) {
                $or = ($demands['or']) ? 'or' : '';
                $and = ($demands['and']) ? 'and' : '';

                if ($or !== '') {
                    foreach ($demands[$or] as $demandKey => $demand) {
                        foreach ($properties as $key => $property) {
                            foreach ($tables as $alias => $table) {
                                if ($demandKey === $key && $table === $property['table']) {
                                    $demandedProperties[$or][$key] = $property;
                                }
                            }
                        }
                    }
                }

                if ($and !== '') {
                    foreach ($demands[$and] as $demandKey => $demand) {
                        foreach ($properties as $key => $property) {
                            foreach ($tables as $alias => $table) {
                                if ($demandKey === $key && $table === $property['table']) {
                                    $demandedProperties[$and][$key] = $property;
                                }
                            }
                        }
                    }
                }
            }
        }

        foreach ($tables as $alias => $table) {
            foreach ($properties as $key => $property) {
                if ($demandedProperties['or'][$key] || $demandedProperties['and'][$key]) {
                    if ($table === $property['table']) {
                        $demandedProperties['or'][$key]['alias'] = $alias;
                    }
                } else {
                    if ($table === $property['table']) {
                        $demandedProperties[$key] = $property;
                        $demandedProperties[$key]['alias'] = $alias;
                    }
                }
            }
        }

        $filteredDemands = $this->filterDemandedProperties($demandedProperties, $demands);

        return array_merge_recursive($demandedProperties, $filteredDemands);
    }

    /**
     * Add sorting to QueryBuilder from demands.
     *
     * @param array $table Array of tables where key used as alias and value used as tablename.
     * @param QueryBuilder $queryBuilder
     * @return QueryBuilder
     */
    public function getSortBy(array $table, QueryBuilder $queryBuilder): QueryBuilder
    {
        $demands = $this->getDemandsFromDemandProviders();
        $sortingArguments = $demands['orderBy'] ?? [];
        $properties = ConfigurationUtility::getExtensionConfiguration()['properties'];

        foreach ($sortingArguments as $argument) {
            [$propertyName, $orderingDirection] = GeneralUtility::trimExplode(',', $argument);
            $property = $properties[$propertyName];

            if (null === $property) {
                throw new \UnexpectedValueException(
                    'Demanded property does not exist!'
                );
            }

            $propertyTable = $property['table'];
            $propertyField = $property['field'];
            $tableAlias = $propertyTable;

            foreach ($table as $alias => $tableName) {
                if ($propertyTable === $tableName) {
                    $tableAlias = $alias;
                }
            }

            if ($orderingDirection) {
                $queryBuilder->addOrderBy($tableAlias . '.' . $propertyField, strtoupper($orderingDirection));
            } else {
                $queryBuilder->addOrderBy($tableAlias . '.' . $propertyField);
            }
        }

        return $queryBuilder;
    }

    /**
     * Filter demands for existing only properties.
     *
     * @param array $properties
     * @param array $demands
     * @return array
     */
    public function filterDemandedProperties(array $properties, array $demands): array
    {
        $filteredProperties = [];

        foreach ($demands as $key => $demand) {
            foreach ($properties as $property) {
                if ($key === 'or' || $key === 'and') {
                    $filteredProperties[$key] = $this->filterDemandedProperties($properties, $demand);
                } else {
                    if (array_key_exists($key, $properties) || array_key_exists($key, $property)) {
                        $filteredProperties[$key] = $demand;
                    }
                }
            }
        }

        return $filteredProperties;
    }
}
