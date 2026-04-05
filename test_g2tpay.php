<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://g2tpay.net/integrate/pay");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['apikey' => '141e51b4-f555-4815-9e9b-de9780616a34']));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
echo "RESPONSE:\n";
var_dump($response);
echo "ERROR:\n" . curl_error($ch) . "\n";
