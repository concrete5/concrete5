<?php

namespace Concrete\Package\TestMetadatadriverYaml;

use Concrete\Core\Database\EntityManager\Provider\ProviderInterface;
use Concrete\Core\Database\EntityManager\Provider\YamlProvider;
use Concrete\Core\Package\Package;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Controller test addon - testing metadatadriver with legacy annotation driver.
 *
 * @author markus.liechti
 */
class Controller extends Package implements ProviderInterface
{
    protected $pkgHandle = 'test_metadatadriver_yaml';
    protected $appVersionRequired = '8.0.0';
    protected $pkgVersion = '0.0.1';

    public function getPackageDescription()
    {
        return t('Test addon registers entities via the yaml driver');
    }

    public function getPackageName()
    {
        return t('Test addon - uses yaml driver');
    }

    /**
     * Return customized metadata driver wrapped in a XMLProvider for doctrine orm
     * Path: {package}/config/yaml.
     *
     * @return YamlProvider
     */
    public function getDrivers()
    {
        $relPath = $this->getRelativePathFromInstallFolder() . REL_DIR_METADATA_YAML;

        return new YamlProvider($relPath);
    }
}
