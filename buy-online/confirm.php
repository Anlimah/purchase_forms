<?php
session_start();
session_unset();
session_destroy();

if (!isset($_GET['status']) || !isset($_GET['exttrid'])) header('Location: index.php?status=invalid');
if (isset($_GET['status']) && empty($_GET['status'])) header('Location: index.php?status=invalid');
if (isset($_GET['exttrid']) && empty($_GET['exttrid'])) header('Location: index.php?status=invalid');/**/

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("../inc/head-section.php"); ?>
    <title>Form Purchase | Confirm Payment</title>
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
                        <h1>Payment Status Confirmation</h1>
                    </div>

                    <div class="purchase-card-step-info">
                        <span class="step-capsule">Step Final</span>
                    </div>

                    <hr style="color:#999">

                    <div class="purchase-card-body">
                        <div class="pay-status" style="margin: 0px 10%;" style="align-items: baseline;">
                            <div class="d-flex justify-content-center">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p style="margin-left: 10px; margin-top:3px" id="status-out"> Connecting...</p>
                            </div>
                        </div>
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

            //Use a default value when param is missing
            function getUrlParam(parameter, defaultvalue) {
                var urlparameter = defaultvalue;
                if (window.location.href.indexOf(parameter) > -1) {
                    urlparameter = getUrlVars()[parameter];
                }
                return urlparameter;
            }

            if (getUrlVars()["status"] != "" || getUrlVars()["status"] != undefined) {
                if (getUrlVars()["exttrid"] != "" || getUrlVars()["exttrid"] != undefined) {
                    let connect = 15000;
                    let init = 15000;
                    setTimeout(function() {
                        $("#status-out").text("Initializing...");
                        setTimeout(function() {
                            $.ajax({
                                type: "POST",
                                url: "../endpoint/confirm",
                                data: {
                                    status: getUrlVars()["status"],
                                    exttrid: getUrlVars()["exttrid"],
                                },
                                success: function(result) {
                                    console.log(result);
                                    if (result.success) $(".pay-status").html("").append(result.message);
                                    else $(".pay-status").html("").append(result.message + '<br><div><a href="/">Try again</a></div>');
                                },
                                error: function(error) {
                                    console.log(error);
                                }
                            });
                        }, init);

                    }, connect);
                }
            }

            $(document).on({
                ajaxStart: function() {
                    //$(".pay-status").removeClass("hide");
                    $("#status-out").text("Processing...");
                },
                ajaxStop: function() {
                    $(".pay-status").removeClass("hide");
                }
            });

        });
    </script>

</body>

</html>