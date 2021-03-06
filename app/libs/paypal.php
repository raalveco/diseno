<?php

	/* ================================== *
	 * PayPal Express Checkout Amecasoft  *
	 *                                    *
	 * Version 2.0                        *
	 * Fecha: 21/03/2012                  *
	 *                                    *
	 * Desarrollador:                     *
	 *   Ramiro Alonso Vera Contreras     *
	 * ================================== */

	class ConceptoPaypal{
		var $cantidad;
		var $descripcion;
		var $precio;
		
		public function ConceptoPaypal($c, $d, $p){
			$this -> cantidad = $c;
			$this -> descripcion = $d;
			$this -> precio = $p;
		}
	}
	 
	class Paypal{
		var $sandbox = false;

		//'------------------------------------
		//' PayPal API Credentials
		//' Reemplazar <API_USERNAME> con su API Username
		//' Reemplazar <API_PASSWORD> con su API Password
		//' Reemplazar <API_SIGNATURE> con su Signature
		//'------------------------------------
		
		var $API_UserName="ventas_api1.amecasoft.com.mx";
		var $API_Password="2KVKEPHCQDH2UP7R";
		var $API_Signature="AFcWxV21C7fd0v3bYYYRCpSSRl31AORwB7afyFHecoeVc.eut1i.V1vn";
		
		//var $API_UserName="univer_1332350170_biz_api1.gmail.com";
		//var $API_Password="1332350195";
		//var $API_Signature="AAi6CWPybjLDCnq96kqzJlVNxN9IAiMmDzmtD-c0wQNDghNMJ.aq5qof";

		// BN Code 	is only applicable for partners
		var $sBNCode = "PP-ECWizard";
		var $version="64";
		
		//'------------------------------------
		//' The paymentAmount is the total value of 
		//' the shopping cart, that was set 
		//' earlier in a session variable 
		//' by the shopping cart page
		//'------------------------------------
		var $paymentAmount = 0.0;
		
		//'------------------------------------
		//' The currencyCodeType and paymentType 
		//' are set to the selections made on the Integration Assistant 
		//'------------------------------------
		var $currencyCodeType = "MXN";
		var $paymentType = "Sale";
		
		//'------------------------------------
		//' The returnURL is the location where buyers return to when a
		//' payment has been succesfully authorized.
		//'
		//' This is set to the value entered on the Integration Assistant 
		//'------------------------------------
		var $returnURL = "http://www.amecasoft.com.mx/erp/paypal/confirmado";
		
		//'------------------------------------
		//' The cancelURL is the location buyers are sent to when they hit the
		//' cancel button during authorization of payment during the PayPal flow
		//'
		//' This is set to the value entered on the Integration Assistant 
		//'------------------------------------
		var $cancelURL = "http://www.amecasoft.com.mx/erp/paypal/cancelado";
		
		var $API_Endpoint;
		var $PAYPAL_URL;
		
		var $conceptos = false;
		var $nc = 0;
		
		var $venta = 0;
		var $meses = 0;
		
		public function Paypal(){
			
			if ($this -> sandbox) 
			{
				$this -> API_Endpoint = "https://api-3t.sandbox.paypal.com/nvp";
				$this -> PAYPAL_URL = "https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token=";
			}
			else
			{
				$this -> API_Endpoint = "https://api-3t.paypal.com/nvp";
				$this -> PAYPAL_URL = "https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=";
			}
			
			if (session_id() == "") 
				session_start();
		}
		
		public function agregarConcepto($cantidad, $descripcion, $precio){
			$this -> conceptos[$this -> nc++] = new ConceptoPaypal($cantidad, $descripcion, $precio);
		}
		
		public function iniciar($venta, $meses = 0){
			$this -> venta = $venta;
			$this -> meses = $meses;
			
			$this -> returnURL = "http://www.amecasoft.com.mx/erp/paypal/confirmado/" . $venta . "/" . $meses;
			$resultado = $this -> CallShortcutExpressCheckout (); 
			
			print_r($resultado);
			
			$ack = strtoupper($resultado["ACK"]);
			if($ack=="SUCCESS" || $ack=="SUCCESSWITHWARNING")
			{
				$this -> RedirectToPayPal ( $resultado["TOKEN"] ); return;
			} 
			
			return $resultado;
		}
		
		public function confirmar($token, $payer){
			$resultado = $this -> ConfirmPayment ($token, $payer); 
			
			return $resultado;
		}
		
		public function consultar($token){
			$resultado = $this -> GetShippingDetails ($token); 
			
			return $resultado;
		}
		
		function CallShortcutExpressCheckout() 
		{
			//------------------------------------------------------------------------------------------------------------------------------------
			// Construct the parameter string that describes the SetExpressCheckout API call in the shortcut implementation
			
			
			$nvpstr = "&PAYMENTREQUEST_0_PAYMENTACTION=" . $this -> paymentType;
			$nvpstr = $nvpstr . "&RETURNURL=" . $this -> returnURL;
			$nvpstr = $nvpstr . "&CANCELURL=" . $this -> cancelURL;
			$nvpstr = $nvpstr . "&PAYMENTREQUEST_0_CURRENCYCODE=" . $this -> currencyCodeType;
			
			$nvpstr = $nvpstr . "&LANDINGPAGE=Billing";
			
			$total = 0;
			
			$x=-1; foreach($this -> conceptos as $concepto){ $x++;
				$nvpstr .= "&L_PAYMENTREQUEST_0_NAME".$x."=".$concepto -> descripcion;

				$nvpstr .= "&L_PAYMENTREQUEST_0_AMT0".$x."=".$concepto -> precio;

            	$nvpstr .= "&L_PAYMENTREQUEST_0_QTY0".$x."=".$concepto -> cantidad;    
				
				$total += $concepto -> cantidad * $concepto -> precio;
			}
			
			echo $total."<br>";
			print_r($this -> conceptos);
			echo "<br>";
			
			$nvpstr .= "&PAYMENTREQUEST_0_AMT=". $total;
			
			$_SESSION["currencyCodeType"] = $this -> currencyCodeType;	  
			$_SESSION["PaymentType"] = $this -> paymentType;
			$_SESSION['FinalAmt']=$total;
	
			//'--------------------------------------------------------------------------------------------------------------- 
			//' Make the API call to PayPal
			//' If the API call succeded, then redirect the buyer to PayPal to begin to authorize payment.  
			//' If an error occured, show the resulting errors
			//'---------------------------------------------------------------------------------------------------------------
		    $resArray = $this -> hash_call("SetExpressCheckout", $nvpstr);
			
			$ack = strtoupper($resArray["ACK"]);
			if($ack=="SUCCESS" || $ack=="SUCCESSWITHWARNING")
			{
				$token = urldecode($resArray["TOKEN"]);
				$_SESSION['TOKEN']=$token;
			}
			   
		    return $resArray;
		}
	
		/*   
		'-------------------------------------------------------------------------------------------------------------------------------------------
		' Purpose: 	Prepares the parameters for the SetExpressCheckout API Call.
		' Inputs:  
		'		paymentAmount:  	Total value of the shopping cart
		'		currencyCodeType: 	Currency code value the PayPal API
		'		paymentType: 		paymentType has to be one of the following values: Sale or Order or Authorization
		'		returnURL:			the page where buyers return to after they are done with the payment review on PayPal
		'		cancelURL:			the page where buyers return to when they cancel the payment review on PayPal
		'		shipToName:		the Ship to name entered on the merchant's site
		'		shipToStreet:		the Ship to Street entered on the merchant's site
		'		shipToCity:			the Ship to City entered on the merchant's site
		'		shipToState:		the Ship to State entered on the merchant's site
		'		shipToCountryCode:	the Code for Ship to Country entered on the merchant's site
		'		shipToZip:			the Ship to ZipCode entered on the merchant's site
		'		shipToStreet2:		the Ship to Street2 entered on the merchant's site
		'		phoneNum:			the phoneNum  entered on the merchant's site
		'--------------------------------------------------------------------------------------------------------------------------------------------	
		*/
		function CallMarkExpressCheckout( $paymentAmount, $currencyCodeType, $paymentType, $returnURL, 
										  $cancelURL, $shipToName, $shipToStreet, $shipToCity, $shipToState,
										  $shipToCountryCode, $shipToZip, $shipToStreet2, $phoneNum
										) 
		{
			//------------------------------------------------------------------------------------------------------------------------------------
			// Construct the parameter string that describes the SetExpressCheckout API call in the shortcut implementation
			
			$nvpstr="&PAYMENTREQUEST_0_AMT=". $paymentAmount;
			$nvpstr = $nvpstr . "&PAYMENTREQUEST_0_PAYMENTACTION=" . $paymentType;
			$nvpstr = $nvpstr . "&RETURNURL=" . $returnURL;
			$nvpstr = $nvpstr . "&CANCELURL=" . $cancelURL;
			$nvpstr = $nvpstr . "&PAYMENTREQUEST_0_CURRENCYCODE=" . $currencyCodeType;
			$nvpstr = $nvpstr . "&ADDROVERRIDE=1";
			$nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTONAME=" . $shipToName;
			$nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTOSTREET=" . $shipToStreet;
			$nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTOSTREET2=" . $shipToStreet2;
			$nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTOCITY=" . $shipToCity;
			$nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTOSTATE=" . $shipToState;
			$nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE=" . $shipToCountryCode;
			$nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTOZIP=" . $shipToZip;
			$nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTOPHONENUM=" . $phoneNum;
			
			$_SESSION["currencyCodeType"] = $currencyCodeType;	  
			$_SESSION["PaymentType"] = $paymentType;
	
			//'--------------------------------------------------------------------------------------------------------------- 
			//' Make the API call to PayPal
			//' If the API call succeded, then redirect the buyer to PayPal to begin to authorize payment.  
			//' If an error occured, show the resulting errors
			//'---------------------------------------------------------------------------------------------------------------
		    $resArray=$this -> hash_call("SetExpressCheckout", $nvpstr);
			$ack = strtoupper($resArray["ACK"]);
			if($ack=="SUCCESS" || $ack=="SUCCESSWITHWARNING")
			{
				$token = urldecode($resArray["TOKEN"]);
				$_SESSION['TOKEN']=$token;
			}
			   
		    return $resArray;
		}
		
		/*
		'-------------------------------------------------------------------------------------------
		' Purpose: 	Prepares the parameters for the GetExpressCheckoutDetails API Call.
		'
		' Inputs:  
		'		None
		' Returns: 
		'		The NVP Collection object of the GetExpressCheckoutDetails Call Response.
		'-------------------------------------------------------------------------------------------
		*/
		function GetShippingDetails( $token )
		{
			//'--------------------------------------------------------------
			//' At this point, the buyer has completed authorizing the payment
			//' at PayPal.  The function will call PayPal to obtain the details
			//' of the authorization, incuding any shipping information of the
			//' buyer.  Remember, the authorization is not a completed transaction
			//' at this state - the buyer still needs an additional step to finalize
			//' the transaction
			//'--------------------------------------------------------------
		   
		    //'---------------------------------------------------------------------------
			//' Build a second API request to PayPal, using the token as the
			//'  ID to get the details on the payment authorization
			//'---------------------------------------------------------------------------
		    $nvpstr="&TOKEN=" . $token;
	
			//'---------------------------------------------------------------------------
			//' Make the API call and store the results in an array.  
			//'	If the call was a success, show the authorization details, and provide
			//' 	an action to complete the payment.  
			//'	If failed, show the error
			//'---------------------------------------------------------------------------
		    $resArray=$this -> hash_call("GetExpressCheckoutDetails",$nvpstr);
		    $ack = strtoupper($resArray["ACK"]);
			if($ack == "SUCCESS" || $ack=="SUCCESSWITHWARNING")
			{	
				$_SESSION['payer_id'] =	isset($resArray['PAYERID']) ? $resArray['PAYERID'] : 0;
			} 
			return $resArray;
		}
		
		/*
		'-------------------------------------------------------------------------------------------------------------------------------------------
		' Purpose: 	Prepares the parameters for the GetExpressCheckoutDetails API Call.
		'
		' Inputs:  
		'		sBNCode:	The BN code used by PayPal to track the transactions from a given shopping cart.
		' Returns: 
		'		The NVP Collection object of the GetExpressCheckoutDetails Call Response.
		'--------------------------------------------------------------------------------------------------------------------------------------------	
		*/
		function ConfirmPayment( $token, $payer )
		{
			/* Gather the information to make the final call to
			   finalize the PayPal payment.  The variable nvpstr
			   holds the name value pairs
			   */
			
	
			//Format the other parameters that were stored in the session from the previous calls	
			$token 				= $token;
			$payerID 			= $payer;
			$finalAMT 			= urlencode($_SESSION['FinalAmt']);
	
			$serverName 		= urlencode($_SERVER['SERVER_NAME']);
	
			$nvpstr  = '&TOKEN=' . $token . '&PAYERID=' . $payerID . '&PAYMENTREQUEST_0_PAYMENTACTION=' . $this -> paymentType . '&PAYMENTREQUEST_0_AMT=' . $finalAMT;
			$nvpstr .= '&PAYMENTREQUEST_0_CURRENCYCODE=' . $this -> currencyCodeType . '&IPADDRESS=' . $serverName; 
	
			 /* Make the call to PayPal to finalize payment
			    If an error occured, show the resulting errors
			    */
			$resArray=$this -> hash_call("DoExpressCheckoutPayment",$nvpstr);
	
			/* Display the API response back to the browser.
			   If the response from PayPal was a success, display the response parameters'
			   If the response was an error, display the errors received using APIError.php.
			   */
			$ack = strtoupper($resArray["ACK"]);
	
			return $resArray;
		}
		
		/*
		'-------------------------------------------------------------------------------------------------------------------------------------------
		' Purpose: 	This function makes a DoDirectPayment API call
		'
		' Inputs:  
		'		paymentType:		paymentType has to be one of the following values: Sale or Order or Authorization
		'		paymentAmount:  	total value of the shopping cart
		'		currencyCode:	 	currency code value the PayPal API
		'		firstName:			first name as it appears on credit card
		'		lastName:			last name as it appears on credit card
		'		street:				buyer's street address line as it appears on credit card
		'		city:				buyer's city
		'		state:				buyer's state
		'		countryCode:		buyer's country code
		'		zip:				buyer's zip
		'		creditCardType:		buyer's credit card type (i.e. Visa, MasterCard ... )
		'		creditCardNumber:	buyers credit card number without any spaces, dashes or any other characters
		'		expDate:			credit card expiration date
		'		cvv2:				Card Verification Value 
		'		
		'-------------------------------------------------------------------------------------------
		'		
		' Returns: 
		'		The NVP Collection object of the DoDirectPayment Call Response.
		'--------------------------------------------------------------------------------------------------------------------------------------------	
		*/
	
	
		function DirectPayment( $paymentType, $paymentAmount, $creditCardType, $creditCardNumber,
								$expDate, $cvv2, $firstName, $lastName, $street, $city, $state, $zip, 
								$countryCode, $currencyCode )
		{
			//Construct the parameter string that describes DoDirectPayment
			$nvpstr = "&AMT=" . $paymentAmount;
			$nvpstr = $nvpstr . "&CURRENCYCODE=" . $currencyCode;
			$nvpstr = $nvpstr . "&PAYMENTACTION=" . $paymentType;
			$nvpstr = $nvpstr . "&CREDITCARDTYPE=" . $creditCardType;
			$nvpstr = $nvpstr . "&ACCT=" . $creditCardNumber;
			$nvpstr = $nvpstr . "&EXPDATE=" . $expDate;
			$nvpstr = $nvpstr . "&CVV2=" . $cvv2;
			$nvpstr = $nvpstr . "&FIRSTNAME=" . $firstName;
			$nvpstr = $nvpstr . "&LASTNAME=" . $lastName;
			$nvpstr = $nvpstr . "&STREET=" . $street;
			$nvpstr = $nvpstr . "&CITY=" . $city;
			$nvpstr = $nvpstr . "&STATE=" . $state;
			$nvpstr = $nvpstr . "&COUNTRYCODE=" . $countryCode;
			$nvpstr = $nvpstr . "&IPADDRESS=" . $_SERVER['REMOTE_ADDR'];
	
			$resArray=$this -> hash_call("DoDirectPayment", $nvpstr);
	
			return $resArray;
		}
		
		public function hash_call($methodName,$nvpStr){
			//setting the curl parameters.
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$this -> API_Endpoint);
			curl_setopt($ch, CURLOPT_VERBOSE, 1);
	
			//turning off the server and peer verification(TrustManager Concept).
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_POST, 1);
	
			//NVPRequest for submitting to server
			$nvpreq="METHOD=" . urlencode($methodName) . "&VERSION=" . urlencode($this -> version) . "&PWD=" . urlencode($this -> API_Password) . "&USER=" . urlencode($this -> API_UserName) . "&SIGNATURE=" . urlencode($this -> API_Signature) . $nvpStr . "&BUTTONSOURCE=" . urlencode($this -> sBNCode);

			
			//setting the nvpreq as POST FIELD to curl
			curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
	
			//getting response from server
			$response = curl_exec($ch);
	
			//convrting NVPResponse to an Associative Array
			$nvpResArray=$this -> deformatNVP($response);
			$nvpReqArray=$this -> deformatNVP($nvpreq);
			$_SESSION['nvpReqArray']=$nvpReqArray;
	
			if (curl_errno($ch)) 
			{
				// moving to display page to display curl errors
				  $_SESSION['curl_error_no']=curl_errno($ch) ;
				  $_SESSION['curl_error_msg']=curl_error($ch);
	
				  //Execute the Error handling module to display errors. 
			} 
			else 
			{
				 //closing the curl
			  	curl_close($ch);
			}
	
			return $nvpResArray;
		}

		function RedirectToPayPal ( $token )
		{
			// Redirect to paypal.com here
			$payPalURL = $this -> PAYPAL_URL . $token;
			header("Location: ".$payPalURL);
		}
		
		/*'----------------------------------------------------------------------------------
		 * This function will take NVPString and convert it to an Associative Array and it will decode the response.
		  * It is usefull to search for a particular key and displaying arrays.
		  * @nvpstr is NVPString.
		  * @nvpArray is Associative Array.
		   ----------------------------------------------------------------------------------
		  */
		function deformatNVP($nvpstr)
		{
			$intial=0;
		 	$nvpArray = array();
	
			while(strlen($nvpstr))
			{
				//postion of Key
				$keypos= strpos($nvpstr,'=');
				//position of value
				$valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&'): strlen($nvpstr);
	
				/*getting the Key and Value values and storing in a Associative Array*/
				$keyval=substr($nvpstr,$intial,$keypos);
				$valval=substr($nvpstr,$keypos+1,$valuepos-$keypos-1);
				//decoding the respose
				$nvpArray[urldecode($keyval)] =urldecode( $valval);
				$nvpstr=substr($nvpstr,$valuepos+1,strlen($nvpstr));
		     }
			return $nvpArray;
		}
	}
?>	