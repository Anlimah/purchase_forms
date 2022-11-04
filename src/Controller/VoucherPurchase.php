<?php

namespace Src\Controller;

use Src\System\DatabaseMethods;
use Src\Controller\ExposeDataController;

class VoucherPurchase
{
    private $expose;
    private $dm;

    public function __construct()
    {
        $this->expose = new ExposeDataController();
        $this->dm = new DatabaseMethods();
    }

    private function genPin(int $length_pin = 9)
    {
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle($str_result), 0, $length_pin);
    }

    private function genAppNumber(int $type, int $year)
    {
        $user_code = $this->expose->genCode(5);
        $app_number = ($type * 10000000) + ($year * 100000) + $user_code;
        return $app_number;
    }

    private function doesCodeExists($code)
    {
        $sql = "SELECT `id` FROM `applicants_login` WHERE `app_number`=:p";
        if ($this->dm->getID($sql, array(':p' => sha1($code)))) {
            return 1;
        }
        return 0;
    }

    private function saveVendorPurchaseData(int $ti, int $vd, int $ft, int $ap, $pm, float $am, $fn, $ln, $em, $cn, $cc, $pn)
    {
        $sql = "INSERT INTO `purchase_detail` (`id`, `vendor`, `form_type`, `admission_period`, `payment_method`, `first_name`, `last_name`, `email_address`, `country_name`, `country_code`, `phone_number`, `amount`) 
                VALUES(:ti, :vd, :ft, :ap, :pm, :fn, :ln, :em, :cn, :cc, :pn, :am)";
        $params = array(
            ':ti' => $ti, ':vd' => $vd, ':ft' => $ft, ':pm' => $pm, ':ap' => $ap, ':fn' => $fn, ':ln' => $ln,
            ':em' => $em, ':cn' => $cn, ':cc' => $cc, ':pn' => $pn, ':am' => $am
        );
        if ($this->dm->inputData($sql, $params)) {
            return $ti;
        }
        return 0;
    }

    private function updateVendorPurchaseData(int $trans_id, int $app_number, $pin_number, $status)
    {
        $sql = "UPDATE `purchase_detail` SET `app_number`= :a,`pin_number`= :p, `status` = :s WHERE `id` = :t";
        return $this->dm->getData($sql, array(':a' => $app_number, ':p' => $pin_number, ':s' => $status, ':t' => $trans_id));
    }

    private function registerApplicantPersI($user_id)
    {
        $sql = "INSERT INTO `personal_information` (`app_login`) VALUES(:a)";
        $this->dm->inputData($sql, array(':a' => $user_id));
    }

    private function registerApplicantAcaB($user_id)
    {
        $sql = "INSERT INTO `academic_background` (`app_login`) VALUES(:a)";
        $this->dm->inputData($sql, array(':a' => $user_id));
    }

    private function registerApplicantProgI($user_id)
    {
        $sql = "INSERT INTO `program_info` (`app_login`) VALUES(:a)";
        $this->dm->inputData($sql, array(':a' => $user_id));
    }

    private function registerApplicantPreUni($user_id)
    {
        $sql = "INSERT INTO `previous_uni_records` (`app_login`) VALUES(:a)";
        $this->dm->inputData($sql, array(':a' => $user_id));
    }

    private function setFormSectionsChecks($user_id)
    {
        $sql = "INSERT INTO `form_sections_chek` (`app_login`) VALUES(:a)";
        $this->dm->inputData($sql, array(':a' => $user_id));
    }

    private function setHeardAboutUs($user_id)
    {
        $sql = "INSERT INTO `heard_about_us` (`app_login`) VALUES(:a)";
        $this->dm->inputData($sql, array(':a' => $user_id));
    }

    private function getApplicantLoginID($app_number)
    {
        $sql = "SELECT `id` FROM `applicants_login` WHERE `app_number` = :a;";
        return $this->dm->getID($sql, array(':a' => sha1($app_number)));
    }

    private function saveLoginDetails($app_number, $pin, $who)
    {
        $hashed_pin = password_hash($pin, PASSWORD_DEFAULT);
        $sql = "INSERT INTO `applicants_login` (`app_number`, `pin`, `purchase_id`) VALUES(:a, :p, :b)";
        $params = array(':a' => sha1($app_number), ':p' => $hashed_pin, ':b' => $who);

        if ($this->dm->inputData($sql, $params)) {
            $user_id = $this->getApplicantLoginID($app_number);

            //register in Personal information table in db
            $this->registerApplicantPersI($user_id);

            //register in Acaedmic backgorund
            // Removed this education background because data will be bulk saved and also user can add more than 1
            //$this->registerApplicantAcaB($user_id);

            //register in Programs information
            $this->registerApplicantProgI($user_id);

            //register in Previous university information
            $this->registerApplicantPreUni($user_id);

            //Set initial form checks
            $this->setFormSectionsChecks($user_id);

            //Set initial form checks
            $this->setHeardAboutUs($user_id);

            return 1;
        }
        return 0;
    }

    private function genLoginDetails(int $type, int $year)
    {
        $rslt = 1;
        while ($rslt) {
            $app_num = $this->genAppNumber($type, $year);
            $rslt = $this->doesCodeExists($app_num);
        }
        $pin = strtoupper($this->genPin());
        return array('app_number' => $app_num, 'pin_number' => $pin);
    }

    //Get and Set IDs for foreign keys

    private function getAdmissionPeriodID()
    {
        $sql = "SELECT `id` FROM `admission_period` WHERE `active` = 1;";
        return $this->dm->getID($sql);
    }

    private function getFormTypeID($form_type)
    {
        $sql = "SELECT `id` FROM `form_type` WHERE `name` LIKE '%$form_type%'";
        return $this->dm->getID($sql);
    }

    private function getPaymentMethodID($name)
    {
        $sql = "SELECT `id` FROM `payment_method` WHERE `name` LIKE '%$name%'";
        return $this->dm->getID($sql);
    }

    public function SaveFormPurchaseData($data, $trans_id)
    {
        if (!empty($data) && !empty($trans_id)) {
            //return json_encode($data) . " T=" . $trans_id;
            $fn = $data['first_name'];
            $ln = $data['last_name'];
            $em = $data['email_address'];
            $cn = $data['country_name'];
            $cc = $data['country_code'];
            $pn = $data['phone_number'];
            $am = $data['amount'];
            $ft = $data['form_type'];
            $vd = $data['vendor_id'];

            $pm = $data['pay_method'];

            $ap_id = $this->getAdmissionPeriodID();
            $ft_id = $this->getFormTypeID($ft);
            //$pm_id = $this->getPaymentMethodID($pm);

            // For on premises purchases, generate app number and pin and send immediately
            $purchase_id = $this->saveVendorPurchaseData($trans_id, $vd, $ft_id, $ap_id, $pm, $am, $fn, $ln, $em, $cn, $cc, $pn);
            if ($purchase_id) {
                if ($pm == "CASH") {
                    return $this->genLoginsAndSend($purchase_id);
                } else {
                    return array("success" => true, "message" => "Save purchase data!");
                }
            } else {
                return array("success" => false, "message" => "Failed saving purchase data!");
            }
        } else {
            return array("success" => false, "message" => "Invalid data entries!");
        }
    }

    public function getTransactionStatusFromDB($trans_id)
    {
        $sql = "SELECT `id`, `status` FROM `purchase_detail` WHERE `id` = :t";
        return $this->dm->getData($sql, array(':t' => $trans_id));
    }

    public function updateTransactionStatusInDB($status, $trans_id)
    {
        $sql = "UPDATE `purchase_detail` SET `status` = :s WHERE `id` = :t";
        return $this->dm->getData($sql, array(':s' => $status, ':t' => $trans_id));
    }

    private function getAppPurchaseData(int $trans_id)
    {
        // get form_type, country code, phone number
        $sql = "SELECT `form_type`, `country_code`, `phone_number`, `email_address` FROM `purchase_detail` WHERE `id` = :t";
        return $this->dm->getData($sql, array(':t' => $trans_id));
    }

    public function genLoginsAndSend(int $trans_id)
    {
        $data = $this->getAppPurchaseData($trans_id);

        if (!empty($data)) {

            $app_type = 0;
            if ($data[0]["form_type"] == 2 || $data[0]["form_type"] == 3 || $data[0]["form_type"] == 4) {
                $app_type = 1;
            } else if ($data[0]["form_type"] == 'Postgraduate') {
                $app_type = 2;
            }

            $app_year = $this->expose->getAdminYearCode();

            $login_details = $this->genLoginDetails($app_type, $app_year);

            if ($this->saveLoginDetails($login_details['app_number'], $login_details['pin_number'], $trans_id)) {

                $this->updateVendorPurchaseData($trans_id, $login_details['app_number'], $login_details['pin_number'], 'COMPLETED');

                $key = 'APPLICATION NUMBER: RMU-' . $login_details['app_number'] . '    PIN: ' . $login_details['pin_number'] . ". Follow the link, https://admissions.rmuictonline.com to start application process.";
                $message = 'Your RMU Online Application login details. ';

                if ($this->expose->sendSMS($data[0]["phone_number"], $key, $message, $data[0]["country_code"])) {
                    if (!empty($data[0]["email_address"])) {
                        $msg = $message . $key;
                        $this->expose->sendEmail($data[0]["email_address"], 'ONLINE APPLICATION PORTAL LOGIN INFORMATION', $msg);
                    }
                    return array("success" => true, "message" => "Form purchase successful!", "exttrid" => $trans_id);
                } else {
                    return array("success" => false, "message" => "Failed sending login details via SMS!");
                }
            } else {
                return array("success" => false, "message" => "Failed saving login details!");
            }
        }
        return array("success" => false, "message" => "No data records for this transaction!");
    }
}
