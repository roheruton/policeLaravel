@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content')
    <div class="row">
        <div class="col-md-12 pt-4 m-auto">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        Crear Policia
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('police-unit.store') }}" method="post" class="row"
                        enctype='multipart/form-data'>
                        @csrf
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Nombre: </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                                    id="name" aria-describedby="emailHelp" placeholder="Ingrese un nombre" required>
                                @error('name')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror

                            </div>

                            <div class="form-group">
                                <label for="exampleInputEmail1">Latitud: </label>
                                <input type="text" class="form-control @error('lat') is-invalid @enderror" name="lat"
                                    id="lat" aria-describedby="emailHelp" placeholder="Ingrese la latitud" required
                                    readonly>
                                @error('lat')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Longitud: </label>
                                <input type="text" class="form-control @error('lng') is-invalid @enderror" name="lng"
                                    id="lng" aria-describedby="emailHelp" placeholder="Ingrese la longitud" required
                                    readonly>
                                @error('lng')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Direccion: </label>
                                <input type="text" class="form-control @error('direction') is-invalid @enderror"
                                    name="direction" id="direction" aria-describedby="emailHelp"
                                    placeholder="Ingrese la direccion" required>
                                @error('direction')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div id="map">

                            </div>
                            <a href='#' id='geolocate' class='ui-button'>Find me</a>

                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
                        <a href="{{ route('police-unit.index') }}"> Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link href='https://api.mapbox.com/mapbox.js/v3.3.1/mapbox.css' rel='stylesheet' />
    <style>
        #map {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 100%;
        }

        .ui-button {
            background: #3887BE;
            color: #FFF;
            display: block;
            position: absolute;
            top: 50%;
            left: 50%;
            width: 160px;
            margin: -20px 0 0 -80px;
            z-index: 100;
            text-align: center;
            padding: 10px;
            border: 1px solid rgba(0, 0, 0, 0.4);
            border-radius: 3px;
        }

        .ui-button:hover {
            background: #3074a4;
            color: #fff;
        }

    </style>
@stop

@section('js')
    <script src='https://api.mapbox.com/mapbox.js/v3.3.1/mapbox.js'></script>
    <script>
        console.log('Hi!');
    </script>
    <script>
        L.mapbox.accessToken =
            'pk.eyJ1IjoibHVuaXRhYmIzIiwiYSI6ImNrbDJzMnRzNTBsYzMycHFqZGQwd2IxeXEifQ.g6Ozwc9YcfsNiNBdDyrQPA';
        var map = L.mapbox.map('map')
            .setView([40, -74.50], 9)
            .addLayer(L.mapbox.styleLayer('mapbox://styles/mapbox/streets-v11')).addControl(L.mapbox.geocoderControl(
                'mapbox.places', {
                    autocomplete: true
                },
            ));

        var myLayer = L.mapbox.featureLayer().addTo(map);
        var geocoder = L.mapbox.geocoder('mapbox.places');
        geocoder.query('Chester, NJ', showMap);

        map.on('move', function() {
            var {
                lat,
                lng
            } = map.getCenter();
            document.getElementById('lat').value = lat;
            document.getElementById('lng').value = lng;

        });

        function showMap(err, data) {
            // The geocoder can return an area, like a city, or a
            // point, like an address. Here we handle both cases,
            // by fitting the map bounds to an area or zooming to a point.
            if (data.lbounds) {
                map.fitBounds(data.lbounds);
            } else if (data.latlng) {
                map.setView([data.latlng[0], data.latlng[1]], 13);
            }
        }

        if (!navigator.geolocation) {
            geolocate.innerHTML = 'Geolocation is not available';
        } else {
            geolocate.onclick = function(e) {
                e.preventDefault();
                e.stopPropagation();
                map.locate();
            };
        }

        // Once we've got a position, zoom and center the map
        // on it, and add a single marker.
        map.on('locationfound', function(e) {
            map.fitBounds(e.bounds);

            myLayer.setGeoJSON({
                type: 'Feature',
                geometry: {
                    type: 'Point',
                    coordinates: [e.latlng.lng, e.latlng.lat]
                },
                properties: {
                    'title': 'Here I am!',
                    'marker-color': '#ff8888',
                    'marker-symbol': 'star'
                }
            });

            // And hide the geolocation button
            geolocate.parentNode.removeChild(geolocate);
        });

        // If the user chooses not to allow their location
        // to be shared, display an error message.
        map.on('locationerror', function() {
            geolocate.innerHTML = 'Position could not be found';
        });
    </script>
@stop
