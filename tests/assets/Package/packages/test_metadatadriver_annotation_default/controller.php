<?php

namespace Concrete\Package\TestMetadatadriverAnnotationDefault;

defined('C5_EXECUTE') or die('Access Denied.');
/**
 * Controller test addon - testing metadatadriver with legacy annotation driver.
 *
 * @author markus.liechti
 */
class Controller extends \Concrete\Core\Package\Package
{
    protected $pkgHandle = 'test_metadatadriver_annotation_default';
    protected $appVersionRequired = '8.0.0';
    protected $pkgVersion = '0.0.1';

    public function getPackageDescription()
    {
        return t('Test addon registers entities via the default annotation driver');
    }

    public function getPackageName()
    {
        return t('Test addon - uses default annotation driver');
    }
}
