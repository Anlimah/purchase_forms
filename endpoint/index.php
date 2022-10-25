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
	} elseif ($_GET["url"] == "formInfo") {
		if (isset($_GET["form_type"]) && !empty($_GET["form_type"])) {
			$form_type = $expose->validateInput($_GET["form_type"]);
			$result = $expose->getFormPrice($form_type);
			if (!empty($result)) {
				$data["success"] = true;
				$data["message"] = $result[0]["amount"];
			} else {
				$data["success"] = false;
				$data["message"] = "Amount not set";
			}
		} else {
			$data["success"] = false;
			$data["message"] = "Amount not set";
		}
		die(json_encode($data));
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

			$v_code = $expose->genCode(6);
			$subject = 'VERIFICATION CODE';
			$message = "Hi " . $_SESSION["step1"]["first_name"] . " " . $_SESSION["step1"]["last_name"] . ", <br> Your verification code is " . $v_code;

			if ($expose->sendEmail($_SESSION['step2']["email_address"], $subject, $message)) {
				$_SESSION['email_code'] = $v_code;
				$_SESSION['step2Done'] = true;
				$data["success"] = true;
			} else {
				$data["success"] = false;
				$data["message"] = "Error occured while sending email!";
			}
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
				if ($otp == $_SESSION['email_code']) {
					$_SESSION['step3Done'] = true;
					$data["success"] = true;
				} else {
					$data["success"] = false;
					$data["message"] = "Email verification code provided is incorrect!";
				}
			}
		} else {
			$data["success"] = false;
			$data["message"] = "Invalid request!";
		}
		die(json_encode($data));
	} elseif ($_GET["url"] == "verifyStep4") {
		if (isset($_SESSION["_step4Token"]) && !empty($_SESSION["_step4Token"]) && isset($_POST["_v4Token"]) && !empty($_POST["_v4Token"]) && $_POST["_v4Token"] == $_SESSION["_step4Token"]) {
			if (isset($_POST["country"]) && !empty($_POST["country"]) && isset($_POST["phone_number"]) && !empty($_POST["phone_number"])) {

				$country = $expose->validateCountryCode($_POST["country"]);
				$charPos = strpos($country, ")");

				$country_name = substr($country, ($charPos + 2));
				$country_code = substr($country, 1, ($charPos - 1));

				$phone_number = $expose->validateInput($_POST["phone_number"]);

				$_SESSION["step4"] = array(
					"country_name" => $country_name,
					"country_code" => $country_code,
					"phone_number" => $phone_number,
				);
				$otp_code = $expose->sendOTP($phone_number, $country_code);
				if ($otp_code) {
					$_SESSION['sms_code'] = $otp_code;
					$_SESSION['step4Done'] = true;
					$data["success"] = true;
				} else {
					$data["success"] = false;
					$data["message"] = "Error occured while sending OTP!";
				}
			} else {
				$data["success"] = false;
				$data["message"] = "Invalid request! 2";
			}
		} else {
			$data["success"] = false;
			$data["message"] = "Invalid request! 1";
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
			//$pay_method = $expose->validateInput($_POST["pay_method"]);
			$amount = $expose->getFormPrice($form_type)[0]["amount"];

			/*$app_type = 0;
			if ($form_type == 'Undergraduate (Degree)' || $form_type == 'Undergraduate (Diploma)' || $form_type == 'Short courses') {
				$app_type = 1;
			} else if ($form_type == 'Postgraduate') {
				$app_type = 2;
			}

			$app_year = $expose->getAdminYearCode();*/

			if (!empty($amount)) {
				$_SESSION["step6"] = array(
					"form_type" => $form_type,
					"amount" => $amount,
					"pay_method" => "ONLINE",
					"vendor_id" => $_SESSION["vendor_id"]
					//"app_type" => $app_type,
					//"app_year" => $app_year,
				);
				$_SESSION['step6Done'] = true;

				if (isset($_SESSION['step1Done']) && isset($_SESSION['step2Done']) && isset($_SESSION['step3Done']) && isset($_SESSION['step4Done']) && isset($_SESSION['step5Done']) && isset($_SESSION['step6Done'])) {
					if ($_SESSION['step1Done'] == true && $_SESSION['step2Done'] == true && $_SESSION['step3Done'] == true && $_SESSION['step4Done'] == true && $_SESSION['step5Done'] == true && $_SESSION['step6Done'] == true) {
						$_SESSION["customerData"] = array(
							"first_name" => $_SESSION["step1"]["first_name"],
							"last_name" => $_SESSION["step1"]["last_name"],
							"email_address" => $_SESSION["step2"]["email_address"],
							"country_name" => $_SESSION["step4"]["country_name"],
							"country_code" => $_SESSION["step4"]["country_code"],
							"phone_number" => $_SESSION["step4"]["phone_number"],
							"form_type" => $_SESSION["step6"]["form_type"],
							"pay_method" => "ONLINE",
							"amount" => $_SESSION["step6"]["amount"],
							"vendor_id" => $_SESSION["vendor_id"]
							//"app_type" => $_SESSION["step6"]["app_type"],
							//"app_year" => $_SESSION["step6"]["app_year"]
						);
						$data = $expose->callOrchardGateway($_SESSION["customerData"]);
						session_unset();
						session_destroy();
					}
				}
			} else {
				$data["success"] = false;
				$data["message"] = "Error occured while processing selected amount!";
			}
		} else {
			$data["success"] = false;
			$data["message"] = "Invalid request!";
		}
		die(json_encode($data));
	}

	//Step for mobile money
	/*elseif ($_GET["url"] == "verifyStep7Momo") {
		if (isset($_SESSION["_step7MomoToken"]) && !empty($_SESSION["_step7MomoToken"]) && isset($_POST["_v7MomoToken"]) && !empty($_POST["_v7MomoToken"]) && $_POST["_v7MomoToken"] == $_SESSION["_step7MomoToken"]) {
			$_SESSION["step7"] = array(
				"momo_agent" => $expose->validateInput($_POST["momo_agent"]),
				"momo_number" => $expose->validatePhone($_POST["momo_number"])
			);

			if (!empty($_SESSION["step7"])) $_SESSION['step7Done'] = true;

			if (isset($_SESSION['step1Done']) && isset($_SESSION['step2Done']) && isset($_SESSION['step3Done']) && isset($_SESSION['step4Done']) && isset($_SESSION['step5Done']) && isset($_SESSION['step6Done']) && isset($_SESSION['step7Done'])) {
				if ($_SESSION['step1Done'] == true && $_SESSION['step2Done'] == true && $_SESSION['step3Done'] == true && $_SESSION['step4Done'] == true && $_SESSION['step5Done'] == true && $_SESSION['step6Done'] == true && $_SESSION['step7Done'] == true) {
					$data = $expose->callOrchardGateway($_SESSION["step6"]["amount"]);
				}
			}
		} else {
			$data["success"] = false;
			$data["message"] = "Invalid request!";
		}
		die(json_encode($data));
	}*/

	//Online Payment confirmation
	elseif ($_GET["url"] == "confirm") {
		if (isset($_POST["status"]) && !empty($_POST["status"]) && isset($_POST["exttrid"]) && !empty($_POST["exttrid"])) {
			$status = $expose->validateInput($_POST["status"]);
			$transaction_id = $expose->validatePhone($_POST["exttrid"]);
			$data = $expose->confirmPurchase($transaction_id);
		} else {
			$data["success"] = false;
			$data["message"] = "Invalid request! 1";
		}
		die(json_encode($data));
	}

	//vendor login
	elseif ($_GET["url"] == "loginVendor") {
		if (isset($_SESSION["_loginToken"]) && !empty($_SESSION["_loginToken"]) && isset($_POST["_vlToken"]) && !empty($_POST["_vlToken"]) && $_POST["_vlToken"] == $_SESSION["_loginToken"]) {
			if (isset($_POST["username"]) && !empty($_POST["username"])) {
				if (isset($_POST["password"]) && !empty($_POST["password"])) {
					//Francis N394YAZ6P
					//Agnes N394YAZ6P

					$username = $expose->validateText($_POST["username"]);
					$password = $expose->validatePassword($_POST["password"]);

					$data = $expose->verifyVendorLogin($username, $password);

					if ($data["success"]) {
						$_SESSION["vendor_id"] = $data["message"];
						$vendorPhone = $expose->getVendorPhone($_SESSION["vendor_id"]);
						if (!empty($vendorPhone)) {
							$otp_code = $expose->sendOTP($vendorPhone[0]["phone_number"], $vendorPhone[0]["country_code"]);
							if ($otp_code) {
								$_SESSION['sms_code'] = $otp_code;
								$_SESSION['verifySMSCode'] = true;
								$data["success"] = true;
								$data["message"] = "Login successfull!";
							} else {
								$data["success"] = false;
								$data["message"] = "Error occured while sending OTP!";
							}
						} else {
							$data["success"] = false;
							$data["message"] = "No phone number entry found for this user!";
						}
					}
				} else {
					$data["success"] = false;
					$data["message"] = "Password field is required!";
				}
			} else {
				$data["success"] = false;
				$data["message"] = "Username field is required!";
			}
		}
		die(json_encode($data));
	}

	//After a successfull login, verify vendor mobile phone before redirection to home page
	elseif ($_GET["url"] == "verifyVendor") {
		if (isset($_SESSION["_verifySMSToken"]) && !empty($_SESSION["_verifySMSToken"]) && isset($_POST["_vSMSToken"]) && !empty($_POST["_vSMSToken"]) && $_POST["_vSMSToken"] == $_SESSION["_verifySMSToken"]) {
			if (isset($_POST["code"]) && !empty($_POST["code"])) {
				$otp = "";
				foreach ($_POST["code"] as $code) {
					$otp .= $code;
				}
				if ($otp == $_SESSION['sms_code']) {
					$_SESSION["SMSLogin"] = true;
					$_SESSION["loginSuccess"] = true;
					$data["success"] = true;
					$data["message"] = "index.php";
				} else {
					$data["success"] = false;
					$data["message"] = "Entry did not match OTP code sent!!";
				}
			} else {
				$data["success"] = false;
				$data["message"] = "Code entries are needed!";
			}
		} else {
			$data["success"] = false;
			$data["message"] = "Invalid request!";
		}
		die(json_encode($data));
	}

	//Vendor endpoint
	elseif ($_GET["url"] == "vendor") {
		if (isset($_SESSION["_vendor1Token"]) && !empty($_SESSION["_vendor1Token"]) && isset($_POST["_v1Token"]) && !empty($_POST["_v1Token"]) && $_POST["_v1Token"] == $_SESSION["_vendor1Token"]) {
			if (isset($_POST["form_type"]) && isset($_POST["first_name"]) && isset($_POST["last_name"]) && isset($_POST["country"]) && isset($_POST["phone_number"])) {
				if (!empty($_POST["form_type"]) && !empty($_POST["first_name"]) && !empty($_POST["last_name"]) && !empty($_POST["country"]) && !empty($_POST["phone_number"])) {

					$first_name = $expose->validateText($_POST["first_name"]);
					$last_name = $expose->validateText($_POST["last_name"]);
					$phone_number = $expose->validatePhone($_POST["phone_number"]);
					$country = $expose->validateCountryCode($_POST["country"]);

					$charPos = strpos($country, ")");
					$country_name = substr($country, ($charPos + 2));
					$country_code = substr($country, 1, ($charPos - 1));

					$form_type = $expose->validateInput($_POST["form_type"]);
					//$pay_method = $expose->validateInput($_POST["pay_method"]);
					$amount = $expose->getFormPrice($form_type)[0]["amount"];

					/*$app_type = 0;
					if ($form_type == 'Undergraduate (Degree)' || $form_type == 'Undergraduate (Diploma)' || $form_type == 'Short courses') {
						$app_type = 1;
					} else if ($form_type == 'Postgraduate') {
						$app_type = 2;
					}

					$app_year = $expose->getAdminYearCode();*/

					if (!empty($amount)) {
						$_SESSION["vendorData"] = array(
							"first_name" => $first_name,
							"last_name" => $last_name,
							"country_name" => $country_name,
							"country_code" => $country_code,
							"phone_number" => $phone_number,
							"email_address" => "",
							"form_type" => $form_type,
							"pay_method" => "CASH",
							"amount" => $amount,
							"vendor_id" => $_SESSION["vendor_id"]
							//"app_type" => $app_type,
							//"app_year" => $app_year
						);

						if (!empty($_SESSION["vendorData"])) {
							if ($expose->sendOTP($_SESSION["vendorData"]["phone_number"], $_SESSION["vendorData"]["country_code"])) {
								$_SESSION['verifySMSCode'] = true;
								$data["success"] = true;
								$data["message"] = "OTP verification code sent!";
							} else {
								$data["success"] = false;
								$data["message"] = "Error occured while sending OTP!";
							}
						} else {
							$data["success"] = false;
							$data["message"] = "Failed in preparing data submitted!";
						}
					} else {
						$data["success"] = false;
						$data["message"] = "Unset data values!";
					}
				} else {
					$data["success"] = false;
					$data["message"] = "Some required fields might be empty!";
				}
			} else {
				$data["success"] = false;
				$data["message"] = "Invalid inputs!";
			}
		} else {
			$data["success"] = false;
			$data["message"] = "Invalid request!1";
		}
		die(json_encode($data));
	}

	//Verify customer phone number before sending Application login details
	elseif ($_GET["url"] == "verifyCustomer") {
		if (isset($_SESSION["_verifySMSToken"]) && !empty($_SESSION["_verifySMSToken"]) && isset($_POST["_vSMSToken"]) && !empty($_POST["_vSMSToken"]) && $_POST["_vSMSToken"] == $_SESSION["_verifySMSToken"]) {
			if (isset($_POST["code"]) && !empty($_POST["code"])) {
				$otp = "";
				foreach ($_POST["code"] as $code) {
					$otp .= $code;
				}
				if ($otp == $_SESSION['sms_code']) {
					if (isset($_SESSION["vendorData"]) && !empty($_SESSION["vendorData"])) {
						if ($expose->vendorExist($_SESSION["vendorData"]["vendor_id"])) {
							$data = $expose->processVendorPay($_SESSION["vendorData"]);
						} else {
							$data["success"] = false;
							$data["message"] = "Process can only be performed by a vendor!";
						}
					} else {
						$data["success"] = false;
						$data["message"] = "Empty data payload!";
					}
				} else {
					$data["success"] = false;
					$data["message"] = "Entry did not match OTP code sent!";
				}
			} else {
				$data["success"] = false;
				$data["message"] = "Code entries are needed!";
			}
		} else {
			$data["success"] = false;
			$data["message"] = "Invalid request!";
		}
		die(json_encode($data));
	}

	//Vendor Payment confirmation
	elseif ($_GET["url"] == "vendorConfirm") {
		if (isset($_POST["status"]) && !empty($_POST["status"]) && isset($_POST["exttrid"]) && !empty($_POST["exttrid"])) {
			$status = $expose->validatePhone($_POST["status"]);
			$transaction_id = $expose->validatePhone($_POST["exttrid"]);
			$data = $expose->confirmVendorPurchase($_SESSION["vendor_id"], $transaction_id);
		} else {
			$data["success"] = false;
			$data["message"] = "Invalid request! 1";
		}
		die(json_encode($data));
	}
} else if ($_SERVER['REQUEST_METHOD'] == "PUT") {
	parse_str(file_get_contents("php://input"), $_PUT);
	die(json_encode($data));
} else if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
	parse_str(file_get_contents("php://input"), $_DELETE);
	die(json_encode($data));
} else {
	http_response_code(405);
}
