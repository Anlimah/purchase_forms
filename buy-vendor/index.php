<?php
session_start();
//echo $_SERVER["HTTP_USER_AGENT"];
if (!isset($_SESSION["_step1Token"])) {
    $rstrong = true;
    $_SESSION["_step1Token"] = hash('sha256', bin2hex(openssl_random_pseudo_bytes(64, $rstrong)));
    $_SESSION["vendor_type"] = "VENDOR";
    $_SESSION["vendor_id"] = "1925166560534122";
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

<?php require_once("../inc/head-section.php"); ?>

<body class="fluid-container">
    <div class="flex">
        <div class="form_card card" style="height: 480px !important;">
            <!--<img src="../assets/images/RMU-LOG.png" alt="RMU LOG">-->
            <h1 style="text-align: center; color: #003262 !important; font-size:30px !important">RMU Online</h1>
            <form id="step1Form" method="post" enctype="multipart/form-data">
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
                    <label class="form-label" for="first_name">First Name</label>
                    <input title="Provide your first name" class="form-control" type="text" name="first_name" id="first_name" placeholder="Type your first name" required>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="last_name">Last Name</label>
                    <input title="Provide your last name" class="form-control" type="text" name="last_name" id="last_name" placeholder="Type your last name" required>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="phone-number">Phone Number</label>
                    <div style="display:flex !important; flex-direction:row !important; justify-content: space-between !important">
                        <select title="Choose country and country code" class="form-select form-select-sm country-code" name="country" id="country" style="margin-right: 10px; width: 45%" required>
                            <option selected disabled value="">Choose...</option>
                            <?php
                            foreach (COUNTRIES as $cn) {
                                echo '<option value="(' . $cn["code"] . ') ' . $cn["name"] . '">(' . $cn["code"] . ') ' . $cn["name"] . '</option>';
                            }
                            ?>
                        </select>
                        <input maxlength="10" title="Provide your Provide Number" class="form-control form-control-sm" style="width: 70%" type="tel" name="phone_number" id="phone_number" placeholder="0244123123" required>
                    </div>
                </div>
                <button class="btn btn-primary" type="submit" style="padding: 10px 10px; width:100%">Submit</button>
                <input type="hidden" name="_v1Token" value="<?= $_SESSION["_step1Token"]; ?>">
            </form>
        </div>
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
                        url: "../endpoint/buy-vendor",
                        data: new FormData(this),
                        contentType: false,
                        cache: false,
                        processData: false,
                        success: function(result) {
                            console.log(result);
                            if (result.success) {
                                window.location.href = result.message;
                            } else {
                                alert(result.message);
                            }
                        },
                        error: function(error) {
                            console.log(result);
                        }
                    });
                }
                e.preventDefault();
            });
        });
    </script>
</body>

</html>