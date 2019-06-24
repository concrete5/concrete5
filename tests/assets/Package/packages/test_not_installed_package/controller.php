<?php

namespace Concrete\Package\TestNotInstalledPackage;

defined('C5_EXECUTE') or die('Access Denied.');
/**
 * Controller test addon - this package shouldn't be installed by any test case.
 *
 * @author Markus Liechti <markus@liechti.io>
 */
class Controller extends \Concrete\Core\Package\Package
{
    protected $pkgHandle = 'test_not_installed_package';
    protected $appVersionRequired = '8.0.0';
    protected $pkgVersion = '0.0.1';

    public function getPackageDescription()
    {
        return t("Test addon which shouldn't be installed");
    }

    public function getPackageName()
    {
        return t('Test addon - not installed package');
    }
}
