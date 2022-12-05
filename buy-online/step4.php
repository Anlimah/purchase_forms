<?php
session_start();
if (isset($_SESSION['step3Done']) && $_SESSION['step3Done'] == true && isset($_SESSION["vendor_id"]) && !empty($_SESSION["vendor_id"]) && $_SESSION["vendor_type"] == "ONLINE") {
    if (!isset($_SESSION["_step4Token"])) {
        $rstrong = true;
        $_SESSION["_step4Token"] = hash('sha256', bin2hex(openssl_random_pseudo_bytes(64, $rstrong)));
    }
} else {
    header('Location: step3.php');
}

?>
<?php
require_once('../inc/page-data.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("../inc/head-section.php"); ?>
    <title>Form Purchase | Step 4</title>
</head>

<body class="fluid-container">
    <div class="flex">
        <div class="form_card card" style="height: 500px !important;">
            <img src="../assets/images/RMU-LOG.png" alt="RMU LOG">
            <h1 style="text-align: center; color: #003262 !important; font-size:30px !important">Step 4</h1>
            <form action="#" id="step1Form" method="post" enctype="multipart/form-data" style="margin: 0px 12%;">
                <div class="mb-4">
                    <p class="mb-4" style="color:#003262;">
                        Provide your <b>number</b>. We'll send you an OTP message with a code for verification on the next screen.<br>
                        <span class="text-danger"><b>Note:</b> We don't accept VoIP or Skype numbers.</span>
                    </p>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="phone-number">Phone Number</label>
                    <div style="display:flex !important; flex-direction:row !important; justify-content: space-between !important">
                        <select title="Choose country and country code" class="form-select form-select-sm country-code" name="country" id="country" style="margin-right: 10px; width: 45%" required>
                            <option selected disabled value="">Choose...</option>
                            <?php
                            foreach (COUNTRIES as $cn) {
                                echo '<option value="(' . $cn["code"] . ') ' . $cn["name"] . '">(' . $cn["code"] . ') ' . $cn["name"] . '</option>';
                            }
                            ?>
                        </select>
                        <input maxlength="10" title="Provide your Provide Number" class="form-control form-control-sm" style="width: 70%" type="tel" name="phone_number" id="phone_number" placeholder="0244123123" required>
                    </div>
                </div>
                <button class="btn btn-primary" type="submit" id="submitBtn" style="padding: 10px 10px; width:100%">Continue</button>
                <input class="form-control" type="hidden" name="_v4Token" value="<?= $_SESSION["_step4Token"]; ?>">
            </form>
        </div>
    </div>

    <script src="../js/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#step1Form").on("submit", function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: "../endpoint/verifyStep4",
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(result) {
                        console.log(result);
                        if (result.success) {
                            window.location.href = 'step5.php';
                        } else {
                            alert(result.message);
                        }
                        /*if (res["response"] == "success") {
                            console.log(res['msg']);
                            window.location.href = 'verify-code.php'
                        } else {
                            console.log(res['msg']);
                        }*/
                    },
                    error: function(error) {}
                });
            });

            $(document).on({
                ajaxStart: function() {
                    $("#submitBtn").prop("disabled", true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');
                },
                ajaxStop: function() {
                    $("#submitBtn").prop("disabled", false).html('Continue');
                }
            });

            $("#phone_number").focus();
        });
    </script>
</body>

</html>