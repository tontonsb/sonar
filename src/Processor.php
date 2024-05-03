<?php

namespace Tontonsb\Sonar;

class Processor
{
	public static function process(): void
	{
		$location = Config::get('location');
		if ($location)
			chdir($location);

		// Take all ...Sonar... dirs and wrap them in the Recording class
		$recordings = array_map(
			fn($file) => new Recording($file),
			array_filter(
				glob('*'),
				fn($node) => is_dir($node) && str_contains($node, 'Sonar'),
			),
		);

		if (!$recordings) {
			echo "No recordings to process found.";

			die;
		}

		foreach ($recordings as $recording) {
			$kml = $recording->getKml();
			$fileName = $recording->dir.'.kml';

			echo 'ok';
			die;

			file_put_contents($fileName, $kml);
			echo "File $fileName written.\n";
		}
	}
}
