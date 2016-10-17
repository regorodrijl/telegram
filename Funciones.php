<?php
require_once 'basedatos.php';

class Funciones{
	function datos($tabla,$nombre,$chat_id,$hora,$activo){
		try{
			$pdo = BaseDatos::getInstancia();//instancia de la BD.

			//preparamos la consulta de insert
			$stmt=$pdo->prepare("select * from $tabla where chat_id=?;");
			//vinculamos los parametros
			$stmt->bindParam(1,$chat_id);
			//$this->sendSMS("CONSULTA:  select * from $tabla where chat_id=$this->chat_id;");
			// Ejecutamos la consulta
			$stmt->execute();
			//$this->sendSMS("SE EJECUTO");
			if($stmt->rowCount() !=0){
				$this->update($tabla,$nombre,$chat_id,$hora,$activo);
				//$this->sendSMS("Datos modificados!");
			}else{
				$this->insertar($tabla,$nombre,$chat_id,$hora);
				//$this->sendSMS("Datos insertados.");
			}
		}catch(PDOException $error){
			return "Error: ".$error->getMessage();
		}
	}
	function activar($tabla,$chat_id){
		$pdo = BaseDatos::getInstancia();

		$stmt=$pdo->prepare("select activo from $tabla where chat_id=?;");
		$stmt->bindParam(1,$chat_id);
		$stmt->execute();
		if($stmt->rowCount() !=0){
			try{
				$stmtUp = $pdo->prepare("update $tabla set activo=false where chat_id=?");
				$stmtUp->bindParam(1,$chat_id);
				$stmtUp->execute();	
			}catch(Exception $e) {
				$this->sendSMS("Error al modififcar: ".$e->getMessage());
			}
		}
	}

	public function insertar($tabla,$nombre,$chat_id,$hora){
		try{
				$pdo = BaseDatos::getInstancia();//instancia de la BD.
			//preparamos la consulta de insert
				$stmt=$pdo->prepare("insert into $tabla (nombre,chat_id,hora) values(?,?,?);");
			//vinculamos los parametros
				$stmt->bindParam(1,$nombre);
				$stmt->bindParam(2,$chat_id);
				$stmt->bindParam(3,$hora);
			// Ejecutamos la consulta
				$stmt->execute();
			}catch(PDOException $error){
				return "Error: ".$error->getMessage();
			}
		}

		function update($tabla,$nombre,$chat_id,$hora,$activo){
			$pdo = BaseDatos::getInstancia();
			try{
				$stmtUp = $pdo->prepare("update $tabla set hora=?,activo=? where chat_id=?");
				$stmtUp->bindParam(1,$hora);
				$stmtUp->bindParam(2,$activo);
				$stmtUp->bindParam(3,$chat_id);
				$stmtUp->execute();	
			}catch(Exception $e) {
				return "Error al modififcar: ".$e->getMessage();
			}
		}
	}
	?>