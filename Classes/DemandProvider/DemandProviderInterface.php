<?php

declare(strict_types=1);

namespace Pixelant\Demander\DemandProvider;

/**
 * Interface for demand providers
 */
interface DemandProviderInterface
{
    /**
     * Returns a standard demand array
     *
     * [
     *     'table1-field1' => [
     *         'operator' => '='
     *         'value' => 1234
     *     ],
     *     'table1-field2' => [
     *         'operator' => '-'
     *         'value' => [min, max]
     *     ],
     *     'and' => [...],
     *     'or' => [...],
     * ]
     *
     * Operators:
     * =   equals
     * >   greater than
     * >=  greater than or equal to
     * <   less than
     * <=  less than or equal to
     * -   between (i.e. >= and <=)
     * in  in list
     *
     * @return array
     */
    public function getDemand(): array;
}
