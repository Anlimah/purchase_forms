<?php

use Src\Controller\ExposeDataController;

require_once('../bootstrap.php');
$expose = new ExposeDataController();

session_start();
if (isset($_SESSION['step5Done']) && $_SESSION['step5Done'] == true && isset($_SESSION["vendor_id"]) && !empty($_SESSION["vendor_id"]) && $_SESSION["vendor_type"] == "ONLINE") {
    if (!isset($_SESSION["_step6Token"])) {
        $rstrong = true;
        $_SESSION["_step6Token"] = hash('sha256', bin2hex(openssl_random_pseudo_bytes(64, $rstrong)));
        $_SESSION["ip"] = $expose->getIPAddress();
        $_SESSION["device"] = $expose->getDeciveInfo();
    }
} else {
    header('Location: step5.php');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("../inc/head-section.php"); ?>
    <title>Form Purchase | Step 6</title>
</head>

<body class="fluid-container">

    <div id="wrapper">

        <header class="fp-header">
            <div class="container">
                <div class="items">
                    <img src="../assets/images/rmu-logo-small.png" style="width: 70px;">
                    <span class="rmu-logo-letter">RMU</span>
                </div>
            </div>
        </header>

        <main class="container flex-container" style="margin-bottom: 100px;">
            <div class="flex-card">
                <div class="form-card card">
                    <div class="purchase-card-header">
                        <h1>Form and Payment Options</h1>
                    </div>

                    <div class="purchase-card-step-info">
                        <span class="step-capsule">Step 6 of 6</span>
                    </div>

                    <hr style="color:#999">

                    <div class="purchase-card-body">
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
                            <div class="mb-4 hide" id="form-cost-display">
                                <p style="line-height: normal !important;">
                                    <b><span id="form-type"></span></b> forms cost <b> GHS<span id="form-cost"></span></b>.
                                </p>
                                <p class="mb-4">
                                    Choose your payment method.
                                </p>
                                <div class="mb-4" style="display:flex !important; flex-direction:row !important; justify-content: space-between !important; align-items:baseline">
                                    <label class="form-label" for="payment_method" style="margin-right: 10px;width:50%">Method: </label>
                                    <select title="Select your phone number network." class="form-select form-select-sm" name="payment_method" id="payment_method" required>
                                        <option value="CRD">Card</option>
                                        <option value="MOM" selected>Mobile Money</option>
                                    </select>
                                </div>
                            </div>
                            <button class="btn btn-primary" type="submit" id="submitBtn" style="padding: 10px 10px; width:100%" disabled>Pay</button>
                            <input type="hidden" name="_v6Token" value="<?= $_SESSION["_step6Token"]; ?>">
                        </form>
                    </div>
                </div>
            </div>
        </main>

        <footer class="fp-footer fluid-container text-bg-secondary" style="text-align: center;line-height: 1.2;">
            <span>For more information and support</span>
            <div style="font-size: 12px;">
                <span><i class="bi bi-telephone-fill" style="color:#003262"></i> (+233) 302 712775; 718225; 714070</span> |
                <span><i class="bi bi-envelope-fill" style="color:#003262"></i> admissions@rmu.edu.gh</span>
            </div>
        </footer>
    </div>

    <script src="../js/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#step1Form").on("submit", function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: "../endpoint/verifyStep6",
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(result) {
                        console.log(result);
                        if (result.success) {
                            window.location.href = result.message;
                        } else {
                            alert(result.message)
                        }
                    },
                    error: function(error) {}
                });
            });

            $(document).on({
                ajaxStart: function() {
                    $("#submitBtn").prop("disabled", true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');
                },
                ajaxStop: function() {
                    $("#submitBtn").prop("disabled", false).html('Pay');
                }
            });

            $(".form-select").change("blur", function() {
                $.ajax({
                    type: "GET",
                    url: "../endpoint/formInfo",
                    data: {
                        form_type: this.value,
                    },
                    success: function(result) {
                        console.log(result);
                        if (result.success) {
                            $("#form-cost-display").removeClass("hide");
                            $("#form-type").text($("#form_type").val());
                            $("#form-cost").text(result.message);
                            $(':input[type="submit"]').prop('disabled', false);
                        }
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });
        });
    </script>
</body>

</html>