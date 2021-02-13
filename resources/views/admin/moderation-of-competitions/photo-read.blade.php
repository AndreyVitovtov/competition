@extends("admin.template")

@section("title")
    {{ $photo->text }}
@endsection

@section("h3")
    <h3>{{ $photo->text }}</h3>
@endsection

@section("main")
    <div>
        <img src="{{ url('photo/'.$photo->photo) }}" alt="">
    </div>
@endsection
