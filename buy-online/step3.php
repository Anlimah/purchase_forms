<?php
session_start();
//if (isset($_SESSION['step2Done']) && $_SESSION['step2Done'] == true && isset($_SESSION["vendor_id"]) && !empty($_SESSION["vendor_id"]) && $_SESSION["vendor_type"] == "ONLINE") {
if (!isset($_SESSION["_step3Token"])) {
    $rstrong = true;
    $_SESSION["_step3Token"] = hash('sha256', bin2hex(openssl_random_pseudo_bytes(64, $rstrong)));
}
/*} else {
    header('Location: step2.php');
}*/

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("../inc/head-section.php"); ?>
    <title>Form Purchase | Step 3</title>
</head>

<body class="fluid-container">

    <div id="wrapper">

        <?php require_once("../inc/page-nav.php"); ?>

        <main class="container flex-container">
            <div class="flex-card">
                <div class="form-card card">
                    <div class="purchase-card-header">
                        <h1>Verify Email Address</h1>
                    </div>

                    <div class="purchase-card-step-info">
                        <span class="step-capsule">Step 3 of 6</span>
                    </div>

                    <hr style="color:#999">

                    <div class="purchase-card-body">
                        <form action="#" id="step1Form" method="post" enctype="multipart/form-data" style="margin: 0px 12%;">
                            <p class="mb-4" style="color:#003262;">
                                A 6 digit code has been sent to the email <?= $_SESSION["step2"]["email_address"] ?>. Enter the code
                            </p>
                            <div class="mb-4" style="width:100%; display: flex; flex-direction:row; align-items:baseline; justify-content:space-around">
                                <input class="form-control num" type="text" maxlength="1" style="width:35px; text-align:center;" name="num[]" id="num1" placeholder="0" required>
                                <input class="form-control num" type="text" maxlength="1" style="width:35px; text-align:center; margin-left:5px" name="num[]" id="num2" placeholder="0" required>
                                <input class="form-control num" type="text" maxlength="1" style="width:35px; text-align:center; margin-left:5px" name="num[]" id="num3" placeholder="0" required>
                                <input class="form-control num" type="text" maxlength="1" style="width:35px; text-align:center; margin-left:5px" name="num[]" id="num4" placeholder="0" required>
                                <input class="form-control num" type="text" maxlength="1" style="width:35px; text-align:center; margin-left:5px" name="num[]" id="num5" placeholder="0" required>
                                <input class="form-control num" type="text" maxlength="1" style="width:35px; text-align:center; margin-left:5px" name="num[]" id="num6" placeholder="0" required>
                            </div>
                            <button class="btn btn-primary mb-4" type="submit" id="submitBtn" style="padding: 10px 10px; width:100%">Verify</button>
                            <input type="hidden" name="_v3Token" value="<?= $_SESSION["_step3Token"]; ?>">
                        </form>
                    </div>

                    <div class="purchase-card-footer flex-row align-items-baseline justify-space-between" style="width: 100%;">
                        <a href="step2.php">Change email address</a>
                        <span id="timer"></span>
                        <button id="resend-code" class="btn btn-outline-dark btn-xs hide">Resend code</button>
                    </div>
                </div>
            </div>
        </main>

        <?php require_once("../inc/page-footer.php"); ?>
    </div>

    <script src="../js/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            var triggeredBy = 0;

            var count = 5;
            var intervalId = setInterval(() => {
                $("#timer").html("Resend code <b>(" + count + " sec)</b>");
                count = count - 1;
                if (count <= 0) {
                    clearInterval(intervalId);
                    $('#timer').hide();
                    $('#resend-code').removeClass("hide").addClass("display");
                    return;
                }
            }, 1000); //1000 will  run it every 1 second

            $("#resend-code").click(function() {
                triggeredBy = 1;

                $.ajax({
                    type: "POST",
                    url: "../endpoint/resend-code",
                    data: {
                        resend_code: "email"
                    },
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(result) {
                        console.log(result);


                        if (result.success) {
                            clearInterval(intervalId);
                            $("#timer").show();
                            $('#resend-code').removeClass("display").addClass("hide");

                            count = 5;
                            intervalId = setInterval(() => {
                                $("#timer").html("Resend code <b>(" + count + " sec)</b>");
                                count = count - 1;
                                if (count <= 0) {
                                    clearInterval(intervalId);
                                    $('#timer').hide();
                                    $('#resend-code').removeClass("hide").addClass("display").attr("disabled", false);
                                    return;
                                }
                            }, 1000); /**/
                        } else {
                            alert(result.message);
                        }
                    },
                    error: function(error) {}
                });
            })

            $("#step1Form").on("submit", function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: "../endpoint/verifyStep3",
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(result) {
                        console.log(result);
                        if (result.success) {
                            window.location.href = 'step4.php';
                        } else {
                            alert(result.message);
                        }
                    },
                    error: function(error) {}
                });
            });

            $(document).on({
                ajaxStart: function() {
                    if (triggeredBy == 1) $("#resend-code").prop("disabled", true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> sending...');
                    else $("#submitBtn").prop("disabled", true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');
                },
                ajaxStop: function() {
                    if (triggeredBy == 1) {
                        $("#resend-code").addClass("hide").html('Resend code');
                        $("#timer").show();
                    } else $("#submitBtn").prop("disabled", false).html('Verify');
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