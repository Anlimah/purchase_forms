<?php
session_start();

if (!isset($_GET['status']) || !isset($_GET['exttrid'])) header('Location: index.php?status=invalid');
if (isset($_GET['status']) && empty($_GET['status'])) header('Location: index.php?status=invalid');
if (isset($_GET['exttrid']) && empty($_GET['exttrid'])) header('Location: index.php?status=invalid');/**/

if (isset($_SESSION["loginSuccess"]) && $_SESSION["loginSuccess"] = true && isset($_SESSION["vendor_id"]) && !empty($_SESSION["vendor_id"])) {
    $trans_id = $_GET["exttrid"];
} else {
    header("Location: login.php");
}

if (isset($_GET['logout'])) {
    unset($_SESSION['loginSuccess']);
    unset($_SESSION['vendor_id']);
    session_destroy();
    header('Location: login.php');
}

require_once('../bootstrap.php');

use Src\Controller\ExposeDataController;

$expose = new ExposeDataController();

$data = $expose->getApplicationInfo($_GET["exttrid"]);
?>

<!DOCTYPE html>
<html lang="en">

<?php require_once("../inc/head-section.php"); ?>

<body class="fluid-container">
    <div class="flex">
        <div class="form_card card" style="height: 350px !important;width: 650px !important; padding: 20px 20px 10px 20px !important;">
            <!--<img src="../assets/images/RMU-LOG.png" alt="RMU LOGO">-->
            <h1 style="text-align: center; color: #003262 !important; font-size:20px !important">SALE OF FORMS</h1>
            <div class="pay-status" style="margin: 0px 5%;" style="align-items: baseline;">
                <?php if (!empty($data)) { ?>
                    <table style="width:100%;border: 1px solid rgb(155, 155, 155); border-collapse: collapse;" class="mb-4">
                        <tr>
                            <td style="width: 120px; background: #f1f1f1;text-align: right; padding: 5px; font-size: 11px;"><b>VENDOR:</b></td>
                            <td colspan="2" style="text-align: left; padding: 5px; font-size: 11px;"><b><?= $data[0]["vendor_name"] ?></b></td>
                        </tr>
                        <tr>
                            <td style="background: #f1f1f1;text-align: right; padding: 5px; font-size: 11px;"><b>PRICE:</b></td>
                            <td style="text-align: left; padding: 5px; font-size: 11px;"><b><?= $data[0]["amount"] ?></b></td>
                        </tr>
                        <tr>
                            <td style="background: #f1f1f1;text-align: right; padding: 5px; font-size: 11px;"><b>APPLICATION NO:</b></td>
                            <td style="text-align: left; padding: 5px; font-size: 11px;"><b><?= "RMU-" . $data[0]["app_number"] ?></b></td>
                        </tr>
                        <tr>
                            <td style="background: #f1f1f1;text-align: right; padding: 5px; font-size: 11px;"><b>PIN NO:</b></td>
                            <td style="text-align: left; padding: 5px; font-size: 11px;"><b><?= $data[0]["pin_number"] ?></b></td>
                        </tr>
                        <tr style="border-top: 1px solid rgb(155, 155, 155)">
                            <td style="background: #f1f1f1;text-align: right; padding: 5px; font-size: 11px; padding-top:30px">INSTITUTION:</td>
                            <td style="text-align: left; padding: 5px; font-size: 11px;"><b>REGIONAL MARITIME UNIVERSITY</b></td>
                        </tr>
                        <tr>
                            <td style="background: #f1f1f1;text-align: right; padding: 5px; font-size: 11px">FORM NAME:</td>
                            <td style="text-align: left; padding: 5px; font-size: 11px;"><b><?= $data[0]["info"] . "-" . strtoupper($data[0]["name"]) ?></b></td>
                        </tr>
                    </table>
                    <center>
                        <button class="btn btn-primary"><b>Print</b></button>
                    </center>
                <?php } else { ?>
                    <div style="width: 100%;height: 100%; text-align:center">No Data available</div>
                <?php } ?>
            </div>
        </div>
    </div>

    <script src="../js/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {});
    </script>

</body>

</html>