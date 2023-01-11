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
                    <h1>Verify Phone Number</h1>
                </div>

                <div class="purchase-card-step-info">
                    <span class="step-capsule">Step 5 of 6</span>
                </div>

                <hr style="color:#999">

                <div class="purchase-card-body">
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
        </div>
    </main>

    <footer class="fp-footer container" style="text-align: center;line-height: 1.2;">
        <span>For more information and support</span>
        <div style="font-size: 12px;">
            <span><i class="bi bi-telephone-fill" style="color:#003262"></i> (+233) 302 712775; 718225; 714070</span> |
            <span><i class="bi bi-envelope-fill" style="color:#003262"></i> admissions@rmu.edu.gh</span>
        </div>
    </footer>

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