@php
    use App\Models\Game\Moderation\PlayerReport;
@endphp

@props([
    'reports' => collect()
])

@php
    /** @var \Illuminate\Support\Collection<\App\Models\Game\Moderation\PlayerReport>|\Illuminate\Pagination\LengthAwarePaginator $reports*/

    $handledReports = collect();
    $unhandledReports = collect();

    $reports->each(function (PlayerReport $report) use ($handledReports, $unhandledReports) {
        if($report->handled)
            $handledReports->add($report);
        else
            $unhandledReports->add($report);
    })

@endphp

<x-layouts.admin>
    <div class="w-full p-2 md:px-16">
        @if($reports->count() <= 0)
            <h1 class="text-2xl font-bold text-center">No Reports</h1>
        @endif

        @if($unhandledReports->count() > 0)
            <h1 class="text-xl font-bold mb-2">Unhandled Reports</h1>

            <table class="border-spacing-3 mb-8">
                <thead>
                <th>Time</th>
                <th>Reporter</th>
                <th>Reported</th>
                <th>Reason</th>
                <th>Details</th>
                <th>Match Info</th>
                <th>Actions</th>
                </thead>
                <tbody>
                @foreach($unhandledReports as $report)
                    <x-admin.tools.report-list-entry :$report/>
                @endforeach
                </tbody>
            </table>
        @endif

        @if($handledReports->count() > 0)
            <h1 class="text-xl font-bold mb-2">Handled Reports</h1>

            <table class="border-spacing-3">
                <thead>
                <th>Time</th>
                <th>Reporter</th>
                <th>Reported</th>
                <th>Reason</th>
                <th>Details</th>
                <th>Match Info</th>
                <th>Consequences</th>
                <th>Handled By</th>
                </thead>
                <tbody>
                @foreach($handledReports as $report)
                    <x-admin.tools.report-list-entry :$report/>
                @endforeach
                </tbody>
            </table>
        @endif

        <div class="mt-4">
            {{ $reports->links() }}
        </div>
    </div>
</x-layouts.admin>