<?php

namespace App\Libraries;

class Pdf{
	function __construct(){
		include_once app_path('ThirdParty/TCPDF-6.3.5/tcpdf.php');
    	include_once app_path('ThirdParty/fpdi/src/autoload.php');
    	include_once app_path('ThirdParty/fpdf/fpdf.php');
	}
}