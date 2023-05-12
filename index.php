<?php
session_start();
if (isset($_SESSION["vendor_type"]) && !empty($_SESSION["vendor_type"])) {
    header("Location: buy-" . strtolower($_SESSION["vendor_type"]) . "/");
} else {
    header("Location: buy-online/");
}
