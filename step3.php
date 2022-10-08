<?php
session_start();
if (isset($_SESSION['step2Done']) && $_SESSION['step2Done'] == true) {
    if (!isset($_SESSION["_step3Token"])) {
        $rstrong = true;
        $_SESSION["_step3Token"] = hash('sha256', bin2hex(openssl_random_pseudo_bytes(64, $rstrong)));
    }
} else {
    header('Location: step2.php');
}

?>
<!DOCTYPE html>
<html lang="en">

<?php require_once("inc/head-section.php"); ?>

<body class="fluid-container">
    <div class="flex">
        <div class="form_card card">
            <img src="assets/images/RMU-LOG.png" alt="RMU LOG">
            <h1 style="text-align: center">Step 3</h1>
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
                <button class="btn btn-primary mb-4" type="submit" style="padding: 10px 10px; width:100%">Continue</button>
                <input type="hidden" name="_v3Token" value="<?= $_SESSION["_step3Token"]; ?>">
                <a href="step2.php">Change email address</a>
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
                    url: "endpoint/verifyStep3",
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(result) {
                        console.log(result);
                        if (result) {
                            window.location.href = 'step4.php';
                        } else {
                            alert(result.message);
                        }
                    },
                    error: function(error) {}
                });
            });

            $("#num1").focus();

            $(".num").on("keyup", function() {
                if (this.value) {
                    $(this).next(":input").focus();
                }
            });
        });
    </script>
</body>

</html>