<?php

namespace Tontonsb\Sonar;

use DOMDocument;
use DOMElement;

class Recording
{
	protected string $location;
	protected string $date;
	protected string $sonarFile;
	protected array $clips = [];

	protected DOMDocument $xml;
	protected DOMElement $root;

	public function __construct(public readonly string $dir)
	{
		$this->validate();

		[$this->location, $this->date, $this->sonarFile] = explode('-', $this->dir);

		$this->initDoc();

		$this->loadClips();
	}

	protected function validate(): void
	{
		if (2 !== substr_count($this->dir, '-'))
			throw new \DomainException("Bad name: $this->dir. Need exactly 2 dashes: location-date-sonarFile.\n");
	}

	protected function initDoc(): void
	{
		$this->xml = new DOMDocument('1.0', 'utf-8');
		$this->xml->formatOutput = true;

		$this->kml = $this->xml->createElementNS('http://www.opengis.net/kml/2.2', 'kml');
		$this->xml->appendChild($this->kml);

		$this->root = $this->xml->createElement('Folder');
		$this->kml->appendChild($this->root);

		$this->root->appendChild(
			$this->xml->createElement('name', $this->dir)
		);

		$this->root->appendChild(
			$this->xml->createElement('open', 1)
		);
	}

	protected function loadClips(): void
	{
		$clipDatas = array_map(
			fn($file) => new ClipData($file),
			glob($this->dir.'/*/*/doc_web.kml.txt'),
		);

		foreach ($clipDatas as $clipData) {
			$clipData->prefix = implode('/', [Config::get('base'), $this->dir, $clipData->correction]);
			// load() needs the prefix as it also does the URL prepration
			$clipData->load();

			// Now copy over the needed data to a Clip DOM that we want in the output
			$clip = $this->getClip($clipData->clip);

			if ('src' == $clipData->correction) {
				foreach ($clipData->getStyles() as $style)
					$clip->addChild($style);

				$clip->addChild($clipData->track);

				$clip->addSRC($clipData->left);
				$clip->addSRC($clipData->right);
			} else {
				$clip->addOriginal($clipData->left);
				$clip->addOriginal($clipData->right);
			}
		}

		usort(
			$this->clips,
			fn($a, $b) => $a->clip <=> $b->clip,
		);

		foreach ($this->clips as $clip)
			$this->root->appendChild($clip->document);
	}

	public function getClip($key): Clip
	{
		return $this->clips[$key] ??= new Clip($this->xml, $key);
	}

	public function getKml(): string
	{
		return $this->xml->saveXML();
	}
}
