<?php 
/**
* Clase  
* puerto 4443 sftp
*/
// para los errores 
ini_set("display_errors", "1");
error_reporting(E_ALL);
require_once 'error.php';
require_once 'Funciones.php';
require_once 'basedatos.php';
require_once(__DIR__.'/php/autoloader.php');

class Telegram 
{

	private $url;
	private $message_id;
	private $chat_id;
	private $text;
	private $nombre;
	private $apellidos;
	private $lat;
	private $lon;
	private $location;
	public static $opcion;

	public function __construct($token){
		$this->url = 'https://api.telegram.org/bot'.$token;
		$this->getUpdates();
	} 

	public function setWebHook($urlWeb){
		//https://api.telegram.org/bot200447207:AAFGPqGdWKHCzAHHA43oHbFeTkmpRxG6Nlg/setwebhook?url=
		file_get_contents($this->url.'/setwebhook?url='.$urlWeb);
	}

	public function getUpdates(){
		$content = file_get_contents("php://input");
		$update = json_decode($content, true);

		//file_put_contents("datosTel.txt",$content);
		file_put_contents("getUpdate.txt",$update);

		$this->message_id = $update["message"]['message_id'];
		$this->chat_id = $update["message"]['chat']['id'];
		$this->text = $update["message"]['text'];
		$this->nombre = $update["message"]["from"]["first_name"];
		$this->apellidos = $update["message"]["from"]["last_name"];

		$this->lat= $update["message"]["location"]["latitude"];
		$this->lon= $update["message"]["location"]["longitude"];
		$this->location= $update["message"]["location"];
	}
	/**
	 * Getters / Setters
	 * 
	 */
	public function getText() {  
		return $this->text;  
	}   

	public function getLocation() { 
		return $this->location; 
	} 
	public function getLat() { 
		return $this->lat; 
	} 

	/**
 	* Metodo Curl para hacer llamadas a curl
 	* @param  $method -> varibale con el metodo, por el que enviar, sacado de Api telegram.
 	* @param  $datos -> varibale con los datos a enviar. 
 	*/
 	public function CURL($method,$datos) {
 		//$this->sendSMS("empezando curl");
 		$ch=curl_init();
 		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content/Type:multipart/form-data"));
 		curl_setopt($ch, CURLOPT_URL,$this->url.$method);
 		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
 		curl_setopt($ch, CURLOPT_POST,1);
 		curl_setopt($ch, CURLOPT_POSTFIELDS,$datos);
 		curl_exec($ch);
 		curl_close($ch);
 		//$this->sendSMS("fin curl");
 	}

	/**
	 * Ayuda del Bot. 
	 */
	public function sendSMS($texto){  
		file_get_contents($this->url."/sendMessage?chat_id=$this->chat_id&parse_mode=HTML&text=".urlencode($texto));
	}

	/**
	* Envia la hora acutal.
	*/
	public function sendHora(){
		$mensajeHora="Hola $this->nombre $this->apellidos. La fecha y hora actual en Santiago es: ".strftime("%A %e de %B de %Y",time())." y son las ".date("H:i:s");
		file_get_contents($this->url."/sendMessage?chat_id=$this->chat_id&parse_mode=HTML&text=".urlencode($mensajeHora));
	}

	/**
	*Enviará 1 foto al azar indicando de que webcam se trata de 5 webcams preconfiguradas que obtendréis de 
	*(http://www.crtvg.es/crtvg/camaras-web)
	*/
	public function sendWebcam(){
		//$this->sendSMS("Se mandará una foto al azar de Santiago de Compostela!");
		
		$foto= array("http://85.91.64.26/quintana/readImage.asp","http://85.91.64.19/praterias/readImage.asp","http://85.91.64.26/obradoiro/readImage.asp","http://85.91.64.19/catedralc/readImage.asp","http://85.91.64.19/santiago/readImage.asp");
		$fotoAleatoria = array_rand($foto);

		//file_put_contents('webCam.jpg',$foto1);
		//file_put_contents("./foto/urlImagen.txt", $foto[$fotoAleatoria]);
		file_put_contents('./foto/webCam.jpg',file_get_contents($foto[$fotoAleatoria]));

		$rutaRealFichero=realpath("./foto/webCam.jpg");
		$post=array(
			'chat_id'=>$this->chat_id,
			'photo'=> new CURLFile($rutaRealFichero)
			);

		$this->CURL("/sendPhoto",$post);
		//$this->sendSMS("Foto Enviada, ;)!");
	}

