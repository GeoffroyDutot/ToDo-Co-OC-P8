<?php


namespace App\Tests\Controller;


use App\Tests\Traits\LoginTest;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{
    use LoginTest;

    private KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * Test User Auth try to access to tasks to do list
     */
    public function testTasksToDoAuthUser()
    {
        $user = $this->getUser($this->client);
        $this->login($this->client, $user);

        $this->client->request('GET', '/tasks');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * Test User Not Auth try to access to tasks to do list - Redirect login and flash error
     */
    public function testTasksToDoAnonymousUser()
    {
        $this->client->request('GET', '/tasks');

        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('button', 'Se connecter');
        $this->assertSelectorExists('.alert.alert-danger');
    }
}
