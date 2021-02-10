@extends("admin.template")

@section("title")
    @lang('pages.group_invitations_archive') ({{ $language->name }})
@endsection

@section("h3")
    <h3>@lang('pages.group_invitations_archive') ({{ $language->name }})</h3>
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
                <td>@lang('pages.group_id')</td>
                <td>{{ $group_id }}</td>
            </tr>
            <tr>
                <td>@lang('pages.link')</td>
                <td>
                    <select id="select_group">
                        @foreach($competition->groups as $group)
                            <option value="{{ $group->id }}" @if($group->id == $groupId) selected @endif>{{ $group->group_link }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
        </table>
        @if(!empty($res))
            <br>
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

    <script>
        $('body').on('change', '#select_group', function() {
            let url = window.location.href;
            url = url.split('&group')[0]+'&group='+$(this).val();
            $(location).attr('href', url);
        });
    </script>
@endsection
