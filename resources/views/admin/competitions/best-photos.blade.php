@extends("admin.template")

@section("title")
    @lang('pages.best_photos')
@endsection

@section("h3")
    <h3>@lang('pages.best_photos')</h3>
@endsection

@section("main")
    <style>
        textarea {
            width: calc(100% - 13px);
            height: 100px;
            border: solid 1px #d1d1d1;
            border-radius: 3px;
            padding: 5px;
        }
    </style>

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
            <form action="{{ route('best-photos-save') }}" method="POST" id="save">
                @csrf
                <input type="hidden" name="languages_id" value="{{ $lang }}">
                <label for="description">@lang('pages.description')</label>
                <textarea name="description"
                          @if($competition) disabled @endif>{{ $competition->description ?? "" }}</textarea>
                <label for="channel_id">@lang('pages.channel_id')</label>
                <input type="text" name="channel_id" value="{{ $competition->channel_id ?? "" }}" id="channel_id"
                       @if($competition) disabled @endif>
                <label for="channel_name">@lang('pages.channel_name')</label>
                <input type="text" name="channel_name" value="{{ $competition->channel_name ?? "" }}" id="channel_name"
                       @if($competition) disabled @endif>
                <br>
                <br>
            </form>
            @if($competition)
                <form action="{{ route('best-photos-complete') }}" method="POST" id="complete">
                    @csrf
                    <input type="hidden" name="best_photos_id" value="{{ $competition->id }}">
                    <input type="hidden" name="languages_id" value="{{ $competition->languages_id }}">
                </form>
                <button form="complete" class="button">@lang('pages.complete')</button>
            @endif
            @if(!$competition)
                <button form="save" class="button">@lang('pages.save')</button>
            @endif
            <button form="archive" class="button"><i class="icon-archive"></i> @lang('pages.archive')</button>
            <form action="{{ route('best-photos-archive') }}" id="archive" method="GET">
                <input type="hidden" name="language" value="{{ $lang ?? null }}">
            </form>
            @if($competition)
                @if($res)
                    <br>
                    <br>
                    <hr>
                    <br>
                    <table>
                        <tr>
                            <td>@lang('pages.user')</td>
                            <td>@lang('pages.post')</td>
                            <td>@lang('pages.count_likes')</td>
                        </tr>
                        @foreach($res as $r)
                            <tr>
                                <td><a href="{{ route('user-profile', $r->userId) }}" class="link">{{ $r->username }}</a></td>
                                <td><a href="{{ $r->post }}" class="link" target="_blank">{{ $r->post }}</a></td>
                                <td>{{ $r->count }}</td>
                            </tr>
                        @endforeach
                    </table>
                @endif
            @endif
        @endif
    </div>
@endsection
