<?php
session_start();
//echo $_SERVER["HTTP_USER_AGENT"];
if (isset($_SESSION["loginSuccess"]) && $_SESSION["loginSuccess"] == true && isset($_SESSION["vendor_id"]) && !empty($_SESSION["vendor_id"])) {
    if (!isset($_SESSION["_vendor1Token"])) {
        $rstrong = true;
        $_SESSION["_vendor1Token"] = hash('sha256', bin2hex(openssl_random_pseudo_bytes(64, $rstrong)));
        $_SESSION["vendor_type"] = "VENDOR";
    }
} else {
    header("Location: login.php");
}

if (isset($_GET['logout'])) {
    session_destroy();
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    header('Location: login.php');
}

require_once('../bootstrap.php');

use Src\Controller\ExposeDataController;

$expose = new ExposeDataController();
?>
<?php
require_once('../inc/page-data.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("../inc/head-section.php"); ?>
    <title>Form Purchase | Vendor</title>
</head>

<body class="fluid-container">

    <div id="wrapper">

        <?php require_once("../inc/page-nav.php"); ?>

        <main class="container flex-container mb-4">
            <div class="flex-card">
                <div class="form-card card" style="max-width: 700px !important;">

                    <div class="purchase-card-header">
                        <h1>Vendor Portal</h1>
                    </div>

                    <div class="purchase-card-step-info">
                        <span class="step-capsule">RMU Forms Purchase Portal</span>
                    </div>

                    <hr style="color:#999">

                    <div class="purchase-card-body">
                        <form id="step1Form" method="post" enctype="multipart/form-data">
                            <div class="flex-column align-items-center">
                                <div class="flex-row justify-space-between">
                                    <div>
                                        <div class="mb-4">
                                            <label class="form-label" for="first_name">First Name</label>
                                            <input name="first_name" id="first_name" title="Provide your first name" class="form-control" type="text" placeholder="Type your first name" required>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label" for="last_name">Last Name</label>
                                            <input name="last_name" id="last_name" title="Provide your last name" class="form-control" type="text" placeholder="Type your last name" required>
                                        </div>
                                    </div>
                                    <div>
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
                                        <div class="mb-4">
                                            <div style="display:flex !important; flex-direction:row !important; justify-content: flex-start !important;">
                                                <label class="form-label" for="country" style="margin-right: 10px; width: 45%">Country Code</label>
                                                <label class="form-label" style="float:left" for="phone-number">Phone Number</label>
                                            </div>
                                            <div style="display:flex !important; flex-direction:row !important; justify-content: space-between !important">
                                                <input name="country" id="country" value="<?= '(' . COUNTRIES[83]["code"] . ') ' . COUNTRIES[83]["name"]  ?>" title="Choose country and country code" class="form-control form-control-sm" list="address-country-list" style="margin-right: 10px; width: 60%" placeholder="Type for options" required>
                                                <datalist id="address-country-list">
                                                    <?php
                                                    foreach (COUNTRIES as $cn) {
                                                        echo '<option value="(' . $cn["code"] . ') ' . $cn["name"] . '">(' . $cn["code"] . ') ' . $cn["name"] . '</option>';
                                                    }
                                                    ?>
                                                </datalist>
                                                <input name="phone_number" id="phone_number" maxlength="10" title="Provide your Provide Number" class="form-control form-control-sm" style="width: 70%" type="tel" placeholder="0244123123" required>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div>
                                    <button class="btn btn-primary" type="submit" id="submitBtn" style="padding: 10px 10px; width:100%">Submit</button>
                                    <input type="hidden" name="_v1Token" value="<?= $_SESSION["_vendor1Token"]; ?>">
                                </div>
                            </div>
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

            $("#step1Form").on("submit", function(e) {
                let answer = confirm("Has your customer made payment? \nIf not, please make sure you have collect your money before proceeding. \nClick OK to continue or Cancel to abort process");
                if (answer == true) {
                    //window.location.href = "purchase_step2.php";
                    $.ajax({
                        type: "POST",
                        url: "../endpoint/vendor",
                        data: new FormData(this),
                        contentType: false,
                        cache: false,
                        processData: false,
                        success: function(result) {
                            console.log(result);
                            if (result.success) {
                                window.location.href = "verify.php?verify=customer";
                            } else {
                                alert(result.message);
                            }
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });
                }
                e.preventDefault();
            });

            $(document).on({
                ajaxStart: function() {
                    $("#submitBtn").prop("disabled", true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');
                },
                ajaxStop: function() {
                    $("#submitBtn").prop("disabled", false).html('Submit');
                }
            });

            $("input[type='text']").on("click", function() {
                if (this.value) {
                    $(this).focus().select(); //.val(''); and as well clesr
                }
            });
        });
    </script>
</body>

</html>