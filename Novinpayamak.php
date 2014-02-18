<?php
/*
  Novin Payamak
  http://www.Novinpayamak.com

  Copyright (c) 2012 Ahmah Rajabi, www.Novinpayamak.com
  
  SMS Plugin for http://freer.ir/virtual, Copyright (c) 2011 Mohammad Hossein Beyram, freer.ir

  The virtual_freer is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v3 (http://www.gnu.org/licenses/gpl-3.0.html)
  as published by the Free Software Foundation.
*/
	//-- اطلاعات کلی پلاگین
	$pluginData[Novinpayamak][type] = 'notify';
	$pluginData[Novinpayamak][name] = 'پیامک محصول';
	$pluginData[Novinpayamak][uniq] = 'Novinpayamak';//www.novinpayamak.com
	$pluginData[Novinpayamak][description] = 'ارسال اطلاعات خرید به موبایل کاربر';
	$pluginData[Novinpayamak][author][name] = 'Ahmad';
	$pluginData[Novinpayamak][author][url] = 'http://www.novinpayamak.com';
	$pluginData[Novinpayamak][author][email] = 'Ahmad@rajabi.us';
	
	//-- فیلدهای تنظیمات پلاگین
	$pluginData[Novinpayamak][field][config][1][title] = 'شماره ارسال';
	$pluginData[Novinpayamak][field][config][1][name] = 'sender_number';
	$pluginData[Novinpayamak][field][config][2][title] = 'کلمه عبور ارسال';
	$pluginData[Novinpayamak][field][config][2][name] = 'password';
	
	//-- تابع پردازش و ارسال اطلاعات
	function notify__Novinpayamak($data,$output,$payment,$product,$cards)
	{
		global $db,$smarty;
		$gateway_number  =  $data[sender_number];
		$gateway_pass    =  $data[password];
		$numbers         =  $payment[payment_mobile];  
		if ($output[status] == 1 AND $payment[payment_mobile] AND $cards)
		{
			$sms_text='';
			foreach($cards as $card)
			{
				$sms_text = 'نوع:' . $product[product_title] . "\r\n";
				if($product[product_first_field_title]!="")
					$sms_text .= $product[product_first_field_title] . ':' . $card[card_first_field];
				if($card[card_second_field]!="")
					$sms_text .= "\r\n" . $product[product_second_field_title] . ':' . $card[card_second_field];
				if($card[card_third_field]!="")
					$sms_text .=  "\r\n" . $product[product_third_field_title] . ':' . $card[card_third_field];
		          //WebService Call
		               $client = new SoapClient('http://www.novinpayamak.com/services/SMSBox/wsdl', array('encoding' => 'UTF-8'));
							
					//Send Begin
	                $flash = false;
					$res = $client->Send(
						array(
								'Auth' 	=> array('number' => $gateway_number,'pass' => $gateway_pass),
								'Recipients' => array($numbers),
								'Message' => array($sms_text),
								'Flash' => $flash
							)
	);
                    
                    if ( $res=>Status == 1000 )
	                {
		              //$res will be XML String
		               $output[message] = '<span dir="rtl">پیام تحویل گوشی مقصد داده شد.<br /><br /></span>';
	                }   
	             else 
	             {
		           $output[message] = '<span dir="rtl">پیام تحویل مرکز پیام کوتاه شده ولی وضعیت آن بر گردانده نشده و وضعیت آن نامشخص و نامعلوم است.<br /><br /></span>'.$res=>Status ;
			      }
		    }
		}
	
	}
	