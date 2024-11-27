@extends('default')

@section('content')

    @include('prob-notice')

    <div class="container">
        @if ($totalProbability != 100)
            <div class="alert alert-danger">
                Sum of all prizes probability must be 100%. Currently it's {{ $totalProbability }}%. You have yet to {{ $totalProbability < 100 ? 'add ' . (100 - $totalProbability) . ' %' : 'remove ' . ($totalProbability - 100) . ' %' }} to the prize.
            </div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('prizes.create') }}" class="btn btn-info">Create</a>
                </div>
                <h1>Prizes</h1>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Title</th>
                            <th>Probability</th>
                            <th>Awarded</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prizes as $prize)
                            <tr>
                                <td>{{ $prize->id }}</td>
                                <td>{{ $prize->title }}</td>
                                <td>{{ $prize->probability }}</td>
                                <td>{{ $prize->awarded }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('prizes.edit', [$prize->id]) }}" class="btn btn-primary">Edit</a>
                                        {!! Form::open(['method' => 'DELETE', 'route' => ['prizes.destroy', $prize->id]]) !!}
                                        {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                                        {!! Form::close() !!}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        <h3>Simulate</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('simulate') }}">
                            @csrf
                            <div class="form-group">
                                <label for="number_of_prizes">Number of Prizes</label>
                                <input type="number" id="number_of_prizes" name="number_of_prizes" value="50" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-primary">Simulate</button>
                        </form>
                    </div>

                    <br>

                    <div class="card-body">
                        <form method="POST" action="{{ route('reset') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">Reset</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mb-4">
        <div class="row">
            <div class="col-md-6">
                <h2>Probability Settings</h2>
                <canvas id="probabilityChart"></canvas>
            </div>
            <div class="col-md-6">
                <h2>Actual Rewards</h2>
                <canvas id="awardedChart"></canvas>
            </div>
        </div>
    </div>

@stop

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const prizeTitles = @json($prizes->pluck('title'));
            const prizeProbabilities = @json($prizes->pluck('probability'));
            const prizeAwarded = @json($prizes->pluck('awarded'));

            // Probability Chart Configuration
            const probabilityCtx = document.getElementById('probabilityChart').getContext('2d');
            const probabilityChart = new Chart(probabilityCtx, {
                type: 'pie',
                data: {
                    labels: prizeTitles,
                    datasets: [{
                        label: 'Prize Probabilities',
                        data: prizeProbabilities,
                        backgroundColor: generateColors(prizeTitles.length),
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        },
                        datalabels: {
                            formatter: (value, context) => {
                                return `${context.chart.data.labels[context.dataIndex]} (${value}%)`;
                            },
                            color: '#fff',
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });

            // Total number of prizes awarded for percentage calculation
            const totalAwarded = prizeAwarded.reduce((a, b) => a + b, 0);

            // Awarded Chart Configuration
            const awardedCtx = document.getElementById('awardedChart').getContext('2d');
            const awardedChart = new Chart(awardedCtx, {
                type: 'pie',
                data: {
                    labels: prizeTitles,
                    datasets: [{
                        label: 'Actual Rewards',
                        data: prizeAwarded,
                        backgroundColor: generateColors(prizeTitles.length),
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        },
                        datalabels: {
                            formatter: (value, context) => {
                                let percentage = ((value / totalAwarded) * 100).toFixed(2);
                                return percentage > 0 ? `${context.chart.data.labels[context.dataIndex]} (${percentage}%)` : ''; // Show label and percentage if greater than 0%
                            },
                            color: '#fff',
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });

            // Function to Generate Random Colors
            function generateColors(count) {
                const colors = [];
                for (let i = 0; i < count; i++) {
                    colors.push(`hsl(${Math.floor(Math.random() * 360)}, 70%, 60%)`);
                }
                return colors;
            }
        });
    </script>
@endpush
