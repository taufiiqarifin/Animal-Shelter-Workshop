<div>

    <!-- YEAR FILTER -->
    <div class="mb-6 flex gap-3 items-center">
        <label class="font-semibold">Year:</label>

        <select wire:model="year"
                class="border rounded p-2 bg-white shadow">
            @for ($y = now()->year; $y >= 2020; $y--)
                <option value="{{ $y }}">{{ $y }}</option>
            @endfor
        </select>
    </div>

    <!-- KPI CARDS -->
    <div class="grid grid-cols-4 gap-4 mb-8">

        <div class="bg-white p-5 shadow rounded-xl">
            <h3 class="text-gray-600">Total Bookings</h3>
            <h1 class="text-3xl font-bold mt-2">{{ $this->metrics['total'] }}</h1>
        </div>

        <div class="bg-white p-5 shadow rounded-xl">
            <h3 class="text-gray-600">Successful</h3>
            <h1 class="text-3xl font-bold mt-2">{{ $this->metrics['success'] }}</h1>
        </div>

        <div class="bg-white p-5 shadow rounded-xl">
            <h3 class="text-gray-600">Cancelled</h3>
            <h1 class="text-3xl font-bold mt-2">{{ $this->metrics['cancel'] }}</h1>
        </div>

        <div class="bg-white p-5 shadow rounded-xl">
            <h3 class="text-gray-600">Success Rate</h3>
            <h1 class="text-3xl font-bold mt-2">
                {{ number_format($this->metrics['success_rate'], 2) }}%
            </h1>
        </div>
    </div>

    <!-- CHARTS GRID -->
    <div class="grid grid-cols-2 gap-6">

        <!-- TOP ACTIVITIES -->
        <div class="bg-white p-5 shadow rounded-xl">
            <h3 class="font-semibold mb-4">Top 5 Activities by Revenue</h3>
            <div id="topActivitiesChart"></div>
        </div>

        <!-- MONTHLY BOOKINGS -->
        <div class="bg-white p-5 shadow rounded-xl">
            <h3 class="font-semibold mb-4">Bookings by Month</h3>
            <div id="monthlyChart"></div>
        </div>

    </div>

</div>


<!-- APEXCHARTS CODE -->
<script>
document.addEventListener('livewire:load', function () {

    // ------------------------
    // TOP ACTIVITIES CHART
    // ------------------------
    var topActChart = new ApexCharts(document.querySelector("#topActivitiesChart"), {
        chart: { type: 'bar', height: 300 },
        series: [{
            name: 'Revenue',
            data: @json($this->topActivities['values'])
        }],
        xaxis: {
            categories: @json($this->topActivities['labels'])
        }
    });

    topActChart.render();


    // ------------------------
    // MONTHLY LINE CHART
    // ------------------------
    var monthlyChart = new ApexCharts(document.querySelector("#monthlyChart"), {
        chart: { type: 'line', height: 300 },
        series: [{
            name: 'Bookings',
            data: @json($this->monthlyData['values'])
        }],
        xaxis: {
            categories: @json($this->monthlyData['labels'])
        }
    });

    monthlyChart.render();


    // ------------------------
    // UPDATE CHARTS WHEN LIVEWIRE UPDATES
    // ------------------------
    Livewire.hook('message.processed', () => {
        topActChart.updateOptions({
            series: [{ data: @json($this->topActivities['values']) }],
            xaxis: { categories: @json($this->topActivities['labels']) }
        });

        monthlyChart.updateOptions({
            series: [{ data: @json($this->monthlyData['values']) }]
        });
    });

});
</script>
