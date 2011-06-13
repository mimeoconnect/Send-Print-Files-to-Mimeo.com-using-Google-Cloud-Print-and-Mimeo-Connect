<?php 
require_once 'Zend/Loader.php';

//Mimeo Account Information
$Mimeo_Root_URL = "https://connect.sandbox.mimeo.com/2010/09/";
$Mimeo_User_Name = "[YOUR MIMEO EMAIL ADDRESS]";
$Mimeo_Password = "[YOUR MIMEO PASSWORD]";

//Actually Register the Printer
$client = new Zend_Gdata_HttpClient();  
$client = $client->setClientLoginToken($_SESSION['Client_Login_Token']);

$client->setHeaders('Authorization','GoogleLogin auth='.$_SESSION['Client_Login_Token']); 
$client->setHeaders('X-CloudPrint-Proxy','Mimeo'); 		

//GCP Services - Register
$client->setUri('http://www.google.com/cloudprint/interface/fetch');

// Your printer definition						
$Printer_ID = "[Your Printer ID]";

//echo "Fetching: " . $Printer_ID . "<br />";
$client->setParameterPost('printerid', $Printer_ID);
$response = $client->request(Zend_Http_Client::POST);
$JobResponse = json_decode($response->getBody());

if(isset($JobResponse->jobs))
	{
		
	$Jobs = $JobResponse->jobs;
	
	// ----------------
	// Retrieve a list of print jobs for printer
	// ----------------
	
	foreach($Jobs as $Job) 
		{
	
		$Job_ID = $Job->id;
		$Job_Title = $Job->title;
		$Job_Content_Type = $Job->contentType;
		$File_URL = $Job->fileUrl;
		$Job_NumberPages = $Job->numberOfPages;
		$Job_Status = $Job->status;
		
		$client->setUri($File_URL);
		$FileResponse = $client->request(Zend_Http_Client::POST);
		$FileContent = $FileResponse->getBody();											
		
		//  Set a local folder you wish to save print job PDF to.
		$Save_Filename = 'files/' . $Job_ID . ".pdf";
		
		//echo "Saving " . $Save_Filename . "<br />";
	    $fh = fopen($Save_Filename, "w");
	    if($fh==false)
	        die("unable to create file");
	    fputs($fh,$FileContent,strlen($FileContent));
	    fclose ($fh);	
	
		// Set a Full URL for your File
	    $Print_Job_URL = "http://yourdomain.com/" . $Save_Filename;
													
		// ----------------
		// Begin Send a Print File to Mimeo
		// ----------------
		
		$inputs = array();
		$inputs["uploadFile"] = "info3@kinlane.com";		
	
		$rest = new RESTclient($Mimeo_Root_URL,$Mimeo_User_Name,$Mimeo_Password);
		$url = "StorageService/[folder name]/";
		$rest->createRequest($url,"POST",$inputs,$FileContent);
		$rest->sendRequest();
		$StorageResponse = $rest->getResponseBody();
		
		// ----------------
		// End Send a Print File to Mimeo
		// ----------------
		
		// ----------------
		// Begin Update Status of Google Cloud Print Job
		// ----------------		
		
		//Update Status of the Print Job with Google
		$client = Zend_Gdata_ClientLogin::getHttpClient($G_Email, $G_Pass, 'cloudprint');
		$Client_Login_Token = $client->getClientLoginToken(); 
		$client->setHeaders('Authorization','GoogleLogin auth='.$Client_Login_Token); 
		$client->setHeaders('X-CloudPrint-Proxy','Mimeo'); 
		
		//GCP Services - Register
		$client->setUri('http://www.google.com/cloudprint/interface/control');
		$client->setParameterPost('jobid', $Job_ID);
		$client->setParameterPost('status', 'Done');
		$response = $client->request(Zend_Http_Client::POST);
		$PrinterResponse = json_decode($response->getBody());
		
		// ----------------
		// End Update Status of Google Cloud Print Job
		// ----------------		
		
		}

	}								
?>