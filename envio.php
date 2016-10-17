<?php
require_once 'basedatos.php';
require_once(__DIR__.'/php/autoloader.php');
$pdo = BaseDatos::getInstancia();
$url='https://api.telegram.org/bot200447207:AAFGPqGdWKHCzAHHA43oHbFeTkmpRxG6Nlg';

//echo date("G:i")."<br>";
/*
El tiempo
 */
$stmtTiempo =$pdo->prepare("select * from telegram;");
$stmtTiempo ->execute();
while ($fila = $stmtTiempo->fetch()) {
	//echo $fila['hora']."<br>";
	$hora=$fila['hora'];
	$activo=$fila['activo'];

	//echo substr($hora,0,5);
	if(substr($hora,0,5)==date("H:i") && $activo==1){
		$id=$fila['chat_id'];
		echo $fila['nombre']." ".$fila['chat_id']." ".$fila['activo']."<br>\n";
		$datosSinParsear=file_get_contents('http://api.wunderground.com/api/471cbfef3c442340/conditions/forecast/lang:SP/q/ES/Santiago.json');
		$datosParseados=json_decode($datosSinParsear);

		$prevision= $datosParseados->forecast->txt_forecast->forecastday;//->date;

		foreach ($prevision as $value) {
			$dia = $value->title;
			$texto = $value->fcttext_metric;
			$icono = $value->icon_url;
			file_get_contents($url."/sendMessage?chat_id=$id&parse_mode=HTML&text=".urlencode($dia." ".$texto));
		}
	}else echo "El Tiempo: Enviará solo si coincide la hora<br>";
}
/*
ENVIAR A EL PAIS
 */

$stmtPais= $pdo->prepare("select * from elpais;");
$stmtPais->execute();
while ($fila = $stmtPais->fetch()) {
	$id=$fila['chat_id'];
	$hora=$fila['hora'];
	$activo=$fila['activo'];
	if(substr($hora,0,5)==date("H:i")&& $activo==1){
		$elPais="http://ep00.epimg.net/rss/tags/ultimas_noticias.xml";
		$feed = new SimplePie();
		$feed->set_feed_url($elPais);
		$feed->init();
		$noticias;

		foreach ($feed->get_items() as $item) {
			$tit=$item->get_title();
			//$des=$item->get_description();
			$link=$item->get_link();
			$noticias=$tit."\n".$link;
		//file_get_contents($url."/sendMessage?chat_id=$id&parse_mode=HTML&text=".urlencode($noticias));
		}
	}else echo "El País: Enviará solo si coincide la hora";
}
?>