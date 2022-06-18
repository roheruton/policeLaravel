@extends('adminlte::page')

@section('title', 'Dashboard')


@section('content')
    <div class="row">
        <div class="col-md-11 pt-4 m-auto">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        Crear Policia
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('police.store2') }}" method="post" enctype='multipart/form-data'>
                        @csrf
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Ci</label>
                                <input type="text" class="form-control @error('ci') is-invalid @enderror" name="ci" id="ci"
                                    aria-describedby="emailHelp" placeholder="Carnet de identidad" required>
                                @error('ci')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror

                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Nombre: </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                                    id="name" aria-describedby="emailHelp" placeholder="Nombre" required>
                                @error('name')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Apellidos: </label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                    name="last_name" id="last_name" aria-describedby="emailHelp" placeholder="Apellidos" required>
                                @error('last_name')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Fecha de Nacimiento: </label>
                                <input type="date" class="form-control @error('dateOfBirth') is-invalid @enderror"
                                    name="dateOfBirth" id="dateOfBirth" aria-describedby="emailHelp"
                                    placeholder="Fecha de nacimiento" required>
                                @error('dateOfBirth')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="custom-file mb-3">
                                <input type="file" class="custom-file-input @error('photos') is-invalid @enderror"
                                    name="photos[]" multiple="multiple" id="customFile" required>
                                <label class="custom-file-label" for="customFile">Fotos:</label>
                                @error('photos')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
                        <a href="{{ route('police.index') }}"> Cancelar</a>

                    </form>
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
