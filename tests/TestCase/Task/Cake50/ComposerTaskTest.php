<?php

namespace Cake\Upgrade\Test\TestCase\Task\Cake50;

use Cake\TestSuite\TestCase;
use Cake\Upgrade\Task\Cake50\ComposerTask;

class ComposerTaskTest extends TestCase
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

        $task = new ComposerTask(['path' => $path]);
        $task->run($path);

        $changes = $task->getChanges();
        $this->assertCount(1, $changes);

        $changesString = (string)$changes;
        $expected = <<<'TXT'
composer.json
-        "php": ">=7.4",
-        "cakephp/cakephp": "^4.2.0",
+        "php": ">=8.1",
+        "cakephp/cakephp": "5.x-dev",
-        "dereuromark/cakephp-shim": "^2.3.0",
-        "phpunit/phpunit": "^9.5",
+        "dereuromark/cakephp-shim": "dev-cake5",
+        "phpunit/phpunit": "^9.5.16",
-    }
+    },
+    "minimum-stability": "dev"

TXT;
        $this->assertTextEquals($expected, $changesString);
    }
}
