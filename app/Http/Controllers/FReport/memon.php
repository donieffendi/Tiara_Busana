<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, Content-Type, Authorization, Accept, X-Requested-With, x-xsrf-token");
header("Content-Type: application/json; charset=UTF-8");

include "config.php";

    public function report()
    {
$postjson = json_decode(file_get_contents('php://input'), true);
$today = date('Y-m-d');

// if ($postjson['kodea'] != '') $kodea = $postjson['kodea'];
// else $kodea = $_GET['kodea'];

// if ($postjson['kodeb'] != '') $kodeb = $postjson['kodeb'];
// else $kodeb = $_GET['kodeb'];

// $KD_BAG = $postjson['KD_BAG'];
// $PER = $_POST['PER'];
// $NO_PO = $_POST['NO_PO'];
$tgl1 = $_GET['tgl1'];
$tgl2 = $_GET['tgl2'];


include('phpjasperxml/class/tcpdf/tcpdf.php');
include('phpjasperxml/class/PHPJasperXML.inc.php');
include('phpjasperxml/setting.php');
$PHPJasperXML = new \PHPJasperXML();
$PHPJasperXML->load_xml_file("phpjasperxml/memon.jrxml");

$PHPJasperXML->transferDBtoArray(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$PHPJasperXML->arraysqltable = array();

$query = "SELECT MEMO.NO_BUKTI, MEMO.TGL, MEMOD.ACNO, MEMOD.NACNO, MEMOD.ACNOB, MEMOD.NACNOB, MEMOD.DEBET, MEMOD.KREDIT 
				FROM MEMO, MEMOD WHERE MEMO.NO_BUKTI = MEMOD.NO_BUKTI 
				AND MEMO.TGL >='$tgl1' AND MEMO.TGL <='$tgl2'  ORDER BY MEMO.NO_BUKTI ";	
				

$result1 = mysqli_query($mysqli, $query);
while ($row1 = mysqli_fetch_assoc($result1)) {
	array_push($PHPJasperXML->arraysqltable, array(
		"NO_BUKTI" => $row1["NO_BUKTI"],
		"TGL" => $row1["TGL"],
		"ACNO" => $row1["ACNO"],
		"NACNO" => $row1["NACNO"],
		"ACNOB" => $row1["ACNOB"],
		"NACNOB" => $row1["NACNOB"],
		"DEBET" => $row1["DEBET"],
		"KREDIT" => $row1["KREDIT"],
		"URAIAN" => $row1["URAIAN"]
		
	));
}
ob_end_clean();
$PHPJasperXML->outpage("I");
	}
