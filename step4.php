<?php
session_start();
if (isset($_SESSION['step3Done']) && $_SESSION['step3Done'] == true) {
    if (!isset($_SESSION["_step4Token"])) {
        $rstrong = true;
        $_SESSION["_step4Token"] = hash('sha256', bin2hex(openssl_random_pseudo_bytes(64, $rstrong)));
    }
} else {
    header('Location: step3.php');
}

?>
<!DOCTYPE html>
<html lang="en">

<?php require_once("inc/head-section.php"); ?>

<body class="fluid-container">
    <div class="flex">
        <div class="form_card card" style="height: 500px !important;">
            <img src="assets/images/RMU-LOG.png" alt="RMU LOG">
            <h1 style="text-align: center">Step 4</h1>
            <form action="#" id="step1Form" method="post" enctype="multipart/form-data" style="margin: 0px 12%;">
                <div class="mb-4">
                    <p class="mb-4" style="color:#003262;">
                        For your security, We'll send you an OTP message with a code that you'll need to enter on the next screen.<br>
                        <span class="text-danger"><b>Note:</b> We don't accept VoIP or Skype numbers.</span>
                    </p>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="phone_number">Mobile Money Number</label>
                    <div style="display:flex !important; flex-direction:row !important; justify-content: space-between !important">
                        <select title="MoMo payment allowed for only Ghanaian applicants" class="form-select form-select-sm country-code" name="phone-number1-code" id="app-phone-number-code" style="margin-right: 10px; width: 45%">
                            <option value="233" selected>(+233) Ghana</option>
                        </select>
                        <input maxlength="10" title="Provide your Mobile Money Number" class="form-control form-control-sm" style="width: 70%" type="tel" name="phone_number" id="phone_number" placeholder="0244123123" required>
                    </div>
                </div>
                <button class="btn btn-primary" type="submit" style="padding: 10px 10px; width:100%">Verify</button>
                <input class="form-control" type="hidden" name="_v4Token" value="<?= $_SESSION["_step4Token"]; ?>">
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
                    url: "endpoint/verifyStep4",
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(result) {
                        console.log(result);
                        if (result) {
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

            $("#phone_number").focus();
        });
    </script>
</body>

</html>