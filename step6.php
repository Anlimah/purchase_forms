<?php

use Src\Controller\ExposeDataController;

session_start();
if (isset($_SESSION['step5Done']) && $_SESSION['step5Done'] == true) {
    if (!isset($_SESSION["_step6Token"])) {
        $rstrong = true;
        $_SESSION["_step6Token"] = hash('sha256', bin2hex(openssl_random_pseudo_bytes(64, $rstrong)));
    }
} else {
    header('Location: step5.php');
}

require_once('bootstrap.php');
$expose = new ExposeDataController();

?>
<!DOCTYPE html>
<html lang="en">

<?php require_once("inc/head-section.php"); ?>

<body class="fluid-container">
    <div class="flex">
        <div class="form_card card">
            <img src="assets/images/RMU-LOG.png" alt="RMU LOG">
            <h1 style="text-align: center; color: #003262 !important; font-size:30px !important">Step 6</h1>
            <form id="step1Form" method="post" enctype="multipart/form-data" style="margin: 0px 12%;">
                <div class="mb-4">
                    <label class="form-label" for="gender">Form type</label>
                    <select title="Select the type of form you want to purchase." class="form-select form-select-sm" name="form_type" id="form_type" required>
                        <option selected disabled value="">Choose...</option>
                        <?php
                        $data = $expose->getFormTypes();
                        foreach ($data as $ft) {
                        ?>
                            <option value="<?= $ft['name'] ?>"><?= $ft['name'] ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <div class=" mb-4">
                    <label class="form-label" for="gender">Payment Method</label>
                    <select title="Select payment method" class="form-select form-select-sm" name="pay_method" id="pay_method" required>
                        <option selected disabled value="">Choose...</option>
                        <?php
                        $data = $expose->getPaymentMethods();
                        foreach ($data as $pm) {
                        ?>
                            <option value="<?= $pm['name'] ?>"><?= $pm['name'] ?></option>';
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <button class="btn btn-primary" type="submit" style="padding: 10px 10px; width:100%">Continue</button>
                <input type="hidden" name="_v6Token" value="<?= $_SESSION["_step6Token"]; ?>">
            </form>
        </div>
    </div>

    <script src="js/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#step1Form").on("submit", function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: "endpoint/verifyStep6",
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(result) {
                        console.log(result);
                        if (result) {
                            window.location.href = "step7.php";
                        } else {
                            alert(result.message);
                        }
                    },
                    error: function(error) {}
                });
            });
        });
    </script>
</body>

</html>