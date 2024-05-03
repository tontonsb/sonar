<?php

namespace Tontonsb\Sonar;

class Recording
{
	protected string $kml;
	protected string $location;
	protected string $date;
	protected string $sonarFile;

	public function __construct(public readonly string $dir)
	{
		$this->validate();

		[$this->location, $this->date, $this->sonarFile] = explode('-', $this->dir);

		$this->loadClips();
	}

	protected function validate(): void
	{
		if (2 !== substr_count($this->dir, '-'))
			throw new DomainException("Bad name: $this->dir. Need exactly 2 dashes: location-date-sonarFile.\n");
	}

	public function loadClips(): void
	{
		$this->kml = '';

		$clipXmls = array_map(
			fn($file) => new Clip($file),
			glob($this->dir.'/*/*/doc_web.kml.txt'),
		);

		// Sort so that clip3 src is next to clip3 original
		usort(
			$clipXmls,
			fn($a, $b) => $a->clip.$a->correction <=> $b->clip.$b->correction,
		);

		foreach ($clipXmls as $clipXml) {
			$this->kml .= $clipXml->getPreparedDocument(
				implode('/', [Config::get('base'), $this->dir, $clipXml->correction])
			);
		}
	}

	public function getKml(): string
	{
		return $this->wrap($this->kml);
	}

	public function wrap($contents): string
	{
		return <<<XML
			<?xml version="1.0" encoding="utf-8"?>
			<kml xmlns="http://www.opengis.net/kml/2.2">
			<Folder>
				<name>$this->dir</name>
				<open>1</open>
				$contents
			</Folder>
			</kml>
			XML;
	}
}
