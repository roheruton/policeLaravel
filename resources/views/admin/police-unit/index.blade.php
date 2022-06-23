@extends('adminlte::page')

@section('title', 'Dashboard')


@section('content')
    <div class="row">
        <div class="col-md-11 pt-4 m-auto">
            <div class="card">
                <div class="card-header">
                    <div class="card-title" style="margin-right: 70%;">
                        Gestionar Unidades Policiales
                    </div>
                    <div class="card-toolbar">
                        <a href="{{ route('police-unit.create') }}" class="btn btn-primary btn-sm"> Nuevo</a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <th>Id</th>
                            <th>Nombre</th>
                            <th>Latitud</th>
                            <th>Longitud</th>
                            <th>Dirección</th>
                            <th>Opciones</th>
                        </thead>
                        <tbody>
                            @forelse ($policeUnits as $police_unit)
                                <tr>
                                    <td>{{ $police_unit->id }}</td>
                                    <td>{{ $police_unit->name }}</td>
                                    <td>{{ $police_unit->lat }}</td>
                                    <td>{{ $police_unit->lng }}</td>
                                    <td>{{ $police_unit->direction }}</td>
                                    <td></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center"> No se encontraron policias</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        console.log('Hi!');
    </script>
@stop
