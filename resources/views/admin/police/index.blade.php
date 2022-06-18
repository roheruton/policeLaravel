@extends('adminlte::page')

@section('title', 'Dashboard')


@section('content')
    <div class="row">
        <div class="col-md-11 pt-4 m-auto">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        Gestionar Policias
                    </div>
                    <div class="card-toolbar">
                        <a href="{{ route('police.create') }}" class="btn btn-primary btn-sm"> Crear</a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <th>id</th>
                            <th>ci</th>
                            <th>nombre</th>
                            <th>apellidos</th>
                            <th>Fecha Nac.</th>
                            <th>Perfil</th>
                            <th>Opciones</th>
                        </thead>
                        <tbody>
                            @forelse ($policias as $key => $police)
                                <tr>
                                    <td>{{ $key }}</td>
                                    <td>{{ $police['ci'] }}</td>
                                    <td>{{ $police['nombre'] }}</td>
                                    <td>{{ $police['apellidos'] }}</td>
                                    <td>{{ $police['fecha_n'] }}</td>
                                    <td><img src="{{$police['avatar'][0]['url']}}" alt="" width="100"></td>
                                    <td></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7"> No se encontraron policias</td>
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
