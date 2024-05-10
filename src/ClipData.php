<?php

namespace Tontonsb\Sonar;

use DOMDocument;
use DOMElement;
use Exception;

class ClipData
{
	public string $prefix = '';

	public readonly string $correction;
	public readonly string $clip;
	public readonly DOMElement $track;
	public readonly DOMElement $left;
	public readonly DOMElement $right;

	protected DOMElement $document;

	public function __construct(public readonly string $kmlFile)
	{
		[$_, $this->correction, $this->clip, $_] = explode('/', $this->kmlFile);
	}

	public function load(): void
	{
		$xml = new DOMDocument;
		$xml->preserveWhiteSpace = false; // Lai beigās var no jauna noformēt
		$xml->loadXML($this->readAndPrepareXmlString());

		$kml = $xml->documentElement;
		if ('kml' !== $kml->tagName)
			throw new Exception("$this->kmlFile is not a KML file.");

		if (1 !== $kml->childElementCount)
			throw new Exception("Clips should contain one Document but $this->kmlFile has $kml->childElementCount children.");

		$this->document = $kml->firstElementChild;
		if ('Document' !== $this->document->tagName)
			throw new Exception("Clips should contain a Document but $this->kmlFile contains {$this->document->tagName}.");

		foreach ($this->document->getElementsByTagName('Folder') as $folder) {
			$name = $folder->getElementsByTagName('name')[0]->nodeValue ?? null;

			match ($name) {
				'Sonar Track' => $this->track = $folder,
				'Left Channel' => $this->left = $folder,
				'Right Channel' => $this->right = $folder,
				default => null,
			};
		}
	}

	protected function readAndPrepareXmlString(): string
	{
		// replace URLTOKEN with the needed prefix
		return str_replace(
			Config::get('placeholder'),
			$this->prefix,
			file_get_contents($this->kmlFile),
		);
	}

	public function getStyles(): array
	{
		return [
			...iterator_to_array($this->document->getElementsByTagName('Style')),
			...iterator_to_array($this->document->getElementsByTagName('StyleMap')),
		];
	}

	public function getMasterImage(): ?string
	{
		$filename = dirname($this->kmlFile).'/files/MasterImage.png';

		return file_exists($filename) ? $filename : null;
	}
}
