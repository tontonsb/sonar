<?php

namespace Tontonsb\Sonar;

use DOMElement;

class Areas extends KML
{
	protected function initRoot(): void
	{
		$this->root = $this->xml->createElement('Folder');

		$this->root->appendChild(
			$this->xml->createElement('name', 'Areas')
		);

		$this->root->appendChild(
			$this->xml->createElement('open', 1)
		);
	}
}
