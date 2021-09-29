<?php


namespace App\Tests\Controller;


use App\Tests\Traits\LoginTest;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends WebTestCase
{
    use LoginTest;

    private KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * Test User No Auth try to access to homepage and redirected to login
     */
    public function testHomepageAnonymousUser()
    {
        $this->client->request('GET', '/');

        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('button', 'Se connecter');
        $this->assertSelectorExists('.alert.alert-danger');
    }

    /**
     * Test Auth User try to access to homepage
     */
    public function testHomepageAuthUser()
    {
        $user = $this->getUser($this->client);
        $this->login($this->client, $user);

        $this->client->request('GET', '/');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', 'Bienvenue sur Todo List, l\'application vous permettant de gérer l\'ensemble de vos tâches sans effort !');
    }
}
