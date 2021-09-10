<?php


namespace App\DataFixtures;


use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TaskFixtures extends Fixture implements DependentFixtureInterface {

    public function load(ObjectManager $manager) {
        $task = new Task();
        $task->setTitle('Titre de la tâche');
        $task->setContent('Important');
        $task->setCreatedAt(new \DateTime('now'));
        $task->setAuthor($this->getReference('admin'));

        $manager->persist($task);

        $task1 = new Task();
        $task1->setTitle('Titre autre');
        $task1->setContent('À faire');
        $task1->setCreatedAt(new \DateTime('now'));
        $task1->setAuthor($this->getReference('user'));

        $manager->persist($task1);

        $task2 = new Task();
        $task2->setTitle('Titre de tâche');
        $task2->setContent('To do');
        $task2->setCreatedAt(new \DateTime('now'));
        $task2->toggle(true);
        $task2->setAuthor($this->getReference('user'));

        $manager->persist($task2);

        $manager->flush();
    }

    public function getDependencies() {
        return [
            UserFixtures::class
        ];
    }
}
