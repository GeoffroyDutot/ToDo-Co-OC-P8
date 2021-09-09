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

    /**
     * Test access to Create Task page with Auth User
     */
    public function testCreateTaskAuthUser()
    {
        $user = $this->getUser($this->client);
        $this->login($this->client, $user);

        $this->client->request('GET', '/tasks/create');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * Test access to Create Task page with No Auth User - Redirect login & Flash error
     */
    public function testCreateTaskAnonymousUser()
    {
        $this->client->request('GET', '/tasks/create');

        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('button', 'Se connecter');
        $this->assertSelectorExists('.alert.alert-danger');
    }

    /**
     * Test Create Task with correct data - Redirect tasks to do list & Flash success
     */
    public function testCreateTaskCorrectData()
    {
        $user = $this->getUser($this->client);
        $this->login($this->client, $user);

        $crawler = $this->client->request('GET', '/tasks/create');
        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'Titre de la tâche',
            'task[content]' => 'Contenu de la tâche'
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects('/tasks');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }

    /**
     * Test Create Task with bad data - Redirect to create task & Show error(s)
     */
    public function testCreateTaskBadData()
    {
        $user = $this->getUser($this->client);
        $this->login($this->client, $user);

        $crawler = $this->client->request('GET', '/tasks/create');
        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'Titre de la tâche',
            'task[content]' => null
        ]);
        $this->client->submit($form);

        $this->assertSelectorExists('.help-block');
    }

    /**
     * Test toggle task with anonymous user
     */
    public function testToggleTaskAnonymousUser() {
        $this->client->request('GET', '/tasks/1/toggle');

        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('button', 'Se connecter');
        $this->assertSelectorExists('.alert.alert-danger');
    }

    /**
     * Test toggle task to done -
     */
    public function testToggleTaskDone()
    {
        $user = $this->getUser($this->client);
        $this->login($this->client, $user);

        $this->client->request('GET', '/tasks/1/toggle');

        $this->assertResponseRedirects('/tasks');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }

    /**
     * Test toggle task to undone -
     */
    public function testToggleTaskUndone()
    {
        $user = $this->getUser($this->client);
        $this->login($this->client, $user);

        $this->client->request('GET', '/tasks/1/toggle');

        $this->assertResponseRedirects('/tasks');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }


    //@TODO Check to do tasks page -> to do tasks
    //@TODO Check tasks done page -> finished tasks
    // And change toggle task to -> page from

    /**
     * Test edit task with anonymous user
     */
    public function testEditTaskAnonymousUser() {
        $this->client->request('GET', '/tasks/1/edit');

        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('button', 'Se connecter');
        $this->assertSelectorExists('.alert.alert-danger');
    }

    /**
     * Test edit task with correct data - edit & redirect to list & show success
     */
    public function testEditTaskCorrectData() {
        $user = $this->getUser($this->client);
        $this->login($this->client, $user);

        $crawler = $this->client->request('GET', '/tasks/1/edit');
        $form = $crawler->selectButton('Modifier')->form([
            'task[title]' => 'Nouveau titre de la tâche',
            'task[content]' => 'Contenu de la tâche'
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects('/tasks');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }


    /**
     * Test edit task with correct data - edit & show error
     */
    public function testEditTaskBadData() {
        $user = $this->getUser($this->client);
        $this->login($this->client, $user);

        $crawler = $this->client->request('GET', '/tasks/1/edit');
        $form = $crawler->selectButton('Modifier')->form([
            'task[title]' => 'Le titre de la tâche',
            'task[content]' => null
        ]);
        $this->client->submit($form);

        $this->assertSelectorExists('.help-block');
    }

    /**
     * Test delete task with anonymous user
     */
    public function testDeleteTaskAnonymousUser() {
        $this->client->request('GET', '/tasks/1/delete');

        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('button', 'Se connecter');
        $this->assertSelectorExists('.alert.alert-danger');
    }

    /**
     * Test delete task - Remove & redirect to list & show success
     */
    public function testDeleteTask() {
        $user = $this->getUser($this->client);
        $this->login($this->client, $user);

        $this->client->request('GET', '/tasks/1/delete');

        $this->assertResponseRedirects('/tasks');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }
}