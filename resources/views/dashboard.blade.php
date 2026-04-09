<?php
  $level = Session::get('level');
  ?>

  
@extends('template.master')
@section('title', 'Dashboard Superman')
@section('konten')

<!-- MAIN -->
<div class="main">
	<!-- MAIN CONTENT -->
	<div class="main-content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-6">
					<div class="panel">
						<div class="panel-heading">
							<h4>Proses SPP</h4>
						</div>
						<hr>
						<div class="panel-body">
							<canvas id="canvas-proses-spp" height="280" width="600"></canvas>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="panel">
						<div class="panel-heading">
							<h4>Status Pembayaran SPP</h4>
						</div>
						<hr></hr>
						<div class="panel-body">
							<canvas id="canvas-bayar-spp" height="280" width="280"></canvas>
						</div>
					</div>
				</div>
			</div>
		
			<!-- OVERVIEW -->
			
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.4.0/chart.min.js"></script>
<script>
    var petugas = <?php echo $petugas; ?>;
    var spp = <?php echo $spp; ?>;
	var bayar_label = <?php echo $bayar_label; ?>;
    var bayar_count = <?php echo $bayar_count; ?>;
    var barChartData = {
        labels: petugas,
        datasets: [{
            label: 'Jumlah SPP',
            backgroundColor: [
				'rgb(255, 99, 132)',
      			],
            data: spp
        }]
    };

	var pieChartData = {
        labels: bayar_label,
        datasets: [{
            label: 'Jumlah SPP',
            backgroundColor: [
				'rgb(255, 99, 132)',
      			'rgb(54, 162, 235)',],
            data: bayar_count
        }]
    };

    window.onload = function() {
        var c_proses = document.getElementById("canvas-proses-spp").getContext("2d");
		var c_bayar = document.getElementById("canvas-bayar-spp").getContext("2d");
        window.myBar = new Chart(c_proses, {
            type: 'bar',
            data: barChartData,
            options: {
                elements: {
                    rectangle: {
                        borderWidth: 2,
                        borderColor: '#c1c1c1',
                        borderSkipped: 'bottom'
                    }
                },
                responsive: true,
				plugins: {
					legend: {
						position: 'bottom'
					}
				}
            }
        });

		window.myBar = new Chart(c_bayar, {
            type: 'pie',
            data: pieChartData,
            options: {
                responsive: true,
				maintainAspectRatio: false,
				plugins: {
					legend: {
						position: 'bottom'
					}
				}
            }
        });
    };
</script>
@endsection