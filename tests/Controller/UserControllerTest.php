<?php


namespace App\Tests\Controller;


use App\Tests\Traits\LoginTest;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase {
    use LoginTest;

    private KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * Test User Auth try to access to users list
     */
    public function testUsersListAuthUserAdmin()
    {
        $user = $this->getAdminUser($this->client);
        $this->login($this->client, $user);

        $this->client->request('GET', '/users');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', 'Liste des utilisateurs');
    }

    /**
     * Test User ROLE_USER try to access to users list
     */
    public function testUsersListAuthUser()
    {
        $user = $this->getUser($this->client);
        $this->login($this->client, $user);

        $this->client->request('GET', '/users');

        $this->assertResponseRedirects('/');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    /**
     * Test anonymous user try to access to users list
     */
    public function testUserListAnonymousUser()
    {
        $this->client->request('GET', '/users');

        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('button', 'Se connecter');
        $this->assertSelectorExists('.alert.alert-danger');
    }

    /**
     * Test access to Create User page with No Auth User - Redirect login & Flash error
     */
    public function testCreateUserAnonymousUser()
    {
        $this->client->request('GET', '/users/create');

        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('button', 'Se connecter');
        $this->assertSelectorExists('.alert.alert-danger');
    }

    /**
     * Test access to Create User page with Auth User - Redirect login & Flash error
     */
    public function testCreateUserAuthUser()
    {
        $user = $this->getUser($this->client);
        $this->login($this->client, $user);

        $this->client->request('GET', '/users/create');

        $this->assertResponseRedirects('/');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    /**
     * Test access to Create User page with Auth User Admin
     */
    public function testCreateUserAuthAdminUser()
    {
        $user = $this->getAdminUser($this->client);
        $this->login($this->client, $user);

        $this->client->request('GET', '/users/create');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * Test Create User with correct data - Redirect users list & Flash success
     */
    public function testCreateUserCorrectData()
    {
        $user = $this->getAdminUser($this->client);
        $this->login($this->client, $user);

        $crawler = $this->client->request('GET', '/users/create');
        $form = $crawler->selectButton('Ajouter')->form([
            'user[username]' => 'James',
            'user[password][first]' => 'Passsswooord',
            'user[password][second]' => 'Passsswooord',
            'user[email]' => 'james-contact@gmail.com',
            'user[Roles]' => 'ROLE_USER'
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects('/users');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }

    /**
     * Test Create User with incorrect data - Show error
     */
    public function testCreateUserIncorrectData()
    {
        $user = $this->getAdminUser($this->client);
        $this->login($this->client, $user);

        $crawler = $this->client->request('GET', '/users/create');
        $form = $crawler->selectButton('Ajouter')->form([
            'user[username]' => 'Thomas',
            'user[password][first]' => 'Passsswooord',
            'user[password][second]' => 'diffeerent',
            'user[email]' => 'thomas-contact@gmail.com',
            'user[Roles]' => 'ROLE_USER'
        ]);
        $this->client->submit($form);

        $this->assertSelectorExists('.help-block');
    }

    /**
     * Test access to Edit User page with No Auth User - Redirect login & Flash error
     */
    public function testEditUserAnonymousUser()
    {
        $this->client->request('GET', '/users/3/edit');

        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('button', 'Se connecter');
        $this->assertSelectorExists('.alert.alert-danger');
    }

    /**
     * Test access to Edit User page with Auth User - Redirect login & Flash error
     */
    public function testEditUserAuthUser()
    {
        $user = $this->getUser($this->client);
        $this->login($this->client, $user);

        $this->client->request('GET', '/users/3/edit');

        $this->assertResponseRedirects('/');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    /**
     * Test access to Edit User page with Auth User Admin
     */
    public function testEditUserAuthAdminUser()
    {
        $user = $this->getAdminUser($this->client);
        $this->login($this->client, $user);

        $this->client->request('GET', '/users/3/edit');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * Test Edit User with correct data - Redirect users list & Flash success
     */
    public function testEditUserCorrectData()
    {
        $user = $this->getAdminUser($this->client);
        $this->login($this->client, $user);

        $crawler = $this->client->request('GET', '/users/3/edit');
        $form = $crawler->selectButton('Modifier')->form([
            'user[username]' => 'James44',
            'user[password][first]' => 'Passsswooord',
            'user[password][second]' => 'Passsswooord',
            'user[email]' => 'james44-contact@gmail.com',
            'user[Roles]' => 'ROLE_USER'
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects('/users');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }

    /**
     * Test Edit User with incorrect data - Show error
     */
    public function testEditUserIncorrectData()
    {
        $user = $this->getAdminUser($this->client);
        $this->login($this->client, $user);

        $crawler = $this->client->request('GET', '/users/3/edit');
        $form = $crawler->selectButton('Modifier')->form([
            'user[username]' => 'James44',
            'user[password][first]' => 'Passsswooord',
            'user[password][second]' => 'badpass',
            'user[email]' => 'james44-contact@gmail.com',
            'user[Roles]' => 'ROLE_USER'
        ]);
        $this->client->submit($form);

        $this->assertSelectorExists('.help-block');
    }
}
