<?php
session_start();
if (isset($_SESSION['step4Done']) && $_SESSION['step4Done'] == true && isset($_SESSION["vendor_id"]) && !empty($_SESSION["vendor_id"]) && $_SESSION["vendor_type"] == "ONLINE") {
    if (!isset($_SESSION["_step5Token"])) {
        $rstrong = true;
        $_SESSION["_step5Token"] = hash('sha256', bin2hex(openssl_random_pseudo_bytes(64, $rstrong)));
    }
} else {
    header('Location: step4.php');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("../inc/head-section.php"); ?>
    <title>Form Purchase | Step 5</title>
</head>

<body class="fluid-container">
    <div class="flex">
        <div class="form_card card">
            <img src="../assets/images/RMU-LOG.png" alt="RMU LOG">
            <h1 style="text-align: center; color: #003262 !important; font-size:30px !important">Step 5</h1>
            <form action="#" id="step1Form" method="post" enctype="multipart/form-data" style="margin: 0px 12%;">
                <p class="mb-4">Enter the verification code we sent to your phone.</p>
                <div class="mb-4" style="display:flex !important; flex-direction:row !important; justify-content: space-around !important; align-items:baseline">
                    <label class="form-label" for="email_addr">RMU - </label>
                    <input class="form-control num" type="text" maxlength="1" style="width:50px; text-align:center" name="code[]" id="num1" placeholder="0" required>
                    <input class="form-control num" type="text" maxlength="1" style="width:50px; text-align:center" name="code[]" id="num2" placeholder="0" required>
                    <input class="form-control num" type="text" maxlength="1" style="width:50px; text-align:center" name="code[]" id="num3" placeholder="0" required>
                    <input class="form-control num" type="text" maxlength="1" style="width:50px; text-align:center" name="code[]" id="num4" placeholder="0" required>
                </div>
                <button class="btn btn-primary mb-4" type="submit" id="submitBtn" style="padding: 10px 10px; width:100%">Verify</button>
                <input class="form-control" type="hidden" name="_v5Token" value="<?= $_SESSION["_step5Token"]; ?>">
                <a href="step4.php">Change number</a>
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
                    url: "../endpoint/verifyStep5",
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(result) {
                        console.log(result);
                        if (result.success) {
                            window.location.href = 'step6.php';
                        } else {
                            alert(result.message);
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
                    $("#submitBtn").prop("disabled", false).html('Verify');
                }
            });

            $("#num1").focus();

            $(".num").on("keyup", function() {
                if (this.value) {
                    $(this).next(":input").focus().select(); //.val(''); and as well clesr
                }
            });

            $("input[type='text']").on("click", function() {
                $(this).select();
            });
        });
    </script>
</body>

</html>