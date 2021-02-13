@extends("admin.template")

@section("title")
    @lang('pages.moderation-of-competitions-photo')
@endsection

@section("h3")
    <h3>@lang('pages.moderation-of-competitions-photo')</h3>
@endsection

@section("main")
    <div>
        <table>
            <tr>
                <td>@lang('pages.user')</td>
                <td>@lang('pages.text')</td>
                <td>@lang('pages.photo')</td>
                <td>@lang('pages.actions')</td>
            </tr>
            @foreach($photos as $photo)
                <tr>
                    <td><a href="{{ route('user-profile', $photo->users_id) }}" class="link">{{ $photo->user->username }}</a></td>
                    <td>{{ $photo->text }}</td>
                    <td><a href="{{ route('moderation-of-competitions-photo-read', ['id' => $photo->id]) }}" class="link">@lang('pages.photo')</a></td>
                    <td class="actions">
                        <div>
                            <form action="{{ route('moderation-of-competitions-photo-active') }}"
                                  method="POST"
                                  id="form-active-{{ $photo->id }}">
                                @csrf
                                <input type="hidden" name="id" value="{{ $photo->id }}">
                                <button form="form-active-{{ $photo->id }}">
                                    <i class='icon-play-1'></i>
                                </button>
                            </form>

                            <form action="{{ route('moderation-of-competitions-photo-delete') }}" method="POST" id="form-delete-{{ $photo->id }}">
                                @csrf
                                <input type="hidden" name="id" value="{{ $photo->id }}">
                                <button form="form-delete-{{ $photo->id }}">
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
