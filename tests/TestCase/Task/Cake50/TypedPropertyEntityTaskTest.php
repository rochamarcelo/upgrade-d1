<?php

namespace Cake\Upgrade\Test\TestCase\Task\Cake50;

use Cake\TestSuite\TestCase;
use Cake\Upgrade\Task\Cake50\TypedPropertyEntityTask;

class TypedPropertyEntityTaskTest extends TestCase
{
 /**
  * Basic test to simulate running on this repo
  *
  * Should return all files in the src directory of this repo
  *
  * @return void
  */
    public function testRun()
    {
        $path = TESTS . 'test_files' . DS . 'Task' . DS . 'Cake50' . DS;
        $filePath = $path . 'src' . DS . 'Model' . DS . 'Entity' . DS . 'SomeEntity.php';

        $task = new TypedPropertyEntityTask(['path' => $path]);
        $task->run($filePath);

        $changes = $task->getChanges();
        $this->assertCount(1, $changes);

        $changesString = (string)$changes;
        $expected = <<<'TXT'
src/Model/Entity/SomeEntity.php
-    protected $_accessible = [
+    protected array $_accessible = [

TXT;
        $this->assertTextEquals($expected, $changesString);
    }
}
