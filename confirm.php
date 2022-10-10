<?php
session_start();

if (!isset($_GET['status']) || !isset($_GET['transaction_id'])) header('Location: index.php?status=1');
if (isset($_GET['status']) && empty($_GET['status'])) header('Location: index.php?status=2');
if (isset($_GET['transaction_id']) && empty($_GET['transaction_id'])) header('Location: index.php?status=3');

?>

<!DOCTYPE html>
<html lang="en">

<?php require_once("inc/head-section.php"); ?>

<body class="fluid-container">
    <div class="flex">
        <div class="form_card card" style="height: 320px !important;">
            <img src="assets/images/RMU-LOG.png" alt="RMU LOG">
            <h1 style="text-align: center; color: #003262 !important; font-size:24px !important">Payment Confirmation</h1>
            <div class="d-flex justify-content-center" style="margin: 0px 10%;">
                <div class="pay-status" style="align-items: baseline;">
                    <div class="spinner-grow" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <p style="margin-left: 10px; margin-top:3px"> Processing payment...</p>
            </div>
        </div>
    </div>

    <script src="js/jquery-3.6.0.min.js"></script>
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
                if (getUrlVars()["transaction_id"] != "" || getUrlVars()["transaction_id"] != undefined) {
                    let delay = 1000 * 15;
                    setTimeout(function() {
                        $.ajax({
                            type: "POST",
                            url: "endpoint/confirm",
                            data: {
                                status: getUrlVars()["status"],
                                transaction_id: getUrlVars()["transaction_id"],
                            },
                            success: function(result) {
                                console.log(result);
                                if (result.success) {
                                    $(".pay-status").html("").append(
                                        '<p class="mb-4"><b style="color: #003262">' + result.message + '</b><br>' +
                                        '<span style="color:red;"><b>Please do not close this page yet.</b></span><br><br>' +
                                        'An email and SMS with your <b>Application Number</b> and <b>PIN</b> to access application portal, has been sent to you!<br>' +
                                        'Please confirm and proceed to the <a href="../apply"><b>online applicatioin portal</b></a> to complete your application process.</p>' +
                                        '<form id="step1Form" method="post" enctype="multipart/form-data">' +
                                        '<div class="mb-4">' +
                                        '<button class="btn btn-primary" type="submit" style="padding: 10px 10px; width:100%">Continue</button>' +
                                        '<input type="hidden" name="_v1Token" value="' + getUrlVars()["transaction_id"] + '">' +
                                        '</form>'
                                    );
                                    $(".d-flex").html("").append(result.message + '<div><a href="/">Try again</a></div>');
                                } else {
                                    $(".d-flex").html("").append(result.message + '<div><a href="/">Try again</a></div>');
                                }
                            },
                            error: function(error) {
                                console.log(error);
                            }
                        });
                    }, delay);
                }
            }

            $(document).on({
                ajaxStart: function() {
                    $(".d-flex").removeClass("hide");
                },
                ajaxStop: function() {
                    $(".d-flex").removeClass("hide");
                }
            });

        });
    </script>

</body>

</html>