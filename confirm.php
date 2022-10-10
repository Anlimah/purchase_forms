<?php
session_start();

if ((isset($_GET['status']) && !empty($_GET['status'])) || !isset($_GET['status'])) header('Location: index.php?status=cancelled');
if ((isset($_GET['transaction_id']) && !empty($_GET['transaction_id']) || !isset($_GET['transaction_id']))) header('Location: index.php?status=cancelled');

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

        alert(getUrlVars()["status"]);
    });
</script>