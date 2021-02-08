@extends("admin.template")

@section("title")
    @lang('pages.channels')
@endsection

@section("h3")
    <h3>@lang('pages.channels')</h3>
@endsection

@section("main")
    <div>
        <form action="{{ route('channels-save') }}" method="POST">
            @csrf
            <table>
                <tr>
                    <td>@lang('pages.language')</td>
                    <td>@lang('pages.channel')</td>
                </tr>
                @foreach($channels as $channel)
                    <tr>
                        <td>{{ $channel->language->name }}</td>
                        <td>
                            <input type="text"
                                   name="channelsId[{{ $channel->id }}]"
                                   value="{{ $channel->channel_id }}"
                                   placeholder="id">
                            <br>
                            <br>
                            <input type="text"
                                   name="channelsLink[{{ $channel->id }}]"
                                   value="{{ $channel->link }}"
                                   placeholder="@lang('pages.link')">
                        </td>
                    </tr>
                @endforeach
            </table>
            <br>
            <input type="submit" value="@lang('pages.save')" class="button">
        </form>
    </div>
@endsection
