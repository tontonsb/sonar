import './style.css'
import 'ol/ol.css'

import Map from 'ol/Map'
import View from 'ol/View'
import KML from 'ol/format/KML'
import TileLayer from 'ol/layer/Tile'
import VectorLayer from 'ol/layer/Vector'
import { fromLonLat } from 'ol/proj'
import OSM from 'ol/source/OSM'
import VectorSource from 'ol/source/Vector'

const map = new Map({
  target: 'map',
  layers: [
    new TileLayer({
      source: new OSM(),
    }),
	new VectorLayer({
		source: new VectorSource({
			url: 'samples/doc_web.kml',
			format: new KML(),
		}),
	}),
  ],
  view: new View({
    center: fromLonLat([23.54, 56.87]),
    zoom: 12,
  }),
})
