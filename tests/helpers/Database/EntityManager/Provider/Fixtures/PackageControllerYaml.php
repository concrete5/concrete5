<?php

namespace Concrete\TestHelpers\Database\EntityManager\Provider\Fixtures;

use Concrete\Core\Application\Application;
use Concrete\Core\Database\EntityManager\Provider\ProviderInterface;
use Concrete\Core\Database\EntityManager\Provider\YamlProvider;
use Concrete\Core\Package\Package;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Controller test addon - testing yaml metadatadriver.
 *
 * @author Markus Liechti <markus@liechti.io>
 */
class PackageControllerYaml extends Package implements ProviderInterface
{
    protected $pkgHandle = 'test_metadatadriver_yaml';
    protected $appVersionRequired = '5.8.0';
    protected $pkgVersion = '0.0.1';

    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    public function getPackageDescription()
    {
        return t('Test addon registers entities via the yaml driver');
    }

    public function getPackageName()
    {
        return t('Test addon - uses yaml driver');
    }

    /**
     * Return customized metadata driver wrapped in a YamlProvider for doctrine orm
     * Path: {package}/config/yaml.
     *
     * @return YamlProvider
     */
    public function getDrivers()
    {
        $yamlProvider = new YamlProvider($this);

        return $yamlProvider->getDrivers();
    }
}
