<?php

namespace Cake\Upgrade\Utility;

class ShellSnippets extends AbstractSnippets {

	/**
	 * @param string $path
	 *
	 * @return array
	 */
	public function snippets(string $path): array {
		$list = [
			'$this->out()' => [
				'#\$this-\>out\(\)#',
				'$this->out(\'\')',
			],
			'getOptionParser()' => [
				'#public function getOptionParser\(\)(?!:)#',
				'public function getOptionParser(): \Cake\Console\ConsoleOptionParser',
			],
		];

		return $list;
	}

}
