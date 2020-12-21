<?php

declare(strict_types=1);


namespace Pixelant\Demander\Service;

use Pixelant\Demander\DemandProvider\DemandProviderInterface;
use TYPO3\CMS\Core\Database\Query\Expression\CompositeExpression;
use TYPO3\CMS\Core\Database\Query\Expression\ExpressionBuilder;

/**
 * Main API entry point for using demands from the Demander Extension
 */
class DemandService implements \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * Get active demand restrictions using configured DemandProviders
     *
     * @param array $tables Array of tables, where array key is table alias and value is a table name
     * @param ExpressionBuilder $expressionBuilder
     * @return CompositeExpression
     */
    public function getRestrictions(
        array $tables,
        ExpressionBuilder $expressionBuilder
    ): CompositeExpression
    {
        $demandProviders = $this->getConfiguredDemandProviders();

        return $this->getRestrictionsFromDemandProviders($demandProviders, $tables, $expressionBuilder);
    }

    /**
     * Get active demand restrictions using provided DemandProviders
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
    ): CompositeExpression
    {
        // TODO: Set $demandArray to merged, returned values of $demandProviders

        return $this->getRestrictionsFromDemandArray($demandArray, $tables, $expressionBuilder);
    }

    /**
     * Get active demand restrictions using provided DemandProviders
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
    ): CompositeExpression
    {
        // TODO: Most of the code for getting restrictions in this class goes here
    }

    /**
     * Returns an array of UI configurations for $propertyNames
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
     * Returns a UI configuration array for $propertyName
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
        // TODO: Generate and return UI configuration
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
        // TODO: Implement calculation of outer bounds. Use getUiConfigurationForProperty() as much as possible.
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
     * Return inner bounds (i.e. currently active restriction values) for $propertyName
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
        // TODO: Implement calculation of inner bounds
    }

    /**
     * Returns DemandProvider objects configured in TypoScript, in order of execution
     *
     * @return array<DemandProviderInterface>
     */
    protected function getConfiguredDemandProviders(): array
    {
        // TODO: Initialize DemandProvider objects configured in TypoScript and return in order of execution
    }
}
