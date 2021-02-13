@extends("admin.template")

@section("title")
    @lang('pages.best_photos_archive') ({{ $language->name }})
@endsection

@section("h3")
    <h3>@lang('pages.best_photos_archive') ({{ $language->name }})</h3>
@endsection

@section("main")
    <div>
        <table>
            <tr>
                <td>@lang('pages.date_time')</td>
                <td>@lang('pages.channel_name')</td>
                <td>@lang('pages.actions')</td>
            </tr>
            @foreach($competitions as $competition)
                <tr>
                    <td>{{ $competition->date }} {{ $competition->time }}</td>
                    <td>{{ $competition->channel_name }}</td>
                    <td class="actions">
                        <div>
                            <form action="{{ route('best-photos-archive-details') }}" method="GET">
                                <input type="hidden" name="id" value="{{ $competition->id }}">
                                <button>
                                    <i class='icon-eye-2'></i>
                                </button>
                            </form>
                            <br>
                            <form action="{{ route('best-photos-archive-delete') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{ $competition->id }}">
                                <input type="hidden" name="language" value="{{ $language->id }}">
                                <button>
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
