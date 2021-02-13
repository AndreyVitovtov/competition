@extends("admin.template")

@section("title")
    @lang('pages.best_videos_archive') ({{ $language->name }})
@endsection

@section("h3")
    <h3>@lang('pages.best_videos_archive') ({{ $language->name }})</h3>
@endsection

@section("main")
    <style>
        table td:nth-child(1) {
            font-weight: normal;
        }

        textarea {
            width: 100%;
            resize: none;
            border: none;
        }
    </style>

    <div>
        <table>
            <tr>
                <td>@lang('pages.description')</td>
                <td><textarea disabled>{{ $competition->description }}</textarea></td>
            </tr>
            <tr>
                <td>@lang('pages.channel_name')</td>
                <td>{{ $competition->channel_name }}</td>
            </tr>
        </table>
        @if(!empty($res))
            <br>
            <br>
            <table>
                <tr>
                    <td>@lang('pages.user')</td>
                    <td>@lang('pages.post')</td>
                    <td>@lang('pages.count_likes')</td>
                </tr>
                @foreach($res as $r)
                    <tr>
                        <td>
                            <a href="{{ route('user-profile', $r->userId) }}" class="link">{{ $r->username }}</a>
                        </td>
                        <td><a href="{{ $r->post }}" class="link" target="_blank">{{ $r->post }}</a></td>
                        <td>{{ $r->count }}</td>
                    </tr>
                @endforeach
            </table>
        @endif
    </div>
@endsection
