<?php
require_once __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['userid'])) {
    header("Location: ../");
    exit();
}

$userid = $_SESSION['userid'];

$sql = "SELECT * FROM current_tickets WHERE userid = ? ORDER BY expiredate DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userid);
$stmt->execute();
$result = $stmt->get_result();

$id = $ticketname = $buydate = $expiredate = $opportunities = null;
$currentDate = new DateTime();
$currentDate = $currentDate->format('Y-m-d'); // Csak az év-hónap-nap kell

$validTicketFound = false;

while ($row = $result->fetch_assoc()) {
    $expireDate = new DateTime($row['expiredate']);
    $expireDate = $expireDate->format('Y-m-d'); // Csak az év-hónap-nap kell

    // Ha a lejárati dátum egyenlő vagy nagyobb, mint a mai nap, akkor még érvényes
    if ($expireDate >= $currentDate) {
        $id = $row['id'];
        $ticketname = $row['ticketname'];
        $buydate = $row['buydate'];
        $expiredate = $row['expiredate'];
        $opportunities = $row['opportunities'];
        $validTicketFound = true;
        break;
    }
}

$stmt->close();

$currentDate = new DateTime();
$expireDate = new DateTime($expiredate);
$interval = $currentDate->diff($expireDate);
$daysRemaining = $interval->days;
if ($expireDate < $currentDate) {
    $daysRemaining = "-";
} else {
    $interval = $currentDate->diff($expireDate);
    $daysRemaining = $interval->days;

    if ($expireDate >= $currentDate) {
        $daysRemaining++;
    }
}

$sql_latest_training = "SELECT workout_date FROM workout_stats WHERE userid = $userid ORDER BY workout_date DESC LIMIT 1";
$result_latest_training = $conn->query($sql_latest_training);
if (!$result_latest_training) {
    die("Hiba a legutóbbi edzés dátumának lekérdezésekor: " . $conn->error);
}
$latest_training = ($result_latest_training->num_rows > 0) ? $result_latest_training->fetch_assoc()['workout_date'] : $translations["n/a"];



$sql = "SELECT firstname, lastname, profile_balance FROM users WHERE userid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userid);
$stmt->execute();
$stmt->bind_result($firstname, $lastname, $profile_balance);
$stmt->fetch();

$stmt->close();



$conn->close();


require __DIR__ . '/../vendor/autoload.php';

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\Label\Font\NotoSans;

$filename = __DIR__ . "/../assets/img/logincard/{$userid}.png";
$logoPath = 'https://gymoneglobal.com/assets/img/logo.png';

if (!file_exists($filename)) {
    try {
        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($userid)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(300)
            ->margin(5)
            ->logoPath($logoPath)
            ->logoResizeToWidth(100)
            ->labelText($firstname . ' ' . $lastname)
            ->labelFont(new NotoSans(20))
            ->labelAlignment(new LabelAlignmentCenter())
            ->validateResult(false)
            ->build();

        $result->saveToFile($filename);
        header("Refresh:2");
    } catch (Exception $e) {
        echo ' . $translations["unexpected-error"] . ' . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="<?php echo $lang_code; ?>">

<head>
    <meta charset="UTF-8">
    <title><?php echo $business_name; ?> - <?php echo $translations["dashboard"]; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="shortcut icon" href="../assets/img/brand/favicon.png" type="image/x-icon">
</head>
<!-- ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<body>
    <nav class="navbar navbar-inverse visible-xs">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href=""><img width="70px" src="../assets/img/logo.png" alt="Logo"></a>
            </div>
            <div class="collapse navbar-collapse" id="myNavbar">
                <ul class="nav navbar-nav">
                    <li class="active"><a href=""><i class="bi bi-house"></i> <?php echo $translations["mainpage"]; ?></a></li>
                    <li><a href="stats/"><i class="bi bi-graph-up"></i> <?php echo $translations["statspage"]; ?></a></li>
                    <li><a href="profile/"><i class="bi bi-person-badge"></i> <?php echo $translations["profilepage"]; ?></a></li>
                    <li><a href="invoices/"><i class="bi bi-receipt"></i> <?php echo $translations["invoicepage"]; ?></a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row content">
            <?php
            $current_page = 'dashboard';
            require_once __DIR__ . '/../includes/user_sidebar.php';
            ?>
            <br>
            <div class="col-sm-10">
                <div class="d-none topnav d-sm-inline-block">
                    <h4><?php echo $translations["welcome"]; ?> <?php echo $lastname; ?> <?php echo $firstname; ?></h4>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#logoutModal">
                        <?php echo $translations["logout"]; ?>
                    </button>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title fw-semibold"><?php echo $translations["currentticket"]; ?></h4>
                                <h1><strong><?php if (!empty($ticketname)): ?>
                                            <?php echo $ticketname; ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </strong></h1>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title fw-semibold"><?php echo $translations["lastworkout"]; ?></h4>
                                <h1><strong><?= $latest_training;?></strong></h1>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title fw-semibold"><?php echo $translations["remainingdays"]; ?></h4>
                                <h1><strong><?php echo $daysRemaining; ?> </strong><?php echo $translations["day"]; ?></h1>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title fw-semibold"><?php echo $translations["profilebalance"]; ?></h4>
                                <h1><strong><?php echo $profile_balance;?></strong><?php echo $currency; ?></h1>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <?php
                                if (file_exists($filename)) {
                                    echo "<img class='img img-fluid' src='../assets/img/logincard/{$userid}.png' alt='{$firstname}-{$lastname}-{$userid}'>";
                                } else {
                                    echo "<h2 class='lead'>{$translations["qrgenerateing"]}</h2>";
                                }
                                ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- EXIT MODAL -->
        <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel">
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <p class="lead"><?php echo $translations["exit-modal"]; ?></p>
                    </div>
                    <div class="modal-footer">
                        <a type="button" class="btn btn-secondary"
                            data-dismiss="modal"><?php echo $translations["not-yet"]; ?></a>
                        <a href="logout.php" type="button"
                            class="btn btn-danger"><?php echo $translations["confirm"]; ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPTS! -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>

</html>