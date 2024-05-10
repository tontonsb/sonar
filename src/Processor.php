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

		$areas = new Areas;

		$masterImgDir = Config::get('master_image_dir');
		if (!file_exists($masterImgDir)) {
			mkdir($masterImgDir);
		} else if (!is_dir($masterImgDir)) {
			throw new \RuntimeException("Master image dir $masterImgDir exists and is not a directory.");
		}

		foreach ($recordings as $recording) {
			echo "Processing recording $recording->sonarFile in $recording->dir.\n";

			foreach ($recording->getAreas() as $area)
				$areas->addChild($area->document);

			$masterImages = $recording->getMasterImages();
			foreach ($masterImages as $clipname => $image)
				copy($image, $masterImgDir.'/'.$recording->sonarFile.'-'.$clipname.'.png');

			echo "\t".count($masterImages)." master images found and copied to directory $masterImgDir\n";

			$kml = $recording->getKml();
			$fileName = $recording->dir.'.kml';

			file_put_contents($fileName, $kml);
			echo "\tFile $fileName written.\n";
		}

		file_put_contents('Areas.kml', $areas->getKml());
		echo "All covered areas written to Areas.kml.\n";
	}
}
