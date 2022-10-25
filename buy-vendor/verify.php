<?php
session_start();
echo $_SESSION["sms_code"];
if (isset($_SESSION['verifySMSCode']) && $_SESSION['verifySMSCode'] == true) {
    if (!isset($_SESSION["_verifySMSToken"])) {
        $rstrong = true;
        $_SESSION["_verifySMSToken"] = hash('sha256', bin2hex(openssl_random_pseudo_bytes(64, $rstrong)));
    }
} else {
    header('Location: index.php');
}

?>
<!DOCTYPE html>
<html lang="en">

<?php require_once("../inc/head-section.php"); ?>

<body class="fluid-container">
    <div class="flex">
        <div class="form_card card">
            <img src="../assets/images/RMU-LOG.png" alt="RMU LOG">
            <h1 style="text-align: center; color: #003262 !important; font-size:30px !important">Verify SMS Code</h1>
            <form action="#" id="step1Form" method="post" enctype="multipart/form-data" style="margin: 0px 12%;">
                <p class="mb-4">Enter the verification code we sent to your phone.</p>
                <div class="mb-4" style="display:flex !important; flex-direction:row !important; justify-content: space-around !important; align-items:baseline">
                    <label class="form-label" for="email_addr">RMU - </label>
                    <input class="form-control num" type="text" maxlength="1" style="width:50px; text-align:center" name="code[]" id="num1" placeholder="0" required>
                    <input class="form-control num" type="text" maxlength="1" style="width:50px; text-align:center" name="code[]" id="num2" placeholder="0" required>
                    <input class="form-control num" type="text" maxlength="1" style="width:50px; text-align:center" name="code[]" id="num3" placeholder="0" required>
                    <input class="form-control num" type="text" maxlength="1" style="width:50px; text-align:center" name="code[]" id="num4" placeholder="0" required>
                </div>
                <button class="btn btn-primary mb-4" type="submit" id="submitBtn" style="padding: 10px 10px; width:100%">
                    Verify
                </button>
                <input class="form-control" type="hidden" name="_vSMSToken" value="<?= $_SESSION["_verifySMSToken"]; ?>">
                <a href="step4.php">Change number</a>
            </form>
        </div>
    </div>

    <script src="../js/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            //get variable(parameters) from url
            function getUrlVars() {
                var vars = {};
                var parts = window.location.href.replace(
                    /[?&]+([^=&]+)=([^&]*)/gi,
                    function(m, key, value) {
                        vars[key] = value;
                    }
                );
                return vars;
            }

            $("#step1Form").on("submit", function(e) {
                e.preventDefault();
                var url = "";
                if (getUrlVars()["verify"] == "vendor") {
                    url = "verifyVendor";
                } else if (getUrlVars()["verify"] == "customer") {
                    url = "verifyCustomer";
                } else {
                    return;
                }

                $.ajax({
                    type: "POST",
                    url: "../endpoint/" + url,
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(result) {
                        console.log(result);
                        if (result.success) {
                            if (url == "verifyVendor")
                                window.location.href = "./";
                            else
                                window.location.href = "confirm.php?status=000&exttrid=" + result.exttrid;
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