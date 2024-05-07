<?php

namespace Tontonsb\Sonar;

use DOMDocument;
use DOMElement;

abstract class KMLFragment
{
	public readonly DOMElement $document;
	public readonly DOMDocument $xml;

	public function __construct(string $rootDocumentTag)
	{
		$this->document = $this->xml->createElement($rootDocumentTag);
	}

	public function addChild(DOMElement $node): void
	{
		$this->document->appendChild(
			$this->xml->importNode($node, true)
		);
	}
}
