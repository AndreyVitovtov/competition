@extends("admin.template")

@section("title")
    @lang('pages.best_photos')
@endsection

@section("h3")
    <h3>@lang('pages.best_photos')</h3>
@endsection

@section("main")
    <div>
        <form action="" method="GET">
            <select name="language" id="language">
                @foreach($languages as $language)
                    <option value="{{ $language->id }}" @if($language->id == $lang) selected @endif>
                        {{ base64_decode($language->emoji)." ".$language->name }}
                    </option>
                @endforeach
            </select>
            <br>
            <br>
            <input type="submit" value="@lang('pages.go')" class="button">
        </form>
        @if($lang != null)
            <br>
            <br>
            <form action="" method="POST">
                <label for="description">@lang('pages.description')</label>
                <textarea name="description"></textarea>
                <br>
                <br>
                <input type="submit" value="@lang('pages.save')" class="button">
            </form>
        @endif
    </div>
@endsection
