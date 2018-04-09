<?php
/**
 * @license    https://opensource.org/licenses/BSD-3-Clause New BSD License
 * @Copyright  (c) 2017-2018, jailgreen <36865973+jailgreen@users.noreply.github.com>
 */

declare(strict_types=1);

namespace AppTest;

use App\ConfigProvider;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * Description of ConfigProviderTest
 *
 * @author jailgreen <36865973+jailgreen@users.noreply.github.com>
 */
class ConfigProviderTest extends TestCase
{
    /**
     * @var ConfigProvider
     */
    private $provider;

    protected function setUp() : void
    {
        $this->provider = new ConfigProvider();
    }

    public function testInvocationReturnsArray() : array
    {
        $config = ($this->provider)();
        $this->assertInternalType('array', $config);

        return $config;
    }

    /**
     * @depends testInvocationReturnsArray
     * @param array $config
     * @return void
     */
    public function testReturnedArrayContainsDependencies(array $config) : void
    {
        $this->assertArrayHasKey('dependencies', $config);
        $this->assertArrayHasKey('templates', $config);
        $this->assertInternalType('array', $config['dependencies']);
        $this->assertInternalType('array', $config['templates']);
    }

    public function testContainerContainsTemplates() : void
    {
        $container = $this->getContainer();
        $config    = $container->get('config');

        $this->assertArrayHasKey('templates', $config);
        $this->assertArrayHasKey('paths', $config['templates']);
    }

    private function getContainer() : ContainerInterface
    {
        $container = require  __DIR__ . '/../config/container.php';
        return $container;
    }
}
