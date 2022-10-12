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
                <div class=" mb-4 hide" id="form-cost-display">
                    <p style="line-height: normal !important;">
                        <b><span id="form-type"></span></b> forms cost <b> GHS<span id="form-cost"></span></b>.
                    </p>
                </div>
                <button class="btn btn-primary" type="submit" style="padding: 10px 10px; width:100%" disabled>Pay</button>
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
                        if (result.success) {
                            //window.location.href = result.message;
                        } else {
                            alert(result.message)
                        }
                    },
                    error: function(error) {}
                });
            });

            $(".form-select").change("blur", function() {
                $.ajax({
                    type: "GET",
                    url: "endpoint/formInfo",
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