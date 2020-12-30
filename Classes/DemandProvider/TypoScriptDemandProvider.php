<?php

declare(strict_types=1);


namespace Pixelant\Demander\DemandProvider;

use Pixelant\Demander\Utility\DemandArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;


class TypoScriptDemandProvider implements DemandProviderInterface
{
    public function getDemand(): array
    {
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        $config = $configurationManager->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_FULL_TYPOSCRIPT)['config.']['tx_demander.'];

        if ($config['demands.']){
            return DemandArrayUtility::removeDotsFromKeys($config['demands.']);
        }
        return [];
    }
}
