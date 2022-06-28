
@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('prueba.detectedFace') }}" method="post" enctype="multipart/form-data">
               
                <label for="">Agrege una imagen: </label>
                <input type="file" name="image" id="image" class="form-control form-control-sm">
                <button type="submit" class="btn btn-sm btn-primary"> Guardar</button>
            </form>
        </div>
    </div>
@endsection