	/**
	 * Enviará 1 canción al azar de 3 que tendrá disponibles.
	 */
	public function sendAudio(){
		//$this->sendSMS("Se mandará una canción al azar de 3! enjoy it!!");
		//Enviará 1 canción al azar de 3 que tendrá disponibles.
		$cancion = array("champions.mp3","tuEnemigo.mp3","maps.mp3");
		$claves_aleatorias = array_rand($cancion);
		$this->sendSMS("Título $cancion[$claves_aleatorias]");

		$rutaRealFichero=realpath("./audio/$cancion[$claves_aleatorias]");
		$array=array(
			'chat_id'=>$this->chat_id,
			'audio'=> new CURLFile($rutaRealFichero)
			);

		$this->CURL("/sendAudio",$array);
	}

	/**
	 *Enviará las coordenadas del Aeropuerto de Santiago
	 *42°53′47″N 8°24′55″O
	 *		LAT, 		  LON
	 *(42.891788900, -8.421176400)
	 */
	public function sendAeropuerto($latitud,$longitud){
		//$this->sendSMS("Se enviará las coordenadas del aeropuerto de Santiago.");

		file_get_contents($this->url."/sendLocation?chat_id=$this->chat_id&latitude=$latitud&longitude=$longitud");

		//$this->sendSMS("Coordenadas Enviadas!");
	}

	/**
	 *Enviará un video que tengamos almacenado en el servidor
	 */
	public function sendMyTube(){
		$this->sendSMS("Se enviará un video.");

		$rutaRealFichero=realpath("./video/muse.mp4");//stressedOut.mp4");

		$array=array(
			'chat_id'=>$this->chat_id,
			'video'=> new CURLFile($rutaRealFichero)
			);

		$this->CURL("/sendVideo",$array);
		$this->sendSMS("Video Enviado!");
	}

	/**
	 * Mostrará los últimos Titulares de El Pais (http://ep00.epimg.net/rss/tags/ultimas_noticias.xml) Se facilita librería de RSS en: http://simplepie.org/
	 */
	public function sendElPais(){ //retocar
		$this->sendSMS("Últimas noticias del diario EL PAIS:");

		$elPais="http://ep00.epimg.net/rss/tags/ultimas_noticias.xml";
		$feed = new SimplePie();
		$feed->set_feed_url($elPais);
		$feed->init();
		foreach ($feed->get_items() as $item) {
			$tit=$item->get_title();
			//$des=$item->get_description();
			$link=$item->get_link();
			$this->sendSMS($tit."\n".$link);
		}
		$this->sendSMS("fin el pais");
	}

	/**
	 * Seleccion en teclado de lo que quieres leer
	 */
	public function sendNoticias(){ //retocar
		$teclado=array(
			'keyboard'=>[ ["El Pais","Xataka"],["AS"] ],
			'one_time_keyboard' => true
			);
		$teclado=json_encode($teclado);

		$post = array(
			'chat_id'   => $this->chat_id,
			'text' => "Que queires leer!!",
			'parse_mode' => 'HTML',
			'reply_markup' => $teclado
			);
		$this->CURL("/sendMessage",$post);

	}

	/**
 	* Mostrará los últimos Titulares de Xataka
 	*/
 	public function sendXataka(){
 		$this->sendSMS("Últimas noticias de Xataka:");
 		$xataka="http://feeds.weblogssl.com/xataka2";
 		$feed = new SimplePie();
 		$feed->set_feed_url($xataka);
 		$feed->init();
 		foreach ($feed->get_items() as $item) {
 			$tit=$item->get_title();
			//$des=$item->get_description();
 			$link=$item->get_link();
 			$this->sendSMS($tit."\n".$link);
 		}
 	}
 	
 	/**
 	* Mostrará los últimos Titulares de AS
 	*/
 	public function sendAs(){
 		$this->sendSMS("Últimas noticias del AS:");
 		$as="http://as.com/rss/tags/ultimas_noticias.xml";
 		$feed = new SimplePie();
 		$feed->set_feed_url($as);
 		$feed->init();
 		foreach ($feed->get_items() as $item) {
 			$tit=$item->get_title();
			//$des=$item->get_description();
 			$link=$item->get_link();
 			$this->sendSMS($tit."\n".$link);
 		}
 	}

	/**
	 * Abrirá un teclado con el botón Enviar Mi Localización. Devolverá el nombre de la calle, código postal, etc.. de dónde nos encontremos. (API a utilizar https://www.factual.com/products/geotag )
	*/
	public function sendCalle(){
		//$this->sendSMS("Hola");

		$btn = new stdClass();
		$btn->text = "Enviar Mi Localización";
		$btn->request_location = true;

		$teclado=array(
			'keyboard'=>[ [$btn] ],
			'one_time_keyboard' => true
			);
		$teclado=json_encode($teclado);
		$post = array(
			'chat_id'   => $this->chat_id,
			'text' => "Envíame tu ubicación!",
			'parse_mode' => 'HTML',
			'reply_markup' => $teclado
			);
		$this->CURL("/sendMessage",$post);		
	}

