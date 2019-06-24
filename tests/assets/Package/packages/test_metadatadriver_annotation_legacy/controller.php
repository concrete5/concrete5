<?php

namespace Concrete\Package\TestMetadatadriverAnnotationLegacy;

use Concrete\Core\Package\Package;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Controller test addon - testing metadatadriver with legacy annotation driver.
 *
 * @author markus.liechti
 */
class Controller extends Package
{
    protected $pkgHandle = 'test_metadatadriver_annotation_legacy';
    protected $appVersionRequired = '5.7.4';
    protected $pkgVersion = '0.0.1';

    public function getPackageDescription()
    {
        return t('Test addon registers entities via an annotation legacy blowser');
    }

    public function getPackageName()
    {
        return t('Test addon - uses annotation legacy driver');
    }
}
