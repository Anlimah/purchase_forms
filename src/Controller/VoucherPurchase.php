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
        $user_code = $this->dm->genCode(5);
        $app_number = 'RMU-' . (($type * 10000000) + ($year * 100000) + $user_code);
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

    private function savePurchaseDetails(int $pi, int $ft, int $pm, int $ap, $fn, $ln, $cn, $ea, $pn)
    {
        $sql = "INSERT INTO `purchase_detail` (`id`, `first_name`, `last_name`, `country`, `email_address`, `phone_number`, `form_type`, `payment_method`, `admission_period`) 
                VALUES(:ui, :fn, :ln, :cn, :ea, :pn, :ft, :pm, :ap)";
        $params = array(
            ':ui' => $pi,
            ':fn' => $fn,
            ':ln' => $ln,
            ':cn' => $cn,
            ':ea' => $ea,
            ':pn' => $pn,
            ':ft' => $ft,
            ':pm' => $pm,
            ':ap' => $ap,
        );
        if ($this->dm->inputData($sql, $params)) {
            return $pi;
        }
        return 0;
    }

    private function saveVendorPurchaseData(int $ti, int $vd, int $ft, int $ap, $pm, float $am, $fn, $ln, $em, $cn, $cc, $pn)
    {
        $sql = "INSERT INTO `purchase_detail` (`id`, `vendor`, `form_type`, `admission_period`, `payment_method`, `first_name`, `last_name`, `email_address`, `country_name`, `country_code`, `phone_number`, `amount`) 
                VALUES(:ti, :vd, :ft, :ap, :pm, :fn, :ln, :em, :cn, :cc, :pn, :am)";
        $params = array(
            ':ti' => $ti, ':vd' => $vd, ':ft' => $ft, ':pm' => $pm, ':ap' => $ap, ':fn' => $fn,
            ':ln' => $ln, ':em' => $em, ':cn' => $cn, ':cc' => $cc, ':pn' => $pn, ':am' => $am
        );
        if ($this->dm->inputData($sql, $params)) {
            return $ti;
        }
        return 0;
    }

    private function registerApplicantPersI($user_id)
    {
        $sql1 = "INSERT INTO `personal_information` (`app_login`) VALUES(:a)";
        $params1 = array(':a' => $user_id);
        if ($this->dm->inputData($sql1, $params1)) {
            return 1;
        }
        return 0;
    }

    private function registerApplicantAcaB($user_id)
    {
        $sql1 = "INSERT INTO `academic_background` (`app_login`) VALUES(:a)";
        $params1 = array(':a' => $user_id);
        if ($this->dm->inputData($sql1, $params1)) {
            return 1;
        }
        return 0;
    }

    private function registerApplicantProgI($user_id)
    {
        $sql1 = "INSERT INTO `program_info` (`app_login`) VALUES(:a)";
        $params1 = array(':a' => $user_id);
        if ($this->dm->inputData($sql1, $params1)) {
            return 1;
        }
        return 0;
    }

    private function registerApplicantPreUni($user_id)
    {
        $sql1 = "INSERT INTO `previous_uni_records` (`app_login`) VALUES(:a)";
        $params1 = array(':a' => $user_id);
        if ($this->dm->inputData($sql1, $params1)) {
            return 1;
        }
        return 0;
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

            return 1;
        }
        return 0;
    }

    private function genLoginDetails(int $who, int $type, int $year)
    {
        $rslt = 1;
        while ($rslt) {
            $app_num = $this->genAppNumber($type, $year);
            $rslt = $this->doesCodeExists($app_num);
        }
        $pin = strtoupper($this->genPin());
        if ($this->saveLoginDetails($app_num, $pin, $who)) {
            return array('app_number' => $app_num, 'pin_number' => $pin);
        }
        return 0;
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

    /*public function createApplicant($data)
    {
        if (!empty($data)) {
            $fn = $data['step1']['first_name'];
            $ln = $data['step1']['last_name'];
            $ea = $data['step2']['email_address'];
            $cn = $data['step4']['country_name'];
            $cc = $data['step4']['country_code'];
            $pn = $data['step4']['phone_number'];
            $ft = $data['step6']['form_type'];
            $pm = "Third Party"; //$data['step6']['pay_method'];
            $at = $data['step6']['app_type'];
            $ay = $data['step6']['app_year'];
            $pi = (int) $data['step6']['user'];

            $ap_id = $this->getAdmissionPeriodID();
            $ft_id = $this->getFormTypeID($ft);
            $pm_id = $this->getPaymentMethodID($pm);

            $purchase_id = $this->savePurchaseDetails($pi, $ft_id, $pm_id, $ap_id, $fn, $ln, $cn, $ea, $pn);
            if ($purchase_id) {
                $login_details = $this->genLoginDetails($purchase_id, $at, $ay);
                if (!empty($login_details)) {
                    $key = 'APPLICATION NUMBER: ' . $login_details['app_number'] . '    PIN: ' . $login_details['pin_number'];
                    $message = 'Your RMU Online Application login details ';
                    if ($this->expose->sendSMS($pn,  $key, $message, $cc)) {
                        //return 1;
                        return array("success" => true, "message" =>  "Forms purchase completed!");
                    } else {
                        return array("success" => false, "message" =>  "Failed to send SMS!");
                    }
                } else {
                    return array("success" => false, "message" =>  "Failed to generate login information!");
                }
            } else {
                return array("success" => false, "message" =>  "Failed to log purchase information!");
            }
        } else {
            return array("success" => false, "message" =>  "Invalid request!");
        }
    }*/

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
            $at = $data['app_type'];
            $ay = $data['app_year'];

            $ap_id = $this->getAdmissionPeriodID();
            $ft_id = $this->getFormTypeID($ft);
            //$pm_id = $this->getPaymentMethodID($pm);

            $purchase_id = $this->saveVendorPurchaseData($trans_id, $vd, $ft_id, $ap_id, $pm, $am, $fn, $ln, $em, $cn, $cc, $pn);
            if ($purchase_id) {
                $login_details = $this->genLoginDetails($purchase_id, $at, $ay);
                if (!empty($login_details)) {
                    $key = 'APPLICATION NUMBER: ' . $login_details['app_number'] . '    PIN: ' . $login_details['pin_number'];
                    $message = 'Your RMU Online Application login details ';
                    if ($this->expose->sendSMS($pn,  $key, $message, $cc)) {
                        //return 1;
                        return array("success" => true, "message" =>  "confirm.php?status=000&exttrid=" . $trans_id);
                    } else {
                        return array("success" => false, "message" =>  "confirm.php?status=001&exttrid=" . $trans_id);
                    }
                } else {
                    return array("success" => false, "message" =>  "confirm.php?status=002&exttrid=" . $trans_id);
                }
            } else {
                return array("success" => false, "message" => "confirm.php?status=003&exttrid=" . $trans_id);
            }
        } else {
            return array("success" => false, "message" => "confirm.php?status=004&exttrid=" . $trans_id);
        }
    }
}
