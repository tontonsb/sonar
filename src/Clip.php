<?php

namespace Tontonsb\Sonar;

class Clip
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
		return str_replace(
			Config::get('placeholder'),
			$prefix,
			$doc,
		);
	}
}
