<?php

namespace Tontonsb\Sonar;

use DOMElement;

class MasterImage extends KML
{
	public function __construct(
		protected DOMElement $masterImage,
	) {
		parent::__construct();
	}

	protected function initRoot(): void
	{
		$master = $this->masterImage->cloneNode(true);

		// Master images are invisible in the combined XML
		foreach ($master->getElementsByTagName('visibility') as $el)
			$el->nodeValue = 1;

		$this->root = $this->xml->importNode($master, true);
	}
}
