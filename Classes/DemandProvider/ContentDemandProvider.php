<?php

declare(strict_types=1);

namespace Pixelant\Demander\DemandProvider;

use TYPO3\CMS\Extbase\Persistence\Generic\Exception\NotImplementedException;

class ContentDemandProvider implements DemandProviderInterface
{
    /**
     * @return array
     * @throws Exception\NotImplementedException
     */
    public function getDemand(): array
    {
        throw new NotImplementedException(__METHOD__, 1614082970);
    }
}
