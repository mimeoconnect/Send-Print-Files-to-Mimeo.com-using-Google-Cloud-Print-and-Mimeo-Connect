<?php
$Printer_Name =  "[Name of Your Printer]";
$Printer_Description =  "[Description of Your Printer]";
$Printer_Proxy =  "[Unique ID of Your Printer]";
$Printer_Status =  "Online";		
$Printer_PPD = "[Path to PPD Definition]";

//Actually Register the Printer
$client = new Zend_Gdata_HttpClient();  
$client = $client->setClientLoginToken($_SESSION['Client_Login_Token']);

$client->setHeaders('Authorization','GoogleLogin auth='.$_SESSION['Client_Login_Token']); 
$client->setHeaders('X-CloudPrint-Proxy','Mimeo'); 

//GCP Services - Register
$client->setUri('http://www.google.com/cloudprint/interface/register');

$client->setParameterPost('printer', $Printer_Name);
$client->setParameterPost('proxy', $Printer_Proxy);

//Pull Capabilities from PPD File
$Capabilities = file_get_contents($Printer_PPD);

$client->setParameterPost('capabilities', $Capabilities);
$client->setParameterPost('defaults', $Capabilities);
$client->setParameterPost('status', 'Online');
$client->setParameterPost('description', $Printer_Description);

$response = $client->request(Zend_Http_Client::POST);

//echo $response;

$PrinterResponse = json_decode($response->getBody());

//var_dump($PrinterResponse);

$Success = $PrinterResponse->success;
//echo "Success: " . $Success . "<br />";

// Printer Information
$Printer = $PrinterResponse->printers[0];

$User_Printer_ID = $Printer->id;
//echo "Printer ID: " . $Printer_ID . "<br />";

$Printer_Name = $Printer->name;
//echo "Printer Name: " . $Printer_Name . "<br />";	

$Printer_Description = $Printer->description;
//echo "Printer Description: " . $Printer_Description . "<br />";		

$Printer_Status = $Printer->status;
//echo "Printer Status: " . $Printer_Status . "<br />";	

$Printer_CreateTime = $Printer->createTime;
//echo "Printer CreateTime: " . $Printer_CreateTime . "<br />";
?>