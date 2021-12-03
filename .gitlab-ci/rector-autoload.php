<?php

use TYPO3\CMS\Core\Cache\Backend\NullBackend;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Configuration\ConfigurationManager;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Core\ClassLoadingInformation;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Package\UnitTestPackageManager;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

define('PATH_site', rtrim(strtr(getcwd(), '\\', '/'), '/') . '/');
define('PATH_thisScript', PATH_site . 'typo3/index.php');
$_SERVER['SCRIPT_NAME'] = PATH_thisScript;
putenv('TYPO3_PATH_ROOT=' . getcwd());
define('TYPO3_MODE', 'BE');
define('TYPO3_PATH_PACKAGES', __DIR__ . '/typo3_src/vendor/');

$classLoaderFilepath = TYPO3_PATH_PACKAGES . 'autoload.php';

$classLoader = require $classLoaderFilepath;

$requestType = SystemEnvironmentBuilder::REQUESTTYPE_BE | SystemEnvironmentBuilder::REQUESTTYPE_CLI;
SystemEnvironmentBuilder::run(0, $requestType);

Bootstrap::initializeClassLoader($classLoader);
Bootstrap::baseSetup();

// Initialize default TYPO3_CONF_VARS
$configurationManager = new ConfigurationManager();
$GLOBALS['TYPO3_CONF_VARS'] = $configurationManager->getDefaultConfiguration();

$cache = new PhpFrontend('core', new NullBackend('production', []));
// Set all packages to active
$packageManager = Bootstrap::createPackageManager(UnitTestPackageManager::class, $cache);

GeneralUtility::setSingletonInstance(PackageManager::class, $packageManager);
ExtensionManagementUtility::setPackageManager($packageManager);

ClassLoadingInformation::dumpClassLoadingInformation();
ClassLoadingInformation::registerClassLoadingInformation();
