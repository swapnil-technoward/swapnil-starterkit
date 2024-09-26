@include('views::components.header',['title' => 'Dashboard'])
<div class="container-fluid">
    @include('views::components.nav')
    <div class="justify-content-center">
        <div class="row mt-5">
            <div class="col-sm-6 mb-3 mb-sm-0">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title">Total Users</h5>
                        <p class="card-text"></p>
                        <a href="#" class="btn btn-primary">Go somewhere</a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title">Total Orders</h5>
                        <p class="card-text"></p>
                        <a href="#" class="btn btn-primary">Go somewhere</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-sm-6 mb-3 mb-sm-0">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title">Special title treatment</h5>
                        <p class="card-text"></p>
                        <a href="#" class="btn btn-primary">Go somewhere</a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title">Special title treatment</h5>
                        <p class="card-text"></p>
                        <a href="#" class="btn btn-primary">Go somewhere</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-sm-12 mb-5 mb-sm-0">
                <div class="card shadow">
                    <div class="card-body">
                        <center><canvas id="myChart" style="width:100%;max-width:700px"></canvas></center>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js">
    </script>
    <script>
        let xValues = ["Italy", "France", "Spain", "USA", "Argentina"];
        let yValues = [55, 49, 44, 24, 15];
        let barColors = ["red", "green","blue","orange","brown"];
        new Chart("myChart", {
            type: "pie",
            data: {
                labels: xValues,
                datasets: [{
                    backgroundColor: barColors,
                    data: yValues
                }]
            },
            options: {
                title: {
                    display: true,
                    text: "World Wide Wine Production"
                }
            }

        });

        function generateData(value, i1, i2, step = 1) {
            for (let x = i1; x <= i2; x += step) {
                yValues.push(eval(value));
                xValues.push(x);
            }
        }
    </script>
@endpush
@include('views::components.footer')
