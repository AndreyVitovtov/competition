@extends("admin.template")

@section("title")
    @lang('pages.group_invitations_archive') ({{ $language->name }})
@endsection

@section("h3")
    <h3>@lang('pages.group_invitations_archive') ({{ $language->name }})</h3>
@endsection

@section("main")
    <div>
        <table>
            <tr>
                <td>
                    @lang('pages.date_time')
                </td>
                <td>
                    @lang('pages.link')
                </td>
                <td class="actions">
                    @lang('pages.actions')
                </td>
            </tr>
        @foreach($competitions as $competition)
            <tr>
                <td>{{ $competition->date }} {{ $competition->time }}</td>
                <td>{{ $competition->group_link }}</td>
                <td class="actions">
                    <div>
                        <form action="{{ route('group-invitations-archive-details') }}" method="GET">
                            <input type="hidden" name="id" value="{{ $competition->id }}">
                            <button>
                                <i class='icon-eye-2'></i>
                            </button>
                        </form>

                        <form action="{{ route('group-invitations-archive-delete') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id" value="{{ $competition->id }}">
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
