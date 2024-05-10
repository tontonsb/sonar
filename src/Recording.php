<?php

namespace Tontonsb\Sonar;

use DOMElement;

class Recording extends KML
{
	public readonly string $location;
	public readonly string $date;
	public readonly string $sonarFile;
	protected array $clips = [];
	protected array $areas = [];
	protected array $masterImages = [];

	public function __construct(public readonly string $dir)
	{
		$this->validate();

		[$this->location, $this->date, $this->sonarFile] = explode('-', $this->dir);

		parent::__construct();

		$this->loadClips();
		$this->readMasterImages();
	}

	protected function validate(): void
	{
		if (2 !== substr_count($this->dir, '-'))
			throw new \DomainException("Bad name: $this->dir. Need exactly 2 dashes: location-date-sonarFile.\n");
	}

	protected function initRoot(): void
	{
		$this->root = $this->xml->createElement('Folder');

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

			if ($masterImage = $clipData->getMasterImage())
				$this->masterImages[$clipData->clip.'-'.$clipData->correction] = $masterImage;

			// Now copy over the needed data to a Clip DOM that we want in the output
			$clip = $this->getClip($clipData->clip);

			if ('src' == $clipData->correction) {
				// We only do areas for SRC
				$area = new Area($this->xml, $this->sonarFile, $clipData->clip);

				foreach ($clipData->getStyles() as $style) {
					$clip->addChild($style);
					$area->addChild($style);
				}

				$clip->addChild($clipData->track);

				$clip->addSRC($clipData->left);
				$clip->addSRC($clipData->right);

				$area->addAreaFromTrack($clipData->left);
				$area->addAreaFromTrack($clipData->right);
				$this->areas[] = $area;
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

	protected function getClip($key): Clip
	{
		return $this->clips[$key] ??= new Clip($this->xml, $key);
	}

	public function readMasterImages(): void
	{
	}

	public function getAreas(): array
	{
		return $this->areas;
	}

	public function getMasterImages(): array
	{
		return $this->masterImages;
	}
}
