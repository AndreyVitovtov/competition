@extends("admin.template")

@section("title")
    @lang('pages.statistics')
@endsection

@section("h3")
    <h3>@lang('pages.statistics')</h3>
@endsection

@section("main")
    <link rel="stylesheet" href="{{asset('css/statistics.css')}}">

    @foreach($statistics as $key => $value)
        <div class="chart_statistics_2">
            <div id="chart_{{ $key }}"></div>
        </div>
    @endforeach

    <script>
        let statistics = {};
        @foreach($statistics as $key => $value)
            statistics['{{ $key }}'] = {!! json_encode($value) !!}
        @endforeach

    //Messengers
        chart.options.title = "@lang('pages.statistics_count_users_messengers')";
        chart.data = [
            ['', 'Telegram', 'Viber'],
            ["@lang('pages.statistics_users_count')", statistics.messengers.Telegram, statistics.messengers.Viber]
        ];
        chart.drawBar('chart_messengers');

    //Countries
        statistics.countries.unshift(['Country', 'Count']);
        chart.options.title = "@lang('pages.statistics_count_users')";
        chart.data = statistics.countries;
        chart.drawPie('chart_countries');

    //Visits
        statistics.visits.unshift(['Date', "@lang('pages.statistics_count')"]);
        chart.options.title = "@lang('pages.statistics_count_users_visits')";
        chart.data = statistics.visits;
        chart.drawColumn('chart_visits');
    </script>

@endsection
