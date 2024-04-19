import './style.css'
import 'ol/ol.css'
import Map from 'ol/Map.js'
import OSM from 'ol/source/OSM.js'
import TileLayer from 'ol/layer/Tile.js'
import VectorLayer from 'ol/layer/Vector.js'
import View from 'ol/View.js'
import VectorSource from 'ol/source/Vector'
import KML from 'ol/format/KML'
import { fromLonLat } from 'ol/proj'

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
