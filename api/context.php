<?php
    class Context{
        public function __construct(){
            $db_host = "localhost";
            $db_user = "root";
            $db_password = "mysql";
            $db_name = "test";
            /*$db_host = "localhost";
            $db_user = "nuvlar";
            $db_password = "nuvlar";
            $db_name = "chat";
            */
            $this->db = new mysqli($db_host, $db_user, $db_password, $db_name);
            $this->db->autocommit(false);
            if($this->db->connect_errno){
                echo "Error, no se pudo conectar a la base de datos";
                exit;
            }

            $this->params = array();

             $this->response = array();
        }

        public function closeDB(){
            $this->db->commit();
            $this->db->close();
        }

        function getMySQLType($str){
              $str=trim($str);
              return strtoupper(substr($str,0,strpos($str,' ')));
        }

        public function query($query, $die_if_fail = true){
    if ($result = $this->db->query($query))
	  {
      if($this->getMySQLType($query) == 'SELECT')
      {
        $returnarray = array();
        while($row = $result->fetch_assoc())
        {
          array_push($returnarray, $row);
        }
        return $returnarray;
        $result->free();
      }
      elseif($this->getMySQLType($query) == 'INSERT')
      {
        return $this->db->insert_id;
      }
      return true;

    }
    else
    {
      if($die_if_fail)
      {
          $this->error($this->db->error);
          error_log($this->db->error);
          //$this->error("Unknown error, please try again later.");
      }
      else{
        return false;
      }
    }
  }
        // public function error($error = 'Hubo un error desconocido.'){
        //     $response = array();
        //     $response['success'] = '0';
        //     $response['error'] = $error;
        //     echo json_encode($response);
        //     die();
        // }

        // public function salt($length = 10){
        //     $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        //     $charactersLength = strlen($characters);
        //     $randomString = '';
        //     for ($i = 0; $i < $length; $i++) {
        //         $randomString .= $characters[rand(0, $charactersLength - 1)];
        //     }
        //     return $randomString;
        // }

        // public function getUserToken(){
        //     if(isset($_POST['token']))
        //     {
        //         $token_movil= $_POST['token'];  
        //         //error_log("Entro al isset: ".$token_movil); 
        //     }
        //     else
        //     {
        //         error_log("No hay token");
        //         return "Error";
        //     }

        //     if(isset($token_movil)){
        //         $token_array=explode('-',$token_movil);
        //         /*
        //         *$token_id=$token_array[0];
        //         *$token=$token_array[1];
        //         */
        //         return $token_array;
        //     }else{
        //         return "Error";
        //     }

        // }

        // public function getUserId(){
        //     if(isset($_POST['token']))
        //     {
        //         $token_movil= $_POST['token'];  
        //         //error_log("Entro al isset: ".$token_movil); 
        //     }
        //     else
        //     {
        //         error_log("No hay token");
        //         return "Error";
        //     }

        //     if(isset($token_movil)){
        //         $token_array=explode('-',$token_movil);
        //         $token_id=$token_array[0];
        //         $token=$token_array[1];
        //         $query_token= $this->query("SELECT id_usuario, hash, salt FROM tokens WHERE id='".$token_id."'");

        //         //error_log("Consulta getUser: "."SELECT id_usuario, hash, salt FROM tokens WHERE id='".$token_id."'");
        //         if($query_token){

        //             $token_hash=hash('sha256', $query_token[0]['salt']."".$token);
        //             //error_log("Token hash generado: ".$token_hash);
        //             //error_log("Token hash db: ".$query_token[0]['hash']);

        //             if($token_hash==$query_token[0]['hash']){
        //                 return $query_token[0]['id_usuario'];
        //             }
        //             else{
        //                 return "Error";
        //                 error_log("Error in salt comparison matchup");
        //             }
        //         }
        //         else{
        //             return "Error";
        //             error_log("Error obtaining Token Query");
        //         }
        //     }

        // }

        // public function getUserData(){
        //     $user_id = $this->getUserId();
        //     $user_data = $this->query("SELECT * FROM users WHERE id = '".$user_id."'");

        //     if($user_data){
        //         /*
        //         *Accesar asi a los datos
        //         *$user_id = $user_data[0]['id'];
        //         *$username = $user_data[0]['username'];
        //         *$name = $user_data[0]['name'];
        //         *$surname = $user_data[0]['surname'];
        //         *$gender = $user_data[0]['gender'];
        //         *$email = $user_data[0]['email'];
        //         */

        //         return $user_data;              
        //     }else{
        //         return "Error";
        //         error_log('getUserData(): "No se consultaron los datos del usuario correctamente"');
        //     }
        // }
        // //SE RECOMIENDA QUE ESTA FUNCION VAYA EN EL CONTEXT.PHP
        // public function sendFCMPayload($id_user, $payload){
        //     $result = $this->query("SELECT * FROM devices WHERE id_user = '$id_user'");
        //     if(sizeof($result) == 0)
        //     {
        //         return 2;
        //     }
        //     $token = $result[0]['hash'];
        //     $payload['to'] = $token;
        //     $payload['priority'] = "high";
        //     $ch = curl_init('https://fcm.googleapis.com/fcm/send');
        //     # Setup request to send json via POST.
        //     $json_payload = json_encode( $payload );
        //     curl_setopt( $ch, CURLOPT_POSTFIELDS, $json_payload );
        //     # Return response instead of printing.
        //     curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:key=AIzaSyBDcC7qWQEKWaogbecLIAxkL_EnlN7z2kU'));
        //     curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        //     # Send request.
        //     $result = curl_exec($ch);
        //     error_log($result);
        //     curl_close($ch);
        //     return 1;
        // }
    }
?>
