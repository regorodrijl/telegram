<?php 
require_once 'config.php';
class BaseDatos{
	private static $_instancia=false;
	private static $_pdo=false;

	private function __construct(){
		// Cremos la conexion con el servidor
		try{
			self::$_pdo = new PDO('mysql:host='.Config::$dbServidor.';dbname='.Config::$dbBasedatos.';charset=utf8',Config::$dbUsuario,Config::$dbPassword);
			self::$_pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);


		} catch (PDOException $e) {
			die("<br> Error Conexion BD. ".$e->getMessage());		
		}
	}
		//tienen que ser static para poder ser llamado sin tener que instanciar un obj de dicha
	public static function getInstancia(){
			// Comprueba si tenemos una instancia de la clase BaseDatos
			// Si es asi devuelve el objeto PDO(Conexion al Mysql)
			// Si no hay instancia crea un objeto de la clase BaseDatos.
			    // si no es una instancia de la clase basedatos(ella misma) 
			// devuelve un objeto de clase PDO. 

		if (!self::$_instancia instanceof self) {
				self::$_instancia = new self(); // creamos un objeto BD
			}
			return self::$_pdo;
		}
		private function __clone(){
			trigger_error('Clonación de este objeto no está permitido',E_USER_ERROR);
		}

		public static function close(){
			if (self::$_instancia) {
				self::$_pdo=null;
				self::$_instancia=flase;
			}
		}
	}
	?>