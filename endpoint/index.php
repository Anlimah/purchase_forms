<?php
session_start();
/*
* Designed and programmed by
* @Author: Francis A. Anlimah
*/

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require "../bootstrap.php";

use Src\Controller\ExposeDataController;

$expose = new ExposeDataController();

$data = [];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == "GET") {
	if ($_GET["url"] == "verifyStepFinal") {
		//echo json_encode($expose->getAllProInfo());
		$arr = array();
		array_push($arr, $_SESSION["step1"], $_SESSION["step2"], $_SESSION["step4"], $_SESSION["step6"], $_SESSION["step7"]);
		echo json_encode($arr);
		//verify all sessions
		//save all user data
		//echo success message
	}

	// All POST request will be sent here
} elseif ($_SERVER['REQUEST_METHOD'] == "POST") {
	// verify applicant provided details
	if ($_GET["url"] == "verifyStep1") {
		if (isset($_SESSION["_step1Token"]) && !empty($_SESSION["_step1Token"]) && isset($_POST["_v1Token"]) && !empty($_POST["_v1Token"]) && $_POST["_v1Token"] == $_SESSION["_step1Token"]) {
			$_SESSION["step1"] = array(
				"first_name" => $expose->validateInput($_POST["first_name"]),
				"last_name" => $expose->validateInput($_POST["last_name"])
			);
			$_SESSION['step1Done'] = true;
			$data["success"] = true;
		} else {
			$data["success"] = false;
			$data["message"] = "Invalid request!";
		}
		die(json_encode($data));
	} else if ($_GET["url"] == "verifyStep2") {
		if (isset($_SESSION["_step2Token"]) && !empty($_SESSION["_step2Token"]) && isset($_POST["_v2Token"]) && !empty($_POST["_v2Token"]) && $_POST["_v2Token"] == $_SESSION["_step2Token"]) {
			$_SESSION["step2"] = array(
				"email_address" => $expose->validateInput($_POST["email_address"])
			);
			$_SESSION['step2Done'] = true;
			$data["success"] = true;
		} else {
			$data["success"] = false;
			$data["message"] = "Invalid request!";
		}
		die(json_encode($data));
	} elseif ($_GET["url"] == "verifyStep3") {
		if (isset($_SESSION["_step3Token"]) && !empty($_SESSION["_step3Token"]) && isset($_POST["_v3Token"]) && !empty($_POST["_v3Token"]) && $_POST["_v3Token"] == $_SESSION["_step3Token"]) {
			if ($_POST["num"]) {
				$otp = "";
				foreach ($_POST["num"] as $num) {
					$otp .= $num;
				}
				$_SESSION['step3Done'] = true;
				$data["success"] = true;
			}
		} else {
			$data["success"] = false;
			$data["message"] = "Invalid request!";
		}
		die(json_encode($data));
	} elseif ($_GET["url"] == "verifyStep4") {
		if (isset($_SESSION["_step4Token"]) && !empty($_SESSION["_step4Token"]) && isset($_POST["_v4Token"]) && !empty($_POST["_v4Token"]) && $_POST["_v4Token"] == $_SESSION["_step4Token"]) {
			$phone_number = $expose->validateInput($_POST["phone_number"]);
			$_SESSION["step4"] = array("phone_number" => $phone_number);
			if ($expose->sendOTP($phone_number)) {
				$_SESSION['step4Done'] = true;
				$data["success"] = true;
			} else {
				$data["success"] = false;
				$data["message"] = "Error occured while sending OTP!";
			}
		} else {
			$data["success"] = false;
			$data["message"] = "Invalid request!";
		}
		die(json_encode($data));
	} elseif ($_GET["url"] == "verifyStep5") {
		if (isset($_SESSION["_step5Token"]) && !empty($_SESSION["_step5Token"]) && isset($_POST["_v5Token"]) && !empty($_POST["_v5Token"]) && $_POST["_v5Token"] == $_SESSION["_step5Token"]) {
			if ($_POST["code"]) {
				$otp = "";
				foreach ($_POST["code"] as $code) {
					$otp .= $code;
				}
				if ($otp == $_SESSION['sms_code']) {
					$_SESSION['step5Done'] = true;
					$data["success"] = true;
				} else {
					$data["success"] = false;
					$data["message"] = "OTP code provided is incorrect!";
				}
			}
		} else {
			$data["success"] = false;
			$data["message"] = "Invalid request!";
		}
		die(json_encode($data));
	} elseif ($_GET["url"] == "verifyStep6") {
		if (isset($_SESSION["_step6Token"]) && !empty($_SESSION["_step6Token"]) && isset($_POST["_v6Token"]) && !empty($_POST["_v6Token"]) && $_POST["_v6Token"] == $_SESSION["_step6Token"]) {

			$form_type = $expose->validateInput($_POST["form_type"]);
			$pay_method = $expose->validateInput($_POST["pay_method"]);
			$amount = $expose->getFormPrice($form_type)[0]["amount"];

			if ($form_type == 'Undergraduate' || $form_type == 'Short courses') {
				$app_type = 1;
			} else if ($form_type == 'Postgraduate') {
				$app_type = 2;
			}

			$app_year = $expose->getAdminYearCode();

			if ($amount) {
				$_SESSION["step6"] = array(
					'user' => microtime(true),
					"form_type" => $form_type,
					"pay_method" => $pay_method,
					"amount" => $amount,
					"app_type" => $app_type,
					"app_year" => $app_year,
				);
				$_SESSION['step6Done'] = true;
				$data["success"] = true;
			} else {
				$data["success"] = false;
				$data["message"] = "Error occured while processing selected amount!";
			}
		} else {
			$data["success"] = false;
			$data["message"] = "Invalid request!";
		}
		die(json_encode($data));
	} elseif ($_GET["url"] == "verifyStep7Momo") {
		if (isset($_SESSION["_step7MomoToken"]) && !empty($_SESSION["_step7MomoToken"]) && isset($_POST["_v7MomoToken"]) && !empty($_POST["_v7MomoToken"]) && $_POST["_v7MomoToken"] == $_SESSION["_step7MomoToken"]) {
			$_SESSION["step7"] = array(
				"momo_agent" => $expose->validateInput($_POST["momo_agent"]),
				"momo_number" => $expose->validatePhone($_POST["momo_number"])
			);

			if (!empty($_SESSION["step7"])) $_SESSION['step7Done'] = true;

			if (isset($_SESSION['step1Done']) && isset($_SESSION['step2Done']) && isset($_SESSION['step3Done']) && isset($_SESSION['step4Done']) && isset($_SESSION['step5Done']) && isset($_SESSION['step6Done']) && isset($_SESSION['step7Done'])) {
				if ($_SESSION['step1Done'] == true && $_SESSION['step2Done'] == true && $_SESSION['step3Done'] == true && $_SESSION['step4Done'] == true && $_SESSION['step5Done'] == true && $_SESSION['step6Done'] == true && $_SESSION['step7Done'] == true) {
					$data = $expose->callOrchardGateway($_SESSION["step6"]["amount"], $_SESSION["step7"]["momo_number"], $_SESSION["step6"]["pay_method"], $_SESSION["step7"]["momo_agent"]);
				}
			}
		} else {
			$data["success"] = false;
			$data["message"] = "Invalid request!";
		}
		die(json_encode($data));
	} elseif ($_GET["url"] == "confirm") {
		if (isset($_POST["status"]) && !empty($_POST["status"]) && isset($_POST["transaction_id"]) && !empty($_POST["transaction_id"])) {
			$status = $expose->validateInput($_POST["status"]);
			$transaction_id = $expose->validatePhone($_POST["transaction_id"]);
			$data = $expose->confirmPurchase($transaction_id);
		} else {
			$data["success"] = false;
			$data["message"] = "Invalid request!";
		}
		die(json_encode($data));
	} /*elseif ($_GET["url"] == "verifyStep7Bank") {
		$message = array("response" => "error", "message" => "Invalid request!");

		if (isset($_SESSION["_step7BankToken"]) && !empty($_SESSION["_step7BankToken"])) {
			if (isset($_POST["_v7BankToken"]) && !empty($_POST["_v7BankToken"])) {
				if ($_POST["_v7BankToken"] == $_SESSION["_step7BankToken"]) {
					die(json_encode($message));
				} else {
					$currency = $expose->validateInput($_POST["currency"]);
					$bank = $expose->validateInput($_POST["bank"]);
					$account_number = $expose->validateInput($_POST["account_number"]);
					$_SESSION["step7"] = array("currency" => $currency, "bank" => $bank, "account_number" => $account_number);
					echo json_encode($_SESSION["step7"]);
					$_SESSION['step7Done'] = true;
					//if ($_SESSION['step7Done']) header('Location: ../src/Controller/PaymentController.php');
					//$expose->payViaBank();
				}
			} else {
				die(json_encode($message));
			}
		} else {
			die(json_encode($message));
		}
		exit();
	}*/
} else if ($_SERVER['REQUEST_METHOD'] == "PUT") {
	parse_str(file_get_contents("php://input"), $_PUT);
	die(json_encode($data));
} else if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
	parse_str(file_get_contents("php://input"), $_DELETE);
	die(json_encode($data));
} else {
	http_response_code(405);
}
