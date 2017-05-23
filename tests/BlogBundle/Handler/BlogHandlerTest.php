<?php

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class BlogHandlerTest extends TestCase
{
    public function testCalculateStatus()
    {
        $repositoryMock = $this->createMock(\BlogBundle\Repository\BlogRepository::class);
        $blogHandler = new \BlogBundle\Handler\BlogHandler($repositoryMock);

        $blog = new \BlogBundle\Entity\Blog();

        // CHECK OPEN
        $today = new  \DateTime();
        $blog->setDate($today->modify('-10 days'));
        $blog = $this->invokeMethod($blogHandler, 'calculateStatus', array($blog));
        $this->assertEquals($blog->getStatus(), 'lapsed');
        // CHECK LAPSED
        $today = new  \DateTime();
        $blog->setDate($today->modify('+10 days'));
        $blog = $this->invokeMethod($blogHandler, 'calculateStatus', array($blog));
        $this->assertEquals($blog->getStatus(), 'open');
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

}