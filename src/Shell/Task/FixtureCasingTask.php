<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         3.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\Upgrade\Shell\Task;

use Cake\Utility\Inflector;

/**
 * Updates test cases for 3.7+
 *
 * @property \Cake\Upgrade\Shell\Task\StageTask $Stage
 */
class FixtureCasingTask extends BaseTask {

	use ChangeTrait;

	/**
	 * @var array
	 */
	public $tasks = ['Stage'];

	/**
	 * Process tests regarding fixture usage and update it for 3.x
	 *
	 * @param string $path Path
	 * @return bool
	 */
	protected function _process($path) {
		$original = $contents = $this->Stage->source($path);

		$contents = $this->_replaceFixtureLoading($contents);

		return $this->Stage->change($path, $original, $contents);
	}

	/**
	 * @param string $contents
	 *
	 * @return string
	 */
	protected function _replaceFixtureLoading($contents) {
		// Serializes data from PHP data into PHP code.
		// Basically a code style conformant version of var_export()
		$export = function ($values) use (&$export) {
			$vals = [];
			if (!is_array($values)) {
				return $vals;
			}
			foreach ($values as $key => $val) {
				if (is_array($val)) {
					$vals[] = "'{$key}' => [" . implode(', ', $export($val)) . ']';
				} else {
					$val = var_export($val, true);
					if ($val === 'NULL') {
						$val = 'null';
					}
					if (!is_numeric($key)) {
						$vals[] = "'{$key}' => {$val}";
					} else {
						$vals[] = "{$val}";
					}
				}
			}
			return $vals;
		};

		// Process field property.
		$processor = function ($matches) use ($export) {
			//@codingStandardsIgnoreStart
			eval('$data = [' . $matches[2] . '];');
			//@codingStandardsIgnoreEnd

			$indent = strpos($matches[2], "\t") === false ? str_repeat(' ', 4) : "\t";

			$out = [];
			foreach ($data as $key => $fixture) {
				$pieces = explode('.', $fixture);
				$fixtureName = $pieces[count($pieces) - 1];
				$fixtureName = Inflector::camelize(Inflector::underscore($fixtureName));
				$pieces[count($pieces) - 1] = $fixtureName;

				if (count($pieces) > 2) {
					$pluginName = Inflector::camelize(Inflector::underscore($pieces[count($pieces) - 2]));
					$pieces[count($pieces) - 2] = $pluginName;
				}

				$out[] = implode('.', $pieces);
			}

			$content = implode(",\n$indent$indent", $export($out));
			if ($content) {
				$content = "\n$indent$indent" . $content . ",";
			}

			return $matches[1] . $content . "\n$indent" . $matches[3];
		};

		$contents = preg_replace_callback(
			'/(public \$fixtures\s+=\s+(?:array\(|\[))(.*?)(\);|\];)/ms',
			$processor,
			$contents,
			-1,
			$count
		);

		return $contents;
	}

}
