<?php

namespace Concrete\Package\TestMetadatadriverAnnotationDefaultCoreExtension;

defined('C5_EXECUTE') or die('Access Denied.');
/**
 * Controller test addon - testing metadatadriver with legacy annotation driver.
 *
 * @author markus.liechti
 */
class Controller extends \Concrete\Core\Package\Package
{
    protected $pkgHandle = 'test_metadatadriver_annotation_default_core_extension';
    protected $appVersionRequired = '8.0.0';
    protected $pkgVersion = '0.0.1';
    // The important different we want to test
    protected $pkgAutoloaderMapCoreExtensions = true;

    public function getPackageDescription()
    {
        return t('Test addon registers entities via the default annotation driver and registers it self as a core extension');
    }

    public function getPackageName()
    {
        return t('Test addon - uses default annotation driver an acts as a core extension');
    }
}
