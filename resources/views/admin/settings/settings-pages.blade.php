@extends("admin.template")

@section("title")
    @lang('pages.settings_pages')
@endsection

@section("h3")
    <h3>@lang('pages.settings_pages')</h3>
@endsection

@section("main")
    <div class="settings settings_pages">
        <div>
            <div>
                <form action="{{ route('pages-go-lang') }}" method="GET">
                    <div>
                        <select name="lang" class="language-go">
                            @foreach($languages as $lang)
                                <option value="{{ $lang->code }}"
                                    @if($lang->code === $l)
                                        selected
                                    @endif
                                >{{ base64_decode($lang->emoji) }} {{ $lang->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <br>
                    <div>
                        <input type="submit" value="@lang('pages.go')" class="button">
                    </div>
                </form>
            </div>
            <br>
            <div class="overflow-X-auto">
                <table border="1">
                    <tr class="head">
                        <td>
                            №
                        </td>
                        <td>
                            @lang('pages.text')
                        </td>
                        <td>
                            @lang('pages.description')
                        </td>
                        <td>
                            @lang('pages.edit')
                        </td>
                    </tr>
                    @foreach($fields as $field)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ base64_decode($field->text) }}</td>
    {{--                        <td>{{ $field['description'] }}</td>--}}
                            <td>@lang('settings_pages.'.$field->name)</td>
                            <td class="actions">
                                <div>
                                    <form action="/admin/settings/pages/edit" method="POST">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $field->id }}">
                                        <input type="hidden" name="lang" value="{{ $l }}">
                                        <button>
                                            <i class='icon-pen'></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
            {{ $fields->links() }}
        </div>
    </div>
@endsection
