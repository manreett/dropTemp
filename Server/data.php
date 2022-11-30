<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('location: logout.php');
    exit;
}

$configs = include('config.php');

$conn = new mysqli($configs['db_server'], $configs['db_user'], $configs['db_pass'], $configs['db_name']);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    switch ($_POST['timeRadio']) {
        case '10m':
            $sql = sprintf("SELECT timestamp, temp, humid FROM data WHERE email = '%s' ORDER BY timestamp DESC LIMIT 10", $conn->real_escape_string($_SESSION['email']));
            break;
        case '3h':
            $sql = sprintf("SELECT timestamp, temp, humid FROM data WHERE email = '%s' ORDER BY timestamp DESC LIMIT 180", $conn->real_escape_string($_SESSION['email']));
            break;
        case '6h':
            $sql = sprintf("SELECT timestamp, temp, humid FROM data WHERE email = '%s' ORDER BY timestamp DESC LIMIT 360", $conn->real_escape_string($_SESSION['email']));
            break;
        default:
            $sql = sprintf("SELECT timestamp, temp, humid FROM data WHERE email = '%s' ORDER BY timestamp DESC LIMIT 60", $conn->real_escape_string($_SESSION['email']));
            break;
    }
} else {
    $sql = sprintf("SELECT timestamp, temp, humid FROM data WHERE email = '%s' ORDER BY timestamp DESC LIMIT 60", $conn->real_escape_string($_SESSION['email']));
}
$result = $conn->query($sql);
$array = $result->fetch_all(MYSQLI_ASSOC);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Droptemp Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>
<body>
<nav class="navbar navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand">dropTemp</a>
        <p class="navbar-text">Logged in as: <?php echo htmlspecialchars($_SESSION['email']); ?></p>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</nav>

<div class="container mt-3">
    <div class="row row-cols-2">
        <div class="col-md-4 col-sm-12 col-12">
            <div class="card">
                <div class="card-header">
                    Time span
                </div>
                <div class="card-body">
                    <form action="" method="post">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="timeRadio" id="10mRadio" value="10m">
                            <label class="form-check-label" for="10mRadio">
                                10 Minutes
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="timeRadio" id="1hRadio" value="1h">
                            <label class="form-check-label" for="1hRadio">
                                1 Hour
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="timeRadio" id="3hRadio" value="3h">
                            <label class="form-check-label" for="3hRadio">
                                3 Hours
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="timeRadio" id="6hRadio" value="6h">
                            <label class="form-check-label" for="6hRadio">
                                6 Hours
                            </label>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Update">
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8 col-sm-12 col-12">
            <div>
                <canvas id="myChart"></canvas>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/date-fns@2.29.3/index.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
        crossorigin="anonymous"></script>

<script type="text/javascript">
    let radioBtn = document.getElementById("<?php
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            echo $_POST['timeRadio'];
        } else {
            echo '1h';
        }
        ?>" + "Radio");
    radioBtn.checked = true;



    let dataArr = <?php echo json_encode($array); ?>;
    const ctx = document.getElementById('myChart');

    const tempArr = dataArr.map((index) => {
        let tempObject = {};
        tempObject.x = index['timestamp'] * 1000
        tempObject.y = index['temp'];
        return tempObject;
    })

    const humidArr = dataArr.map((index) => {
        let humidObject = {};
        humidObject.x = index['timestamp'] * 1000
        humidObject.y = index['humid'];
        return humidObject;
    })

    const lineChartData = {
        datasets: [
            {
                label: "Temperature reading [°C]",
                data: tempArr,
                borderColor: 'rgb(255, 99, 132)'
            },
            {
                label: "Humidity reading [%]",
                data: humidArr,
                borderColor: 'rgb(54, 162, 235)'
            }
        ]
    }

    const lineChartOptions = {
        scales: {
            x: {
                type: 'time',
                title: {
                    align: 'center',
                    display: 'true',
                    text: 'Time'
                }
            },
            y: {
                title: {
                    align: 'center',
                    display: 'true',
                    text: 'Readings'
                }
            }
        }
    }

    new Chart(ctx, {
        type: 'line',
        data: lineChartData,
        options: lineChartOptions
    });
</script>
</body>
</html>
