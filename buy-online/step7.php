<?php
session_start();
if (isset($_SESSION['step6Done']) && $_SESSION['step6Done'] == true && isset($_SESSION["vendor_id"]) && !empty($_SESSION["vendor_id"]) && $_SESSION["vendor_type"] == "ONLINE") {
    if (!isset($_SESSION["_step7MomoToken"])) {
        $rstrong = true;
        $_SESSION["_step7MomoToken"] = hash('sha256', bin2hex(openssl_random_pseudo_bytes(64, $rstrong)));
    }
} else {
    header('Location: step6.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<?php require_once("../inc/head-section.php"); ?>

<body class="fluid-container">
    <div class="flex">
        <div class="form_card card" style="height: 510px !important;">
            <img src="../assets/images/RMU-LOG.png" alt="RMU LOG">
            <h1 style="text-align: center; color: #003262 !important; font-size:30px !important">Step 7</h1>
            <form id="step7MoMoForm" method="post" enctype="multipart/form-data" style="margin: 0px 12%;">
                <p style="line-height: normal !important;">
                    <b><span><?= $_SESSION["step6"]["form_type"] ?></span></b> forms cost <b> GHS<span><?= $_SESSION["step6"]["amount"] ?></span></b>. <br>
                    <span>Make sure you have enough fund in you MoMo account.</span>
                </p>
                <p class="mb-4">
                    Specify your mobile money number details.
                </p>
                <div class="mb-4" style="display:flex !important; flex-direction:row !important; justify-content: space-between !important; align-items:baseline">
                    <label class="form-label" for="momo_agent" style="margin-right: 10px;width:100%">Network</label>
                    <select title="Select your phone number network." class="form-select form-select-sm" name="momo_agent" id="momo_agent">
                        <option value="AIR">AIRTEL</option>
                        <option value="MTN" selected>MTN</option>
                        <option value="TIG">TIGO</option>
                        <option value="VOD">VODAFONE</option>
                    </select>
                </div>
                <div class="mb-4" style="display:flex !important; flex-direction:row !important; justify-content: space-between !important; align-items:baseline !important">
                    <label class="form-label" for="momo_agent" style="margin-right: 10px;width:100%">MoMo Number</label>
                    <input class="form-control" type="tel" name="momo_number" id="momo_number" value="<?= $_SESSION['step4']['phone_number'] ?>" maxlength="10">
                </div>
                <button class="btn btn-primary" type="submit" id="submitBtn" style="padding: 10px 10px; width:100%">Pay</button>
                <input class="form-control" type="hidden" name="_v7MomoToken" value="<?php echo $_SESSION["_step7MomoToken"]; ?>">
                <!--<input class="form-control" type="hidden" name="country" value="GH">-->
            </form>
        </div>
    </div>

    <script src="../js/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#step7MoMoForm").on("submit", function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: "../endpoint/verifyStep7Momo",
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(result) {
                        console.log(result);
                        if (result.success) {
                            //window.location.href = "confirm.php" + result.message;
                            window.location.href = result.message;
                        } else {
                            alert(result.message)
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