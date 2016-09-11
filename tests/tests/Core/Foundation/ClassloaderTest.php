<?php
namespace Concrete\Tests\Core\Foundation;

use Concrete\Core\Foundation\ClassLoader;
use Illuminate\Filesystem\Filesystem;

class ClassloaderTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        parent::setUp();
        $coreAutoloader = ClassLoader::getInstance();
        $coreAutoloader->disable();
    }

    public function tearDown()
    {
        parent::tearDown();
        $coreAutoloader = ClassLoader::getInstance();
        $coreAutoloader->enable();
    }

    public function aliasesClassesDataProvider()
    {
        return [
            ['\Controller', '\Concrete\Core\Controller\Controller'],
            ['\Page', '\Concrete\Core\Page\Page'],
            ['\File', '\Concrete\Core\File\File'],
        ];
    }

    /**
     * @dataProvider aliasesClassesDataProvider
     */
    public function testClassAliases($a, $b)
    {
        // We still need to enable class loaders here otherwise the aliases won't work

        $loader = ClassLoader::getInstance();
        $loader->enable();

        $class1 = new $a();
        $class2 = new $b();
        $this->assertEquals($class1, $class2);
    }

    public function applicationClassesDataProvider()
    {
        return [
            // Overrides, Modified autoloader

            ['page_theme.php', 'themes/fancy/', 'Application\Theme\Fancy\PageTheme'],
            ['autonav/controller.php', 'blocks/autonav/', 'Application\Block\Autonav\Controller'],
            ['core_area_layout/controller.php', 'blocks/core_area_layout/', 'Application\Block\CoreAreaLayout\Controller'],
            ['design.php', 'controllers/panel/page/', 'Application\Controller\Panel\Page\Design'],

            // Overrides, Strict autoloader
            ['RecaptchaController.php', 'src/Concrete/Captcha/', 'Application\Concrete\Captcha\RecaptchaController'],

            // Entity Classes
            ['TestEntity.php', 'src/Entity/', 'Application\Entity\TestEntity'],

            // Test that application/src/Whatever does NOT work
            ['TestClass.php', 'src/Testing/', 'Application\Src\Testing\TestClass', false],
            ['TestClass.php', 'src/Testing/', 'Application\Testing\TestClass', false],

        ];
    }

    public function applicationClassesLegacyDataProvider()
    {
        return [
            ['TestClass.php', 'src/Testing/', 'Application\Src\Testing\TestClass'],
        ];
    }

    public function applicationClassesLegacyCustomNamespaceDataProvider()
    {
        return [
            ['Foobar', 'TestCustomNamespaceClass.php', 'src/Testing/', 'Foobar\Src\Testing\TestCustomNamespaceClass'],
        ];
    }


    public function coreClassesDataProvider()
    {
        return [
            // Strict autoloader
            ['Concrete\Core\Area\Area'],
            ['Concrete\Core\User\User'],
            ['Concrete\Core\Application\EditResponse'],
            ['Concrete\Core\Entity\File\File'],
            ['Concrete\Core\View\DialogView'],

            // Modified autoloader
            ['Concrete\Attribute\Address\Controller'],
            ['Concrete\Attribute\SocialLinks\Controller'],
            ['Concrete\Controller\Backend\Block\Action'],
            ['Concrete\Controller\Dialog\Conversation\Subscribe'],
            ['Concrete\Controller\Frontend\Jobs'],
            ['Concrete\Controller\PageType\CoreStack'],
            ['Concrete\Controller\Panel\Add'],
            ['Concrete\Controller\Search\FileFolder'],
            ['Concrete\Controller\SinglePage\Account\Avatar'],
            ['Concrete\Controller\SinglePage\Dashboard\System\Basics\Name'],
            ['Concrete\Authentication\Concrete\Controller'],
            ['Concrete\Authentication\Facebook\Controller'],
            ['Concrete\Block\CoreAreaLayout\Controller'],
            ['Concrete\Block\ExpressForm\Controller'],
            ['Concrete\Block\SocialLinks\Controller'],
            ['Concrete\Job\IndexSearch'],
            ['Concrete\Job\ProcessEmail'],
            ['Concrete\Job\RemoveOldPageVersions'],
            ['Concrete\Theme\Elemental\PageTheme'],
            ['Concrete\Theme\Dashboard\PageTheme'],

            // Legacy autoloader
            ['Concrete\Core\Legacy\TaskPermission'],
        ];
    }

    /**
     * @dataProvider coreClassesDataProvider
     */
    public function testDisableClassLoader($class)
    {
        // Tests the initial state of the autoloader - basically we're testing whether
        // classloader->disable() works, because they are all enabled
        // as part of bootstrap, then disabled as part of setUp of this test.
        $this->assertFalse(class_exists($class), sprintf('Class %s loaded', $class));
    }

    protected function classExists($class)
    {
        $loader = ClassLoader::getInstance();
        $loader->enable();
        return class_exists($class);
    }

    /**
     * @dataProvider coreClassesDataProvider
     */
    public function testCoreClassExists($class)
    {
        $this->assertTrue($this->classExists($class), sprintf('Class %s failed to load', $class));
    }

    protected function putFileIntoPlace($file, $destination)
    {
        $destination = trim($destination, '/');
        $sourceFile =  dirname(__FILE__) . DIRECTORY_SEPARATOR . 'fixtures' .  DIRECTORY_SEPARATOR . $file;
        $destinationDirectory = DIR_APPLICATION . DIRECTORY_SEPARATOR . $destination;
        $filesystem = new Filesystem();
        if (!$filesystem->isDirectory($destinationDirectory)) {
            $filesystem->makeDirectory($destinationDirectory, 0755, true);
        }
        $filesystem->copy(
            $sourceFile,
            $destinationDirectory . DIRECTORY_SEPARATOR . basename($file)
        );
    }

    protected function cleanUpFile($file, $destination)
    {
        $destination = trim($destination, '/');
        $filesystem = new Filesystem();
        $destinationDirectory = DIR_APPLICATION . DIRECTORY_SEPARATOR . $destination;
        $filesystem->delete(
            $destinationDirectory . DIRECTORY_SEPARATOR . $file
        );
        // Remove the directories we made.
        $filesystem->deleteDirectory($destinationDirectory);
    }

    /**
     * @dataProvider applicationClassesDataProvider
     */
    public function testApplicationCoreOverrideAutoloader($file, $destination, $class, $exists = true)
    {
        $this->putFileIntoPlace($file, $destination);
        $classExists = $this->classExists($class);
        $this->cleanUpFile($file, $destination);
        if ($exists) {
            $this->assertTrue($classExists, sprintf('Class %s failed to load', $class));
        } else {
            $this->assertFalse($classExists, sprintf('Class %s loaded', $class));
        }
    }

    /**
     * @dataProvider applicationClassesLegacyDataProvider
     */
    public function testApplicationLegacyAutoloader($file, $destination, $class, $exists = true)
    {
        $this->putFileIntoPlace($file, $destination);
        $loader = ClassLoader::getInstance();
        $loader->enableLegacyNamespace();
        $loader->enable();
        $classExists = $this->classExists($class);
        $this->cleanUpFile($file, $destination);
        if ($exists) {
            $this->assertTrue($classExists, sprintf('Class %s failed to load', $class));
        } else {
            $this->assertFalse($classExists, sprintf('Class %s loaded', $class));
        }
    }

    /**
     * @dataProvider applicationClassesLegacyCustomNamespaceDataProvider
     */
    public function testApplicationLegacyCustomNamespaceAutoloader($namespace, $file, $destination, $class)
    {
        $this->putFileIntoPlace($file, $destination);
        $loader = ClassLoader::getInstance();
        $loader->enableLegacyNamespace();
        $loader->setApplicationNamespace($namespace);
        $loader->enable();
        $classExists = $this->classExists($class);
        $this->cleanUpFile($file, $destination);
        $this->assertTrue($classExists, sprintf('Class %s failed to load', $class));
    }



}
