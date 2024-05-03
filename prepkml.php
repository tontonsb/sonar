<?php

$base = 'https://sonar.glaive.pro/kml';
$placeholder = 'URLTOKEN';

// Take all ...Sonar... dirs and wrap them in the Recording class
$recordings = array_map(
	fn($file) => new Recording($file),
	array_filter(
		glob('*'),
		fn($node) => is_dir($node) && str_contains($node, 'Sonar'),
	),
);

foreach ($recordings as $recording) {
	$kml = $recording->getKml();
	$fileName = $recording->dir.'.kml';

	file_put_contents($fileName, $kml);
	echo "File $fileName written.\n";
}

// Only definitions follow, no actions.

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
			fn($file) => new ClipXML($file),
			glob($this->dir.'/*/*/doc_web.kml.txt'),
		);

		// Sort so that clip3 src is next to clip3 original
		usort(
			$clipXmls,
			fn($a, $b) => $a->clip.$a->correction <=> $b->clip.$b->correction,
		);

		global $base;
		foreach ($clipXmls as $clipXml) {
			$this->kml .= $clipXml->getPreparedDocument(
				implode('/', [$base, $this->dir, $clipXml->correction])
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

class ClipXML
{
	public readonly string $correction;
	public readonly string $clip;
	public readonly string $document;

	public function __construct(public readonly string $kmlFile)
	{
		[$_, $this->correction, $this->clip, $_] = explode('/', $this->kmlFile);
		$this->loadDocument();
	}

	protected function loadDocument(): void
	{
		$kml = file_get_contents($this->kmlFile);

		// Extract the Document — each file only has one. No checks :))
		preg_match('#<Document>(.*)</Document>#s', $kml, $matches);
		$this->document = $matches[0];
	}

	/**
	 * Get doc with URLTOKEN replaced by the needed prefix
	 */
	public function getPreparedDocument(string $prefix): string
	{
		// Make the names more uniform
		$doc = preg_replace(
			'#<name>.*</name>#',
			"<name>$this->clip $this->correction</name>",
			$this->document,
			1,
		);

		// replace URLTOKEN with the needed prefix
		global $placeholder;
		return str_replace(
			$placeholder,
			$prefix,
			$doc,
		);
	}
}
