<?php

namespace Cake\Upgrade\Task\Cake50;

use Cake\Upgrade\Task\FileTaskInterface;
use Cake\Upgrade\Task\Task;

/**
 * Adjusts:
 * - protected $_defaultConfig => protected array $_defaultConfig
 */
class ModelHookTask extends Task implements FileTaskInterface
{
    /**
     * @param string $path
     *
     * @return array<string>
     */
    public function getFiles(string $path): array
    {
        return $this->collectFiles($path, 'php', ['src/Model/', 'tests/TestCase/Model/']);
    }

    /**
     * @param string $path
     *
     * @return void
     */
    public function run(string $path): void
    {
        $content = (string)file_get_contents($path);
        $newContent = preg_replace('#\bpublic function beforeFind\(EventInterface \$event, Query \$#', 'public function beforeFind(EventInterface $event, \Cake\ORM\Query\SelectQuery $', $content);

        $newContent = preg_replace('#\bpublic function find(\w+)\(Query \$query, array \$options\)#', 'public function find\1(\Cake\ORM\Query\SelectQuery $query, array $options)', $newContent);

        $newContent = preg_replace('#\bprotected function _initializeSchema\(TableSchemaInterface \$schema\)#', 'public function getSchema()', $newContent);

        $this->persistFile($path, $content, $newContent);
    }
}
