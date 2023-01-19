<?php
session_start();
//echo $_SERVER["HTTP_USER_AGENT"];
if (!isset($_SESSION["_loginToken"])) {
    $rstrong = true;
    $_SESSION["_loginToken"] = hash('sha256', bin2hex(openssl_random_pseudo_bytes(64, $rstrong)));
    $_SESSION["vendor_type"] = "VENDOR";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("../inc/head-section.php"); ?>
    <title>Form Purchase | Vendor Login</title>
</head>

<body class="fluid-container">

    <div id="wrapper">

        <?php require_once("../inc/page-nav.php"); ?>

        <main class="container flex-container">
            <div class="flex-card">
                <div class="form-card card">
                    <div class="purchase-card-header">
                        <h1>Vendor Portal</h1>
                    </div>

                    <div class="purchase-card-step-info">
                        <span class="step-capsule">Login</span>
                    </div>

                    <hr style="color:#999">

                    <div class="purchase-card-body">
                        <form id="loginForm" method="post" enctype="multipart/form-data">
                            <div id="liveAlertPlaceholder"></div>
                            <div class="mt-4 mb-4">
                                <label class="form-label" for="username" style="font-size:16px !important">Username</label>
                                <input title="Provide your Username" class="form-control" style="font-size:16px !important;" type="text" name="username" id="username" placeholder="Enter your Username" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label" for="password" style="font-size:16px !important">Password</label>
                                <input title="Provide your Password" class="form-control" style="font-size:16px !important;" type="password" name="password" id="password" placeholder="Enter your Password" required>
                            </div>
                            <button class="btn btn-primary" type="submit" id="submitBtn" style="font-size:16px !important; width:100%;font-size:16px !important">Login</button>
                            <input type="hidden" name="_vlToken" value="<?= $_SESSION["_loginToken"]; ?>">
                        </form>
                    </div>
                </div>
            </div>
        </main>

        <?php require_once("../inc/page-footer.php"); ?>
    </div>

    <script src="../js/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {

            const alertPlaceholder = document.getElementById('liveAlertPlaceholder')

            const alerter = (message, type) => {
                const wrapper = document.createElement('div')
                wrapper.innerHTML = [
                    `<div class="alert alert-${type} alert-dismissible" role="alert"  style="display:flex !important; flex-direction:row !important; justify-content:space-between !important; align-items:baseline important; font-weight:600; padding: 8px 15px;">`,
                    `   <span>${message}</span>`,
                    '   <i type="button" style="color: #555;" data-bs-dismiss="alert" aria-label="Close">X</i>',
                    '</div>'
                ].join('')

                alertPlaceholder.append(wrapper);
            }

            $("#loginForm").on("submit", function(e) {
                e.preventDefault();
                //window.location.href = "purchase_step2.php";
                $.ajax({
                    type: "POST",
                    url: "../endpoint/loginVendor",
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(result) {

                        console.log(result);
                        if (result.success) {
                            alerter(result.message, 'success');
                            window.location.href = "verify.php?verify=vendor";
                        } else {
                            alerter(result.message, 'danger');
                        }
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });

            $(document).on({
                ajaxStart: function() {
                    $("#submitBtn").prop("disabled", true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');
                },
                ajaxStop: function() {
                    $("#submitBtn").prop("disabled", false).html('Login');
                }
            });
        });
    </script>
</body>

</html>