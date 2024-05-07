<?php

namespace Tontonsb\Sonar;

use DOMDocument;
use DOMElement;

abstract class KML
{
	protected DOMDocument $xml;
	protected DOMElement $kml;
	protected DOMElement $root;

	public function __construct()
	{
		$this->initDoc();
		$this->initRoot();
		$this->kml->appendChild($this->root);
	}

	// Initialize $this->root
	abstract protected function initRoot(): void;

	protected function initDoc(): void
	{
		$this->xml = new DOMDocument('1.0', 'utf-8');
		$this->xml->formatOutput = true;

		$this->kml = $this->xml->createElementNS('http://www.opengis.net/kml/2.2', 'kml');
		$this->xml->appendChild($this->kml);
	}

	public function addChild(DOMElement $node): void
	{
		$this->root->appendChild(
			$this->xml->importNode($node, true)
		);
	}

	public function getKml(): string
	{
		return $this->xml->saveXML();
	}
}
