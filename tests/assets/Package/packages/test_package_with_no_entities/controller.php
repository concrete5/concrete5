<?php

namespace Concrete\Package\TestPackageWithNoEntities;

defined('C5_EXECUTE') or die('Access Denied.');
/**
 * Controller test addon - testing metadatadriver with legacy annotation driver.
 *
 * @author markus.liechti
 */
class Controller extends \Concrete\Core\Package\Package
{
    protected $pkgHandle = 'test_package_with_no_entities';
    protected $appVersionRequired = '8.0.0';
    protected $pkgVersion = '0.0.1';

    public function getPackageDescription()
    {
        return t('Test addon installation containing no entities');
    }

    public function getPackageName()
    {
        return t('Test addon - with no entites');
    }
}
