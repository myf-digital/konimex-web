<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once FCPATH . 'vendor/tecnickcom/tcpdf/tcpdf.php';

class Pdf extends TCPDF {
    function __construct() {
        parent::__construct();

        $this->SetPrintHeader(false);
        $this->SetPrintFooter(false);
        $this->SetMargins(10, 10, 10); // kiri, atas, kanan
        $this->SetAutoPageBreak(true, 10);

        // Tipiskan garis
        $this->SetLineWidth(0.1); // default 0.2–0.5 mm

        $this->SetDrawColor(255, 255, 255);
    }

    public function Header() {}

    public function Footer() {}
}
