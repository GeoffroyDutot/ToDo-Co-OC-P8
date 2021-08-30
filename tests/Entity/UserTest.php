<?php


namespace App\Tests\Entity;


use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

class UserTest extends KernelTestCase {

    /**
     * Get User
     */
    public function getUser() {
        $user = new User();
        $user->setUsername('Username');
        $user->setEmail('username@gmail.com');
        $user->setPassword('P@ssw0rd2021');
        $user->addTask($this->getTask());

        return $user;
    }

    /**
     * Get Task
     */
    public function getTask(): Task {
        $task = new Task();
        $task->setTitle('Title');
        $task->setContent('TO DO');

        return $task;
    }

    /**
     * Validate User Data
     */
    public function validateUserData(User $user, int $number = 0) {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($user);

        $errorsMessages = [];
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $errorsMessages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }

        $this->assertCount($number, $errors, implode(',', $errorsMessages));
    }

    /**
     * Test valid user
     */
    public function testValidateUser() {
        $user = $this->getUser();

        $this->assertEquals('Username', $user->getUsername());
        $this->assertEquals('username@gmail.com', $user->getEmail());
        $this->assertEquals('P@ssw0rd2021', $user->getPassword());
        $this->assertEquals('ROLE_USER', $user->getRoles()[0]);

        $this->validateUserData($user, 0);
    }

    /**
     * Test add user role admin
     */
    public function testValidUserAdmin() {
        $user = $this->getUser();

        $user->addRoles('ROLE_ADMIN');

        $this->assertEquals('ROLE_ADMIN', $user->getRoles()[0]);

        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);

        $this->assertEquals(['ROLE_USER', 'ROLE_ADMIN'], $user->getRoles());

        $this->validateUserData($user, 0);
    }

    /**
     * Test add user tasks
     */
    public function testValidUserAddTasks() {
        $user = $this->getUser();
        $task = $this->getTask();

        $user->addTask($task);
        $this->assertNotEmpty($user->getTasks());

        $user->removeTask($user->getTasks()[0]);
        $user->removeTask($task);
        $this->assertEmpty($user->getTasks());

        $this->validateUserData($user, 0);
    }

    /**
     * Test Invalid User Properties Type
     */
    public function testInvalidUserPropertiesType() {
        $user = $this->getUser();

        // Check Invalid Username Type
        $user->setUsername(12);

        // Check Invalid Email Type
        $user->setEmail(false);

        // Check Invalid Password Type
        $user->setPassword(21);

        $this->validateUserData($user, 3);
    }

    /**
     * Test Invalid Null|Blank User Properties Type
     */
    public function testInvalidEmptyUserPropertiesType() {
        $user = $this->getUser();

        // Check Invalid Username Type
        $user->setUsername(null);

        // Check Invalid Email Type
        $user->setEmail(null);

        // Check Invalid Password Type
        $user->setPassword(null);

        // Validate Not Null Constraint
        $this->validateUserData($user, 3);

        // Check Invalid Username Type
        $user->setUsername('');

        // Check Invalid Email Type
        $user->setEmail('');

        // Check Invalid Password Type
        $user->setPassword('');

        // Validate Not Blank Constraint
        $this->validateUserData($user, 4);
    }
}
