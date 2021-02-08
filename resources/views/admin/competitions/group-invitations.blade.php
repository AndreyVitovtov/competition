@extends("admin.template")

@section("title")
    @lang('pages.groupinvitations')
@endsection

@section("h3")
    <h3>@lang('pages.groupinvitations')</h3>
@endsection

@section("main")
    <style>
        textarea {
            width: 100%;
            border: solid 1px #d1d1d1;
            border-radius: 3px;
            height: 100px;
        }
    </style>

    <div>
        <form action="{{ route('group-invitations') }}" method="GET">
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
            <form action="{{ route('group-invitations-save') }}" method="POST">
                @csrf
                <input type="hidden" name="language" value="{{ $lang ?? null }}">
                <input type="hidden" name="id" value="{{ $competition->id ?? null }}">
                <div>
                    <label for="description">@lang('pages.description')</label>
                    <textarea name="description" id="description">{{ $competition->description ?? "" }}</textarea>
                </div>
                <div>
                    <label for="group_id">@lang('pages.group_id')</label>
                    <input type="text" name="group_id" value="{{ $competition->group_id ?? "" }}">
                </div>
                <div>
                    <label for="group_link">@lang('pages.link')</label>
                    <input type="text" name="group_link" value="{{ $competition->group_link ?? "" }}">
                </div>
                <br>
                <input type="submit" value="@lang('pages.save')" class="button">
                @if($competition)
                    <button form="competition-complete" class="button">@lang('pages.complete')</button>
                @endif
                <button form="archive" class="button"><i class="icon-archive"></i> @lang('pages.archive')</button>
            </form>
            <form action="{{ route('group-invitations-archive') }}" id="archive" method="GET">
                <input type="hidden" name="language" value="{{ $lang ?? null }}">
            </form>
            @if($competition)
                <form action="{{ route('group-invitations-complete') }}" method="POST" id="competition-complete">
                    @csrf
                    <input type="hidden" name="id" value="{{ $competition->id }}">
                </form>
            @endif
        @endif
        @if(!empty($res))
            <br>
            <table>
                <tr>
                    <td>@lang('pages.user')</td>
                    <td>@lang('pages.count_ref1')</td>
                    <td>@lang('pages.count_ref2')</td>
                </tr>
                @foreach($res as $r)
                    <tr>
                        <td>
                            <a href="{{ route('user-profile', $r->id) }}" class="link">{{ $r->username }}</a>
                        </td>
                        <td>{{ $r->countRef1 }}</td>
                        <td>{{ $r->countRef2 ?? 0 }}</td>
                    </tr>
                @endforeach
            </table>
        @endif
    </div>
@endsection