	/**
	 * Enviar datos a BD
	 */
	public function enviarDatos($tabla,$hora){
		$gestion = new Funciones;
		//$pdo = BaseDatos::getInstancia();//instancia de la BD.
		$nombre=$this->nombre." ".$this->apellidos;
		$hora=substr($hora,-5);
		$activo=true;
		$this->sendSMS("Se enviarán tus datos: ".$nombre." "." ".$this->chat_id. "\n hora: ".$hora." tabla ".$tabla);
		$gestion->datos($tabla,$nombre,$this->chat_id,$hora,$activo);
		$this->sendSMS("Fin Insercion.");
	}
	/**
	 * mandar para desactivar o activar
	 */
	public function sendActivar($op){
		$this->sendSMS($op);
		$tabla="";
		if($op=="\xF0\x9F\x8C\x9E"){
			//$this->sendSMS("EL TIEMPO");
			$tabla="telegram";
		}else{
			$tabla="elpais";
			//$this->sendSMS("PAIS");
		}
		$gestion = new Funciones;
		//$pdo = BaseDatos::getInstancia();//instancia de la BD.
		$this->sendSMS("Se enviarán tus datos: ".$nombre." ".$this->apellidos." ".$this->chat_id. "\n TU OPCION ES:".$this->opcion);
		//$this->activar($tabla);
		$gestion = $gestion->activar($tabla,$this->chat_id);
		$this->sendSMS("Fin.");
	}

	public function sendAjustes(){
		$teclado=array(
			'keyboard'=>[ ["\xF0\x9F\x93\xB0El pais","\xF0\x9F\x8C\x9ETiempo"] ],
			'one_time_keyboard' => true
			);
		$teclado=json_encode($teclado);

		$post = array(
			'chat_id'   => $this->chat_id,
			'text' => "Que queires Configurar!!",
			'parse_mode' => 'HTML',
			'reply_markup' => $teclado
			);
		$this->CURL("/sendMessage",$post);
	}
	/**
	 * Ajustes MOMENTO DEL DIA
	 *
	 */
	public function sendAjustesMomento($op){		
		$teclado=array(
			'keyboard'=>[ [$op."Mañana",$op."Tarde",$op."Noche"],[$op."activar/desactivar\xE2\x8F\xB0"] ],
			'one_time_keyboard' => true
			);
		$teclado=json_encode($teclado);

		$post = array(
			'chat_id'   => $this->chat_id,
			'text' => "Elige un momento del día!!",
			'parse_mode' => 'HTML',
			'reply_markup' => $teclado
			);
		$this->CURL("/sendMessage",$post);
	}
	/**
	 * Gestion de las horas
	 * 
	 */
	// MAÑANA
	public function sendHoraMañana($op){
		$teclado=array(
			'keyboard'=>[ [$op."07:00",$op."08:00",$op."09:00"],
			[$op."10:00",$op."11:00",$op."12:00"] ],
			'text'=> "$op",
			'one_time_keyboard' => true
			);
		$teclado=json_encode($teclado);

		$post = array(
			'chat_id'   => $this->chat_id,
			'text' => "Elige una hora de la mañana!!",
			'parse_mode' => 'HTML',
			'reply_markup' => $teclado
			);
		$this->CURL("/sendMessage",$post);
	}
	// TARDE
	public function sendHoraTarde($op){
		$teclado=array(
			'keyboard'=>[ [$op."13:00",$op."14:00",$op."15:00"],
			[$op."16:00",$op."17:00",$op."18:00"] ],
			'one_time_keyboard' => true
			);
		$teclado=json_encode($teclado);

		$post = array(
			'chat_id'   => $this->chat_id,
			'text' => "Elige una hora de la tarde!!",
			'parse_mode' => 'HTML',
			'reply_markup' => $teclado
			);
		$this->CURL("/sendMessage",$post);
	}
	// NOCHE
	public function sendHoraNoche($op){
		$teclado=array(
			'keyboard'=>[ [$op."19:00",$op."20:00",$op."21:00"],
			[$op."22:00",$op."23:00",$op."00:00"] ],
			'one_time_keyboard' => true
			);
		$teclado=json_encode($teclado);

		$post = array(
			'chat_id'   => $this->chat_id,
			'text' => "Elige una hora de la noche!!",
			'parse_mode' => 'HTML',
			'reply_markup' => $teclado
			);
		$this->CURL("/sendMessage",$post);
	}
}
?>