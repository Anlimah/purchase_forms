<?php
session_start();

if (!isset($_GET['status']) || !isset($_GET['transaction_id'])) header('Location: index.php?status=1');
if (isset($_GET['status']) && empty($_GET['status'])) header('Location: index.php?status=2');
if (isset($_GET['transaction_id']) && empty($_GET['transaction_id'])) header('Location: index.php?status=3');
//OrchardPaymentGateway::destroyAllSessions(); //Kill all sessions

?>
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
                //window.location.href = "purchase_step2.php";
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
                            alert(result.message);
                        } else {
                            alert(result.message);
                        }
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            }
        }


    });
</script>