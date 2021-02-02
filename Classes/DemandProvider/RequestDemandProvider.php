<?php

declare(strict_types=1);

namespace Pixelant\Demander\DemandProvider;

use Pixelant\Demander\Service\RequestSingleton;

class RequestDemandProvider implements DemandProviderInterface
{
    public function getDemand(): array
    {
        $request = RequestSingleton::getInstance()->getRequest();
        $demands = $request->getParsedBody()['d'];

        if ($demands) {
            return $demands;
        }

        return [];
    }
}
