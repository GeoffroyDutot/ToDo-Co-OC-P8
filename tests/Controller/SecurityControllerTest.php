<?php


namespace App\Tests\Controller;


use App\Tests\Traits\LoginTest;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{
    use LoginTest;

    private KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * Test Login Page Form is http 200
     */
    public function testLoginPage()
    {
        $this->client->request('GET', '/login');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('button', 'Se connecter');
        $this->assertSelectorNotExists('.alert.alert-danger');
    }

    /**
     * Test User already Auth try to access to Login Page, Redirect home
     */
    public function testLoginPageUserAuth()
    {
        $user = $this->getUser($this->client);
        $this->login($this->client, $user);

        $this->client->request('GET', '/login');

        $this->assertResponseRedirects('/');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Bienvenue sur Todo List, l\'application vous permettant de gérer l\'ensemble de vos tâches sans effort !');
    }

    /**
     * Test Login Page Submit Bad data, Redirect to login and show error
     */
    public function testLoginBadCredentials()
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            'username' => 'Username',
            'password' => 'MyPassw0rd'
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    /**
     * Test Login Page Submit Valid data, Redirect to homepage
     */
    public function testSuccessLogin()
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            'username' => 'user',
            'password' => 'pass'
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects('/');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Bienvenue sur Todo List, l\'application vous permettant de gérer l\'ensemble de vos tâches sans effort !');
    }

    /**
     * Test Logout - Redirect login
     */
    public function testLogoutAuthUser()
    {
        $user = $this->getUser($this->client);
        $this->login($this->client, $user);

        $this->client->request('GET', '/logout');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('button', 'Se connecter');
    }

    /**
     * Test Logout with No Auth User - Redirect login
     */
    public function testLogoutAnonymousUser()
    {
        $this->client->request('GET', '/logout');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('button', 'Se connecter');
    }
}

