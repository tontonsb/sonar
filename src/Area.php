<?php

namespace Tontonsb\Sonar;

use DOMDocument;
use DOMElement;

class Area extends KMLFragment
{
	public function __construct(
		public readonly DOMDocument $xml,
		public readonly string $sonarFile,
		public readonly string $clip,
	) {
		parent::__construct('Folder');

		$this->initDoc();
	}

	protected function initDoc(): void
	{
		$this->document->appendChild(
			$this->xml->createElement('name', $this->sonarFile.' '.$this->clip)
		);

		$this->document->appendChild(
			$this->xml->createElement('open', '1')
		);
	}

	public function addAreaFromTrack(DOMElement $track): void
	{
		$trackName = $track->getElementsByTagName('name')[0]->nodeValue;

		foreach ($track->getElementsByTagName('Folder') as $folder) {
			if ('Areas' !== $folder->getElementsByTagName('name')[0]->nodeValue)
				continue;

			$area = $folder->cloneNode(true);
			// Rename 'Areas' to 'Right channel' (or left)
			$area->getElementsByTagName('name')[0]->nodeValue = $trackName;
			$this->addChild($area);

			// There should be only one area per track
			return;
		}
	}
}
