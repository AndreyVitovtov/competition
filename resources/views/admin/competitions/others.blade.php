@extends("admin.template")

@section("title")
    @lang('pages.others')
@endsection

@section("h3")
    <h3>@lang('pages.others')</h3>
@endsection

@section("main")
    <style>
        textarea {
            width: 100%;
            height: 100px;
            border: solid 1px #d1d1d1;
            border-radius: 3px;
        }
    </style>

    <div>
        <form action="">
            <select name="language" id="language">
                @foreach($languages as $language)
                    <option value="{{ $language->id }}"
                    @if($language->id == $lang) selected @endif
                    >{{ base64_decode($language->emoji) }} {{ $language->name }}</option>
                @endforeach
            </select>
            <br>
            <br>
            <input type="submit" value="@lang('pages.go')" class="button">
        </form>

        @if($lang)
            <br>
            <form action="{{ route('others-save') }}" method="POST" id="others">
                <input type="hidden" name="language" value="{{ $lang ?? null }}">
                @csrf
                <label for="description">@lang('pages.description')</label>
                <input type="hidden" name="id" value="{{ $competition->id ?? null }}">
                <textarea name="description" id="description">{{ $competition->description ?? '' }}</textarea>
            </form>
            <button form="others" class="button">@lang('pages.save')</button>
            @if($competition)
                <button form="delete" class="button">@lang('pages.delete')</button>
                <form action="{{ route('others-delete') }}" method="POST" id="delete">
                    <input type="hidden" name="language" value="{{ $lang }}">
                    @csrf
                    <input type="hidden" name="id" value="{{ $competition->id }}">
                </form>
            @endif
        @endif
    </div>
@endsection
