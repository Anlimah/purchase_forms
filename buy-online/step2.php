<?php
session_start();
if (isset($_SESSION['step1Done']) && $_SESSION['step1Done'] == true && isset($_SESSION["vendor_id"]) && !empty($_SESSION["vendor_id"]) && $_SESSION["vendor_type"] == "ONLINE") {
    if (!isset($_SESSION["_step2Token"])) {
        $rstrong = true;
        $_SESSION["_step2Token"] = hash('sha256', bin2hex(openssl_random_pseudo_bytes(64, $rstrong)));
    }
} else {
    header('Location: index.php');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("../inc/head-section.php"); ?>
    <title>Form Purchase | Step 2</title>
</head>

<body class="fluid-container">

    <div id="wrapper">

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
                        <h1>Provide Email Address</h1>
                    </div>

                    <div class="purchase-card-step-info">
                        <span class="step-capsule">Step 2 of 6</span>
                    </div>

                    <hr style="color:#999">

                    <div class="purchase-card-body">
                        <form action="#" id="step1Form" method="post" enctype="multipart/form-data" style="margin: 0px 12%;">
                            <div class="mb-4">
                                <label class="form-label" for="email_addr">Email Address</label>
                                <input title="Provide your email address" class="form-control" type="email" name="email_address" id="email_address" placeholder="surname@gmail.com" required>
                            </div>
                            <button class="btn btn-primary" type="submit" id="submitBtn" style="padding: 10px 10px; width:100%">Continue</button>
                            <input class="form-control" type="hidden" name="_v2Token" value="<?= $_SESSION["_step2Token"]; ?>">
                        </form>
                    </div>
                </div>
            </div>
        </main>

        <footer class="fp-footer fluid-container text-bg-secondary" style="text-align: center;line-height: 1.2;">
            <span>For more information and support</span>
            <div style="font-size: 12px;">
                <span><i class="bi bi-telephone-fill" style="color:#003262"></i> (+233) 302 712775; 718225; 714070</span> |
                <span><i class="bi bi-envelope-fill" style="color:#003262"></i> admissions@rmu.edu.gh</span>
            </div>
        </footer>
    </div>

    <script src="../js/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#step1Form").on("submit", function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: "../endpoint/verifyStep2",
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(result) {
                        console.log(result);
                        if (result.success) {
                            window.location.href = 'step3.php';
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
        });
    </script>
</body>

</html>