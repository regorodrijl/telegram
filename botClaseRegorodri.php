<?php
  // Código API BOT:
  // claseRegorodri_bot -> 216807554:AAGDJ3S2b4ZOXshTKxq7IKgfTEzjQF1c-VA
  //  define('BOT_TOKEN','217401414:AAGP0JLg0fVRzngUI2EwzPBKPu2UwR1wq1Q');
  //define('API_URL','https://api.telegram.org/bot'.BOT_TOKEN);
  //define('WEBHOOK_URL', 'https://my-site.example.com/secret-path-for-webhooks/');
  // log de errores ->> /var/log/nginx/
require_once  __DIR__.'/Telegram.php';
//define('BOT_TOKEN','194713131:AAEHuNt0pS6JwEqHYgPrlBLCKeuYE-uYvqw');//bot->@botClasesDaw_bot
define('BOT_TOKEN','200447207:AAFGPqGdWKHCzAHHA43oHbFeTkmpRxG6Nlg');//bot->@regorodri_bot
//echo __DIR__.'/Telegram.php';
$bot = new Telegram(BOT_TOKEN);
// solo se necesita una vez 
//$bot->setWebHook("https://telegram-regorodri.redirectme.net/telegramBot1/botClaseRegorodri.php");
//file_put_contents("debug.txt","hola");
//$bot->getUpdates();


$texto = $bot->getText();

if($local=$bot->getLocation()){
  $apiKey="tuEmHELfpyZIUPmeB61SWQ6chKwruZaafoP6V70N";
  //$bot->sendSMS("https://api.factual.com/geotag?latitude=".$local['latitude']."&longitude=".$local['longitude']."&KEY=".$apiKey);
  $res=file_get_contents("https://api.factual.com/geotag?latitude=".$local['latitude']."&longitude=".$local['longitude']."&KEY=".$apiKey);
  
  file_put_contents("RES.txt",$res);
  $res=json_decode($res);
  //$bot->sendSMS("JSasdasON: ".print_r($res->response->data->locality->name,true));//($res->response->data->locality->name));
  $bot->sendSMS("Estás en: ".$res->response->data->locality->name);
  $bot->sendSMS("Provincia de: ".$res->response->data->county->name);
  $bot->sendSMS("Calle: ".$res->response->data->street_name->name);
  $bot->sendSMS("Código Postal: ".$res->response->data->postcode->name);
}
// $opcion="nada";
switch ($texto) {
  case '/start':
  $bot->sendSMS("Ayuda: \n/start -> Ayuda \n/hora -> Hora actual \n/audio -> Se enviará una canción \n/webcam -> Enviará una foto de Santiago (Aleatoria) \n/aeropuerto -> Coordendas del aeropuerto de santiago \n/mytube -> Enviará un video \n/calle -> Enviará datos de tu locaclización \n/noticias -> Últimas noticias.");
  break;

  case '/hora'://listo
  $bot->sendHora();
  break;

  case '/audio'://listo
  $bot->sendAudio();
  break;

  case '/aeropuerto'://listo
  $latitud=42.891788900;
  $longitud=-8.421176400;
  $bot->sendAeropuerto($latitud,$longitud);
  break;

  case '/webcam'://listo
  $bot->sendWebcam();
  break;

  case '/mytube'://listo
  $bot->sendMyTube();
  break;

  case '/calle':
  $bot->sendCalle();
  break;

  case '/noticias':
  $bot->sendNoticias();
  break;

  case 'Xataka':  
  $bot->sendXataka();  
  break;

  case 'datos': 
  $bot->sendSMS("datos");
  $bot->enviarDatos();  
  break;

  case 'El Pais':  
  $bot->sendElPais();  
  break;

  case 'AS':  
  $bot->sendAs();  
  break;
  // AJUSTES
  case '/ajustes':  
  $bot->sendAjustes();  
  break;

  case "\xF0\x9F\x93\xB0El pais": 
  $bot->sendAjustesMomento("\xF0\x9F\x93\xB0");  
  break;

  case "\xF0\x9F\x8C\x9ETiempo": 
  $bot->sendAjustesMomento("\xF0\x9F\x8C\x9E"); 
  break;

  case "\xF0\x9F\x93\xB0activar/desactivar\xE2\x8F\xB0": 
  $bot->sendActivar("\xF0\x9F\x93\xB0"); 
  break;  
  case "\xF0\x9F\x8C\x9Eactivar/desactivar\xE2\x8F\xB0": 
  $bot->sendActivar("\xF0\x9F\x8C\x9E"); //tiempo
  break;

  case "\xF0\x9F\x93\xB0Mañana": 
  $bot->sendHoraMañana("\xF0\x9F\x93\xB0"); 
  break;  
  case "\xF0\x9F\x8C\x9EMañana": 
  $bot->sendHoraMañana("\xF0\x9F\x8C\x9E"); 
  break;

  case "\xF0\x9F\x93\xB0Tarde":  
  $bot->sendHoraTarde("\xF0\x9F\x93\xB0"); 
  break;
  case "\xF0\x9F\x8C\x9ETarde":  
  $bot->sendHoraTarde("\xF0\x9F\x8C\x9E"); 
  break;

  case "\xF0\x9F\x93\xB0Noche":  
  $bot->sendHoraNoche("\xF0\x9F\x93\xB0"); 
  break;
  case "\xF0\x9F\x8C\x9ENoche":  
  $bot->sendHoraNoche("\xF0\x9F\x8C\x9E"); 
  break;

  default:
  if(preg_match("/\\xF0\\x9F\\x93\\xB0[0-9]{2}:[0-9]{2}$/",$texto)){
    $bot->enviarDatos("elpais",$texto); 
  }else if(preg_match("/\\xF0\\x9F\\x8C\\x9E[0-9]{2}:[0-9]{2}$/",$texto)){  
    $bot->sendSMS("enviado todo ".substr($texto,-5));
    $bot->enviarDatos("telegram",$texto); 
  }else
  if(preg_match("/(.+)/",$texto)){
    //$bot->sendSMS("dentro de regex");
    $bot->sendSMS("Ayuda: \n/start -> Ayuda \n/hora -> Hora actual \n/audio -> Se enviará una canción \n/webcam -> Enviará una foto de Santiago (Aleatoria) \n/aeropuerto -> Coordendas del aeropuerto de santiago \n/mytube -> Enviará un video \n/calle -> Enviará datos de tu locaclización \n/noticias -> Últimas noticias. \n/ajustes -> Configurar.");
  }
  break;
}

?>