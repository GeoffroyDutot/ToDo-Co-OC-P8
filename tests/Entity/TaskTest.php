<?php


namespace App\Tests\Entity;


use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

class TaskTest extends KernelTestCase {

    /**
     * Get Author
     */
    public function getAuthor(): User {
        return new User();
    }

    /**
     * Get Task
     */
    public function getTask(\DateTime $dateTimeNow): Task {
        $task = new Task();
        $task->setTitle('Title');
        $task->setContent('TO DO');
        $task->setCreatedAt($dateTimeNow);
        $task->setAuthor($this->getAuthor());

        return $task;
    }

    /**
     * Validate Task Data
     */
    public function validateTask(Task $task, int $number = 0) {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($task);

        $errorsMessages = [];
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $errorsMessages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }

        $this->assertCount($number, $errors, implode(',', $errorsMessages));
    }


    /**
     * Test create a task
     */
    public function testValidTask() {
        $dateTimeNow = new \DateTime();

        $task = $this->getTask($dateTimeNow);

        $this->assertEquals('Title', $task->getTitle());
        $this->assertEquals('TO DO', $task->getContent());
        $this->assertEquals(false, $task->isDone());
        $this->assertEquals($dateTimeNow, $task->getCreatedAt());
        $this->assertEquals($this->getAuthor(), $task->getAuthor());

        $this->validateTask($task, 0);
    }

    /**
     * Test toggle task to done
     */
    public function testToggleTaskDone() {
        $dateTimeNow = new \DateTime();

        $task = $this->getTask($dateTimeNow);
        $task->toggle(true);

        $this->assertEquals(true, $task->isDone());

        $this->validateTask($task, 0);
    }

    /**
     * Test Invalid Task Properties Type
     */
    public function testInvalidTaskPropertiesType() {
        $dateTimeNow = new \DateTime();

        $task = $this->getTask($dateTimeNow);

        // Check Invalid Title Type
        $task->setTitle(12);

        // Check Invalid Content Type
        $task->setContent(2);

        // Check Invalid IsDone Type
        $task->toggle('string');

        // Check Invalid CreatedDate Type
        $task->setCreatedAt('2022');

        $this->validateTask($task, 4);
    }

    /**
     * Test Invalid Null|Blank Task Properties Type
     */
    public function testInvalidEmptyTaskPropertiesType() {
        $dateTimeNow = new \DateTime();

        $task = $this->getTask($dateTimeNow);

        // Check Invalid Title Type
        $task->setTitle(null);

        // Check Invalid Content Type
        $task->setContent(null);

        // Validate Not Null Constraint
        $this->validateTask($task, 2);

        // Check Invalid Title Type
        $task->setTitle('');

        // Check Invalid Content Type
        $task->setContent('');

        // Validate Not Blank Constraint
        $this->validateTask($task, 2);
    }
}
