<?php

namespace Tontonsb\Sonar;

use DOMDocument;
use DOMElement;

class Clip
{
	public readonly DOMElement $document;

	public function __construct(
		public readonly DOMDocument $xml,
		public readonly string $clip,
	) {
		$this->initDoc();
	}

	protected function initDoc(): void
	{
		$this->document = $this->xml->createElement('Document');

		$this->document->appendChild(
			$this->xml->createElement('name', $this->clip)
		);

		$this->document->appendChild(
			$this->xml->createElement('open', '1')
		);
	}

	public function addChild(DOMElement $node): void
	{
		$this->document->appendChild(
			$this->xml->importNode($node, true)
		);
	}

	public function addOriginal(DOMElement $track): void
	{
		$track = $track->cloneNode(true);

		// Originals are invisible by default
		foreach ($track->getElementsByTagName('visibility') as $el)
			$el->nodeValue = 0;

		$name = $track->getElementsByTagName('name')[0];
		$name->nodeValue .= ' (original)';

		$this->addChild($track);
	}

	public function addSRC(DOMElement $track): void
	{
		$track = $track->cloneNode(true);

		$name = $track->getElementsByTagName('name')[0];
		$name->nodeValue .= ' (SRC)';

		$this->addChild($track);
	}
}
