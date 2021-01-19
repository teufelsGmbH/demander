<?php

declare(strict_types=1);

namespace Pixelant\Demander\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;

/**
 * Utility for demander typoscript configuration.
 */
class ConfigurationUtility
{
    /**
     * @return array
     */
    public static function getExtensionConfiguration(): array
    {
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        $config = $configurationManager->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_FULL_TYPOSCRIPT)['config.']['tx_demander.'];

        return DemandArrayUtility::removeDotsFromKeys($config);
    }
}
