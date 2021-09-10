<?php


namespace App\Tests\Traits;


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

trait LoginTest {

    /**
     * Create user session
     */
    public function login(KernelBrowser $client, User $user) {
        $session = $client->getContainer()->get('session');
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $session->set('_security_main', serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);
    }

    /**
     * Get a classic user
     */
    public function getUser(KernelBrowser $client) {
        return $client->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy(['roles' => ['']]);
    }

    /**
     * Get an admin user
     */
    public function getAdminUser(KernelBrowser $client) {
        return $client->getContainer()->get('doctrine')->getRepository(User::class)->findOneByUsername('Geoffroy');
    }
}
