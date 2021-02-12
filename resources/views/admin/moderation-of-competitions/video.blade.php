@extends("admin.template")

@section("title")
    @lang('pages.moderation-of-competitions-video')
@endsection

@section("h3")
    <h3>@lang('pages.moderation-of-competitions-video')</h3>
@endsection

@section("main")
    <div>
        <table>
            <tr>
                <td>@lang('pages.user')</td>
                <td>@lang('pages.text')</td>
                <td>@lang('pages.video')</td>
                <td>@lang('pages.actions')</td>
            </tr>
            @foreach($videos as $video)
                <tr>
                    <td><a href="{{ route('user-profile', $video->users_id) }}" class="link">{{ $video->user->username }}</a></td>
                    <td>{{ $video->text }}</td>
                    <td><a href="{{ route('moderation-of-competitions-video-play', $video->id) }}" class="link">@lang('pages.video')</a></td>
                    <td class="actions">
                        <div>
                            <form action="{{ route('moderation-of-competitions-video-active') }}"
                                  method="POST"
                                  id="form-active-{{ $video->id }}">
                                @csrf
                                <input type="hidden" name="id" value="{{ $video->id }}">
                                <button form="form-active-{{ $video->id }}">
                                    <i class='icon-play-1'></i>
                                </button>
                            </form>

                            <form action="{{ route('moderation-of-competitions-video-delete') }}" method="POST" id="form-delete-{{ $video->id }}">
                                @csrf
                                <input type="hidden" name="id" value="{{ $video->id }}">
                                <button form="form-delete-{{ $video->id }}">
                                    <i class='icon-trash-empty'></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection
