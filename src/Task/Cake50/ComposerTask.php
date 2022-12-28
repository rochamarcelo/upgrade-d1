<?php

namespace Cake\Upgrade\Task\Cake50;

use Cake\Upgrade\Task\RepoTaskInterface;
use Cake\Upgrade\Task\Task;

/**
 * Adjusts:
 * - composer version (if x.y of `>=x.y` is less than 8.1)
 * - Cake version
 * - TODO: related plugin versions?
 */
class ComposerTask extends Task implements RepoTaskInterface {

	/**
	 * @var string
	 */
	protected const CHAR_PHP = '>=';

	/**
	 * @var string
	 */
	protected const CHAR_CAKEPHP = '^';

	/**
	 * @var string
	 */
	protected const TARGET_VERSION_PHP = '8.1';

	/**
	 * @var string
	 */
	protected const TARGET_VERSION_CAKEPHP = '5.0.0@beta';

	/**
	 * @param string $path
	 *
	 * @return void
	 */
	public function run(string $path): void {
		$filePath = $path . 'composer.json';
		$content = (string)file_get_contents($filePath);

		$callable = function ($matches) {
			$version = version_compare($matches[1], static::TARGET_VERSION_PHP . '.0', '<') ? static::TARGET_VERSION_PHP : $matches[1];

			return '"php": "' . static::CHAR_PHP . $version . '"';
		};
		$newContent = (string)preg_replace_callback('#"php": "' . preg_quote(static::CHAR_PHP, '#') . '(.+)"#', $callable, $content);

		$callable = function ($matches) {
			$version = version_compare($matches[1], static::TARGET_VERSION_CAKEPHP, '<') ? static::TARGET_VERSION_CAKEPHP : $matches[1];

			return '"cakephp/cakephp": "' . static::CHAR_CAKEPHP . $version . '"';
		};
		$newContent = (string)preg_replace_callback('#"cakephp/cakephp": "' . preg_quote(static::CHAR_CAKEPHP, '#') . '(.+)"#', $callable, $newContent);

		$this->persistFile($filePath, $content, $newContent);
	}

}
