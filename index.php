<?php
session_start();

if (!isset($_SESSION["_step1Token"])) {
    $rstrong = true;
    $_SESSION["_step1Token"] = hash('sha256', bin2hex(openssl_random_pseudo_bytes(64, $rstrong)));
}

?>
<!DOCTYPE html>
<html lang="en">

<?php require_once("inc/head-section.php"); ?>

<body class="fluid-container">
    <div class="flex">
        <div class="form_card card">
            <img src="assets/images/RMU-LOG.png" alt="RMU LOG">
            <h1 style="text-align: center">Step 1</h1>
            <form id="step1Form" method="post" enctype="multipart/form-data">
                <div class="mb-4">
                    <label class="form-label" for="first_name">First Name</label>
                    <input title="Provide your first name" class="form-control" type="text" name="first_name" id="first_name" placeholder="Type your first name" required>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="last_name">Last Name</label>
                    <input title="Provide your last name" class="form-control" type="text" name="last_name" id="last_name" placeholder="Type your last name" required>
                </div>
                <button class="btn btn-primary" type="submit" style="padding: 5px 10px; width:100%">Continue</button>
                <input type="hidden" name="_v1Token" value="<?= $_SESSION["_step1Token"]; ?>">
            </form>
        </div>
    </div>

    <script src="js/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {

            $("#step1Form").on("submit", function(e) {
                e.preventDefault();
                //window.location.href = "purchase_step2.php";
                $.ajax({
                    type: "POST",
                    url: "endpoint/verifyStep1",
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(result) {
                        console.log(result);
                        if (result.success) {
                            window.location.href = 'step2.php';
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
                    error: function(error) {
                        console.log(result);
                    }
                });
            });
        });
    </script>
</body>

</html>