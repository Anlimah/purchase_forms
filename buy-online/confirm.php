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
    <div class="flex">
        <div class="form_card card" style="height: 350px !important;">
            <img src="../assets/images/RMU-LOG.png" alt="RMU LOG">
            <h1 style="text-align: center; color: #003262 !important; font-size:24px !important">Payment Confirmation</h1>
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
                                    if (result.success) {
                                        $(".pay-status").html("").append(
                                            '<p class="mb-4"><b style="color: #003262">' + result.message + '</b><br>' +
                                            'An email and SMS with your <b>Application Number</b> and <b>PIN</b> to access application portal, has been sent to you!<br>' +
                                            'Please confirm and proceed to the <a href="https://admissions.rmuictonline.com/apply/"> <b>online application portal</b></a> to complete your application process.</p>' +
                                            '<!--<form action="endpoint/sms" method="post" enctype="multipart/form-data" style="display: flex;flex-direction:row;justify-content:space-between">' +
                                            '<button class="btn btn-primary" type="submit" style="padding: 10px 10px; width:100%">Resend SMS</button>' +
                                            '<input type="hidden" name="_v1Token" value="' + getUrlVars()["exttrid"] + '">' +
                                            '</form>-->'
                                        );
                                        //$(".pay-status").html("").append(result.message + '<br><div><a href="/">Try again</a></div>');
                                    } else {
                                        $(".pay-status").html("").append(result.message + '<br><div><a href="/">Try again</a></div>');
                                    }
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