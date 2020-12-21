<?php

declare(strict_types=1);

namespace Pixelant\Demander\Query\Restriction;


use Pixelant\Demander\Service\DemandService;
use TYPO3\CMS\Core\Database\Query\Expression\CompositeExpression;
use TYPO3\CMS\Core\Database\Query\Expression\ExpressionBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\QueryRestrictionInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DemandRestriction implements QueryRestrictionInterface
{
    /**
     * @inheritDoc
     */
    public function buildExpression(array $queriedTables, ExpressionBuilder $expressionBuilder): CompositeExpression
    {
        return GeneralUtility::makeInstance(DemandService::class)
            ->getRestrictions($queriedTables, $expressionBuilder);
    }
}
