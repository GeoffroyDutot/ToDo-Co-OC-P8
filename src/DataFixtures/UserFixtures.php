<?php


namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture{

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager) {
        $userAdmin = new User();
        $userAdmin->setUsername('Geoffroy');
        $userAdmin->setEmail('geoffroy.dutot@gmail.com');
        $password = $this->encoder->encodePassword($userAdmin, 'password');
        $userAdmin->setPassword($password);
        $userAdmin->setRoles(['ROLE_ADMIN']);

        $manager->persist($userAdmin);
        $this->addReference('admin', $userAdmin);

        $user = new User();
        $user->setUsername('user');
        $user->setEmail('user-contact@gmail.com');
        $password1 = $this->encoder->encodePassword($user, 'pass');
        $user->setPassword($password1);

        $manager->persist($user);
        $this->addReference('user', $user);

        $user1 = new User();
        $user1->setUsername('JamesT');
        $user1->setEmail('james.t@gmail.com');
        $password2 = $this->encoder->encodePassword($user1, 'password');
        $user1->setPassword($password2);

        $manager->persist($user1);
        $this->addReference('James', $user1);

        $manager->flush();
    }
}
