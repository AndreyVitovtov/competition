@extends("developer.template")

@section("title")
    @lang('pages.menu_list')
@endsection

@section("h3")
    <h3>@lang('pages.menu_list')</h3>
@endsection

@section("main")
    <table>
        <tr>
            <td>Key</td>
            <td>Ru</td>
            <td>Us</td>
            <td>@lang('pages.actions')</td>
        </tr>
        @foreach($textsRu as $key => $text)
            <tr>
                <td style="text-align: left;">{{ $key }}</td>
                <td>{{ $text }}</td>
                <td>{{ $textsUs[$key] }}</td>
                <td class="actions">
                    <div>
                        <form action="{{ route('lang-menu-edit') }}" method="POST">
                            @csrf
                            <input type="hidden" name="key" value="{{ $key }}">
                            <button>
                                <i class='icon-pen'></i>
                            </button>
                        </form>
                        <form action="{{ route('lang-menu-delete') }}" method="POST">
                            @csrf
                            <input type="hidden" name="key" value="{{ $key }}">
                            <button>
                                <i class='icon-trash-empty'></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @endforeach
    </table>
@endsection
