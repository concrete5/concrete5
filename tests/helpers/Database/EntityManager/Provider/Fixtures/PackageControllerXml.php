<?php

namespace Concrete\TestHelpers\Database\EntityManager\Provider\Fixtures;

use Concrete\Core\Database\EntityManager\Provider\ProviderInterface;
use Concrete\Core\Database\EntityManager\Provider\XmlProvider;
use Concrete\Core\Package\Package;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Controller test addon - testing xml metadatadriver.
 *
 * @author @author Markus Liechti <markus@liechti.io>
 */
class PackageControllerXml extends Package implements ProviderInterface
{
    protected $pkgHandle = 'test_metadatadriver_xml';
    protected $appVersionRequired = '5.8.0';
    protected $pkgVersion = '0.0.1';

    public function getPackageDescription()
    {
        return t('Test addon registers entities via the xml driver');
    }

    public function getPackageName()
    {
        return t('Test addon - uses xml driver');
    }

    /**
     * Return customized metadata driver wrapped in a XMLProvider for doctrine orm
     * Path: {package}/config/xml.
     *
     * @return XmlProvider
     */
    public function getDrivers()
    {
        $xmlProvider = new XmlProvider($this);

        return $xmlProvider->getDrivers();
    }
}
