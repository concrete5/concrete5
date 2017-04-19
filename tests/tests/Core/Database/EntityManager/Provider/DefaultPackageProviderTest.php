<?php

namespace Concrete\Tests\Core\Database\EntityManager\Provider;

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Database\EntityManager\Provider\DefaultPackageProvider;
use Concrete\Tests\Core\Database\EntityManager\Provider\Fixtures\PackageControllerWithgetPackageEntityPath;
use Concrete\Tests\Core\Database\EntityManager\Provider\Fixtures\PackageControllerDefault;
use Concrete\Tests\Core\Database\EntityManager\Provider\Fixtures\PackageControllerLegacy;
use Concrete\Tests\Core\Database\EntityManager\Provider\Fixtures\PackageControllerDefaultWithAdditionalNamespaces;
use Illuminate\Filesystem\Filesystem;
use Concrete\Tests\Core\Database\Traits\DirectoryHelpers;

/**
 * PackageProviderFactoryTest
 *
 * @author Markus Liechti <markus@liechti.io>
 * @group orm_setup
 */
class DefaultPackageProviderTest extends \PHPUnit_Framework_TestCase
{

    use DirectoryHelpers;

    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    /**
     * Set up
     */
    public function setUp()
    {
        parent::setUp();
        $this->app = Application::getFacadeApplication();
        $this->filesystem = new Filesystem();
    }

    /**
     * Test packages with removed getPackageEntityPath() method
     *
     * @covers DefaultPackageProvider::getDrivers
     */
    public function testGetDriversWithGetPackageEntityPath()
    {
        $package = new PackageControllerWithgetPackageEntityPath($this->app);
        $dpp = new DefaultPackageProvider($this->app, $package);
        $drivers = $dpp->getDrivers();
        $this->assertInternalType('array', $drivers);
        $c5Driver = $drivers[0];
        $this->assertInstanceOf('Concrete\Core\Database\EntityManager\Driver\Driver', $c5Driver);
        $this->assertInstanceOf('Doctrine\ORM\Mapping\Driver\AnnotationDriver', $c5Driver->getDriver());
        $this->assertEquals($package->getNamespace() . '\Src', $c5Driver->getNamespace());
    }

    /**
     * Test package with default driver and not existing source directory
     *
     * @covers DefaultPackageProvider::getDrivers
     */
    public function testGetDriversWithNoExistingSrcDirectory()
    {
        $package = new PackageControllerDefault($this->app);
        $dpp = new DefaultPackageProvider($this->app, $package);
        $drivers = $dpp->getDrivers();
        $this->assertInternalType('array', $drivers);
        $this->assertEquals(0, count($drivers));
    }

    /**
     * Covers real word case of a package with $appVersionRequired < 8.0.0
     *
     * @covers DefaultPackageProvider::getDrivers
     */
    public function testGetDriversWithPackageWithLegacyNamespaceAndLegacyAnnotationReader()
    {
        $this->createPackageFolderOfTestMetadataDriverLegacy();

        $package = new PackageControllerLegacy($this->app);
        $dpp = new DefaultPackageProvider($this->app, $package);
        $drivers = $dpp->getDrivers();
        $this->assertInternalType('array', $drivers);
        $c5Driver = $drivers[0];
        $this->assertInstanceOf('Concrete\Core\Database\EntityManager\Driver\Driver', $c5Driver);
        $this->assertInstanceOf('Doctrine\ORM\Mapping\Driver\AnnotationDriver', $c5Driver->getDriver());
        $this->assertEquals($package->getNamespace() . '\Src', $c5Driver->getNamespace());

        $this->removePackageFolderOfTestMetadataDriverLegacy();
    }

    /**
     * Covers real word case of a package with $appVersionRequired >= 8.0.0
     *
     * @covers DefaultPackageProvider::getDrivers
     */
    public function testGetDriversWithPackageWithDefaultNamespaceAndDefaultAnnotationReader()
    {
        $this->createPackageFolderOfTestMetadatadriverDefault();

        $package = new PackageControllerDefault($this->app);
        $dpp = new DefaultPackageProvider($this->app, $package);
        $drivers = $dpp->getDrivers();
        $this->assertInternalType('array', $drivers);
        $c5Driver = $drivers[0];
        $this->assertInstanceOf('Concrete\Core\Database\EntityManager\Driver\Driver', $c5Driver);
        $this->assertInstanceOf('Doctrine\ORM\Mapping\Driver\AnnotationDriver', $c5Driver->getDriver());
        $this->assertEquals($package->getNamespace() . '\Entity', $c5Driver->getNamespace());

        $this->removePackageFolderOfTestMetadataDriverDefault();
    }

    /**
     * Covers package with additional namespaces and with $appVersionRewuired >= 8.0.0
     *
     * @covers DefaultPackageProvider::getDrivers
     */
    public function testGetDriversWithPackageWithAdditionalNamespaces()
    {
        $this->createPackageFolderOfTestMetadataDriverAdditionalNamespace();

        $package = new PackageControllerDefaultWithAdditionalNamespaces($this->app);
        $dpp = new DefaultPackageProvider($this->app, $package);
        $drivers = $dpp->getDrivers();

        $this->assertInternalType('array', $drivers);
        $this->assertEquals(3, count($drivers), 'Not all MappingDrivers have bin loaded');
        $c5Driver1 = $drivers[1];
        $driver1 = $c5Driver1->getDriver();
        $this->assertInstanceOf('Concrete\Core\Database\EntityManager\Driver\Driver', $c5Driver1);
        $this->assertInstanceOf('Doctrine\ORM\Mapping\Driver\AnnotationDriver', $driver1);
        $this->assertEquals('PortlandLabs\Concrete5\MigrationTool', $c5Driver1->getNamespace());

        $pathsOfDriver1 = $driver1->getPaths();
        $this->assertEquals('src/PortlandLabs/Concrete5/MigrationTool', $this->folderPathCleaner($pathsOfDriver1[0], 4));

        $this->removePackageFolderOfTestMetadataDriverAdditionalNamespace();
    }

