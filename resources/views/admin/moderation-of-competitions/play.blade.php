@extends("admin.template")

@section("title")
    {{ $text }}
@endsection

@section("h3")
    <h3>{{ $text }}</h3>
@endsection

@section("main")
    <div>
        <video height="500px" controls="controls" autoplay>
            <source src="{{ $video }}">
        </video>
    </div>
@endsection
