<?php

declare(strict_types=1);

namespace Pixelant\Demander\DemandProvider;

use Pixelant\Demander\Service\RequestSingleton;
use Psr\Http\Message\ServerRequestInterface;

class RequestDemandProvider implements DemandProviderInterface
{
    public function getDemand(): array
    {
        $request = RequestSingleton::getInstance()->getRequest() ?? null;
        if ($request instanceof ServerRequestInterface) {
            $demands = $request->getParsedBody()['d'];
        }

        if ($demands) {
            return $demands;
        }

        return [];
    }
}