    private function createPackageFolderOfTestMetadataDriverAdditionalNamespace()
    {
        $this->filesystem->makeDirectory(DIR_BASE . DIRECTORY_SEPARATOR .
                DIRNAME_PACKAGES . DIRECTORY_SEPARATOR .
                'test_metadatadriver_additional_namespace');
        $this->filesystem->makeDirectory(DIR_BASE . DIRECTORY_SEPARATOR .
                DIRNAME_PACKAGES . DIRECTORY_SEPARATOR .
                'test_metadatadriver_additional_namespace' . DIRECTORY_SEPARATOR .
                DIRNAME_CLASSES);
        $this->filesystem->makeDirectory(DIR_BASE . DIRECTORY_SEPARATOR .
                DIRNAME_PACKAGES . DIRECTORY_SEPARATOR .
                'test_metadatadriver_additional_namespace' . DIRECTORY_SEPARATOR .
                DIRNAME_CLASSES . DIRECTORY_SEPARATOR .
                'Concrete');
        $this->filesystem->makeDirectory(DIR_BASE . DIRECTORY_SEPARATOR .
                DIRNAME_PACKAGES . DIRECTORY_SEPARATOR .
                'test_metadatadriver_additional_namespace' . DIRECTORY_SEPARATOR .
                DIRNAME_CLASSES . DIRECTORY_SEPARATOR .
                'Concrete' . DIRECTORY_SEPARATOR . DIRNAME_ENTITIES);
    }

    private function removePackageFolderOfTestMetadataDriverAdditionalNamespace()
    {
        $this->filesystem->deleteDirectory(DIR_BASE . DIRECTORY_SEPARATOR .
                DIRNAME_PACKAGES . DIRECTORY_SEPARATOR .
                'test_metadatadriver_additional_namespace');
    }

    private function createPackageFolderOfTestMetadatadriverDefault()
    {
        $this->filesystem->makeDirectory(DIR_BASE . DIRECTORY_SEPARATOR .
                DIRNAME_PACKAGES . DIRECTORY_SEPARATOR .
                'test_metadatadriver_default');
        $this->filesystem->makeDirectory(DIR_BASE . DIRECTORY_SEPARATOR .
                DIRNAME_PACKAGES . DIRECTORY_SEPARATOR .
                'test_metadatadriver_default' . DIRECTORY_SEPARATOR .
                DIRNAME_CLASSES);
        $this->filesystem->makeDirectory(DIR_BASE . DIRECTORY_SEPARATOR .
                DIRNAME_PACKAGES . DIRECTORY_SEPARATOR .
                'test_metadatadriver_default' . DIRECTORY_SEPARATOR .
                DIRNAME_CLASSES . DIRECTORY_SEPARATOR .
                'Concrete');
        $this->filesystem->makeDirectory(DIR_BASE . DIRECTORY_SEPARATOR .
                DIRNAME_PACKAGES . DIRECTORY_SEPARATOR .
                'test_metadatadriver_default' . DIRECTORY_SEPARATOR .
                DIRNAME_CLASSES . DIRECTORY_SEPARATOR .
                'Concrete' . DIRECTORY_SEPARATOR . DIRNAME_ENTITIES);
    }

    private function removePackageFolderOfTestMetadataDriverDefault()
    {
        $packagePath = DIR_BASE . DIRECTORY_SEPARATOR .
                DIRNAME_PACKAGES . DIRECTORY_SEPARATOR .
                'test_metadatadriver_default';

        if ($this->filesystem->isDirectory($packagePath)) {
            $this->filesystem->deleteDirectory($packagePath);
        }
    }

    private function createPackageFolderOfTestMetadataDriverLegacy()
    {
        $this->filesystem->makeDirectory(DIR_BASE . DIRECTORY_SEPARATOR .
                DIRNAME_PACKAGES . DIRECTORY_SEPARATOR .
                'test_metadatadriver_legacy');
        $this->filesystem->makeDirectory(DIR_BASE . DIRECTORY_SEPARATOR .
                DIRNAME_PACKAGES . DIRECTORY_SEPARATOR .
                'test_metadatadriver_legacy' . DIRECTORY_SEPARATOR .
                DIRNAME_CLASSES);
    }

    private function removePackageFolderOfTestMetadataDriverLegacy()
    {
        $packagePath = DIR_BASE . DIRECTORY_SEPARATOR .
                DIRNAME_PACKAGES . DIRECTORY_SEPARATOR .
                'test_metadatadriver_legacy';

        if ($this->filesystem->isDirectory($packagePath)) {
            $this->filesystem->deleteDirectory($packagePath);
        }
    }

    /**
     * Clean up if a Exception is thrown
     */
    protected function onNotSuccessfulTest(\Exception $e)
    {
        $this->removePackageFolderOfTestMetadataDriverDefault();
        $this->removePackageFolderOfTestMetadataDriverDefault();
        $this->removePackageFolderOfTestMetadataDriverAdditionalNamespace();
    }

}
