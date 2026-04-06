<?php declare(strict_types=1);

namespace Zeobv\AbandonedCart\Test\Controller;

use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\HttpKernel\KernelInterface;

class RecoverControllerTest extends TestCase
{
    use KernelTestBehaviour;

    public function testRouteNotFoundWithoutId(): void
    {
        $client = static::createClient($this->getKernel());

        $client->request('GET', '/zeo/abandonedcart/recover');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testRedirectToHomeIfInvalidId(): void
    {
        $client = static::createClient($this->getKernel());

        $client->request('GET', '/zeo/abandonedcart/recover/myinvalidid');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    /**
     * Creates a KernelBrowser.
     *
     * @param KernelInterface $kernel
     * @param array           $options An array of options to pass to the createKernel method
     * @param array           $server  An array of server parameters
     *
     * @return KernelBrowser A KernelBrowser instance
     */
    protected static function createClient(KernelInterface $kernel, array $options = [], array $server = [])
    {
        try {
            $client = $kernel->getContainer()->get('test.client');
        } catch (ServiceNotFoundException $e) {
            if (class_exists(KernelBrowser::class)) {
                throw new \LogicException('You cannot create the client used in functional tests if the "framework.test" config is not set to true.');
            }
            throw new \LogicException('You cannot create the client used in functional tests if the BrowserKit component is not available. Try running "composer require symfony/browser-kit"');
        }

        $client->setServerParameters($server);

        return self::getClient($client);
    }

    private static function getClient(AbstractBrowser $newClient = null): ?AbstractBrowser
    {
        static $client;

        if (0 < \func_num_args()) {
            return $client = $newClient;
        }

        if (!$client instanceof AbstractBrowser) {
            static::fail(sprintf('A client must be set to make assertions on it. Did you forget to call "%s::createClient()"?', __CLASS__));
        }

        return $client;
    }
}
