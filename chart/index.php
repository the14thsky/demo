<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>MET</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.css" />
    <style>
        body {
            margin:0;
            padding:0;
            height:100%;
            background-color:#f8f8f8;
        }
        .content {
            margin:0 auto;
            width:100%;
            max-width:900px;
        }
        h1,p {
            margin:0;
            padding:15px;
            font-family:'Roboto',sans-serif;
            text-align:center;
            color:#111;
        }
    </style>
</head>
<body>
<div class="content">
	<h1>Chart</h1>
    <div style="width:100%;height:500px;">
        <canvas id="loginChart"></canvas>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>
<script>
var ctx = document.getElementById('loginChart');
var loginChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ["00:00 - 00:59","01:00 - 01:59","02:00 - 02:59","03:00 - 03:59","04:00 - 04:59","05:00 - 05:59","06:00 - 06:59","07:00 - 07:59","08:00 - 08:59","09:00 - 09:59","10:00 - 10:59","11:00 - 11:59","12:00 - 12:59","13:00 - 13:59","14:00 - 14:59","15:00 - 15:59","16:00 - 16:59","17:00 - 17:59","18:00 - 18:59","19:00 - 19:59","20:00 - 20:59","21:00 - 21:59","22:00 - 22:59","23:00 - 23:59"],
        datasets: [{
            label: 'Number of Logins per hour',
            data: [2,0,0,1,0,0,3,2,0,4,8,19,2,2,20,19,28,0,9,1,0,0,0,0],
            backgroundColor: 'rgba(54, 162, 235)',
            borderColor: 'rgba(54, 162, 235, 0.2)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true
                }
            }]
        }
    }
});
</script>
</body>
</html>
