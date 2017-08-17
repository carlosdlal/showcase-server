<!-- <?php
    class user{
        public function signUp($context){
            //Recibir el usuario y contraseña
            $user  = $context->db->real_escape_string($context->params["user"]);
            $first_name = $context->db->real_escape_string($context->params["first_name"]);
            $last_name = $context->db->real_escape_string($context->params["last_name"]);
            $email = $context->db->real_escape_string($context->params["email"]);
            $password  = $context->db->real_escape_string($context->params["password"]);
            $gender= $context->db->real_escape_string($context->params["gender"]);
            
            //Generar la salt
            $salt = $context->salt();
            //Generar el hash concatenando la salt con la contraseña dada
            $pwd_hash = hash('sha256', $salt."".$password);

            error_log("Usuario: ".$user." Password: ".$password." Salt: ".$salt." Hash: ".$pwd_hash);

            //Insertar el usuario en la DB
            $insert = $context->query("INSERT INTO users(username, name, surname, email, password,password_salt, gender) VALUES ('".$user."','".$first_name."','".$last_name."', '".$email."','".$pwd_hash."','".$salt."','".$gender."')");
            if($insert){
                $context->response['success'] = 1;
            }else{
                error_log("INSERT INTO users(username, name, surname, email, password,password_salt, gender) VALUES ('".$user."','".$first_name."','".$last_name."', '".$email."','".$pwd_hash."','".$salt."','".$gender."')");
                $context->response['success'] = 0;
            }
        }

        public function signIn($context){
            $user = $context->db->real_escape_string($context->params["user"]);
            $password  = $context->db->real_escape_string($context->params["password"]);
            $user_data = $context->query("SELECT * FROM users WHERE username = '".$user."'");

            
            error_log("SELECT * FROM users WHERE username = '".$user."'");
            error_log(json_encode($user_data));


            if(sizeof($user_data) > 0){
                //Obtener los datos del usuario
                $salt = $user_data[0]['password_salt'];
                $existent_hash = $user_data[0]['password'];
                $id_user = $user_data[0]['id'];

                $new_pwd_hash_generated = hash('sha256', $salt."".$password);

                error_log($new_pwd_hash_generated);
                error_log($existent_hash);

                if($new_pwd_hash_generated == $existent_hash){

                    //Generar el token
                    $token = $context->salt(50);
                    $token_salt = $context->salt();
                    $token_hash = hash('sha256', $token_salt."".$token);

                    $insert_token = $context->query("INSERT INTO tokens(id_usuario, hash, salt) VALUES ('".$id_user."','".$token_hash."','".$token_salt."')");

                     error_log("INSERT INTO tokens(id_usuario, hash, salt) VALUES ('".$id_user."','".$token_hash."','".$token_salt."')");
                     error_log($insert_token);
                     $context->response['success'] = 1;
                     $context->response['token'] = $insert_token."-".$token;

                }else{
                   $context->response['success'] = 0;
                }                
            }else{
                error_log("No se consultaron los datos del usuario correctamente");
            }
        }

        public function logOut($context){
            if(isset($_POST['params']['movil']) ? true : false){
                //Obtener los datos del usuario (id del token, id_usuario)
                $id_user = $context->getUserId();

                if($id_user == "Error"){
                    $context->response['success'] = 0;
                }else{

                    $token_movil = $context->getUserToken();
                    //Asignar el id_usuario a la variable $usuario
                    $token_id = $token_movil[0];
                    //Asignar el id del token a la variable $token
                    $token = $token_movil[1];

                    //Si los valores de $token_id y $token no son nulos entra al if
                    if(isset($token_id, $token)){
                        //Crear consulta para la eliminación del token en la DB
                        $deleteToken = $context->query("DELETE FROM tokens WHERE id_usuario = $id_user AND id = $token_id");
                        //Si la ejecución de la consulta fué exitosa entra al if
                        if($deleteToken){
                            //Mandar respuestas al viewModel "$sidemenu"
                            $context->response['success'] = 1;

                        }//Si la ejecución de la consulta no fué exitosa entra al else
                        else{
                            //Mandar respuestas al viewModel "$sidemenu"
                            $context->response['success'] = 0;
                        }
                    }//Si los valores de $usuario y $token son nulos entra al else
                    else{
                        //Mandar respuestas al viewModel "$sidemenu"
                        $context->response['success'] = 0;
                    }
                }
            }
        }

        public function contacts($context){
            $id_user = $context->getUserId();
            $obtain_contacts_list = $context->query("SELECT id_user, username, name, surname, gender FROM users INNER JOIN contacts ON contacts.id_user = users.id WHERE contacts.id = '".$id_user."'");
            $context->response['contacts'] = $obtain_contacts_list;
        }

        public function chats($context){
            $id_user = $context->getUserId();
            /*$obtain_last_conversation = $context->query("SELECT u3.id AS contact_id, CONCAT_WS(' ', u3.name, u3.surname) AS contact, u2.name AS sender, messages.message AS message 
                                                        FROM users u
                                                        INNER JOIN messages ON messages.receiver = u.id 
                                                        INNER JOIN users AS u2 ON messages.sender = u2.id 
                                                        INNER JOIN users AS u3 ON IF(messages.receiver = '".$id_user."', messages.sender,messages.receiver) = u3.id
                                                        WHERE (messages.receiver = '".$id_user."' OR messages.sender = '".$id_user."') 
                                                        AND messages.timestamp = (SELECT MAX(timestamp) FROM messages WHERE messages.sender = u.id OR messages.receiver = u.id)");*/


            /*$obtain_last_conversation = $context->query("SELECT u2.id AS contact_id, IF(messages.sender = '".$id_user."', CONCAT_WS(' ', u.name, u.surname), CONCAT_WS(' ', u2.name, u2.surname)) AS contact, u2.name AS sender, messages.message AS message 
                                                         FROM users u
                                                         INNER JOIN messages ON messages.receiver = u.id 
                                                         INNER JOIN users AS u2 ON messages.sender = u2.id 
                                                         WHERE (messages.receiver = '".$id_user."' OR messages.sender = '".$id_user."') 
                                                         AND messages.id = (SELECT MAX(id) FROM messages WHERE messages.sender = '".$id_user."' OR messages.receiver = '".$id_user."')");*/
            //error_log("ID User: ".$id_user." - SELECT u.id AS contact_id, IF(messages.sender = '".$id_user."', CONCAT_WS(' ', u.name, u.surname), CONCAT_WS(' ', u2.name, u2.surname)) AS contact, u2.name AS sender, messages.message AS message FROM users u INNER JOIN messages ON messages.receiver = u.id INNER JOIN users AS u2 ON messages.sender = u2.id WHERE (messages.receiver = '".$id_user."' OR messages.sender = '".$id_user."') AND messages.id = (SELECT MAX(id) FROM messages WHERE messages.sender = '".$id_user."' OR messages.receiver = '".$id_user."')");
            $obtain_last_conversation = $context->query("SELECT id_user AS contact_id, CONCAT_WS(' ', users.name, users.surname) AS contact,(SELECT name from users AS u2 where id = (SELECT m4.sender FROM messages AS m4 WHERE m4.id = (SELECT MAX(m3.id) FROM messages AS m3 WHERE (m3.sender = id_user AND m3.receiver = '".$id_user."')  OR (m3.sender = '".$id_user."' AND m3.receiver = id_user)))) AS sender, messages.message AS message
                                                         FROM users
                                                         INNER JOIN contacts ON contacts.id_user = users.id
                                                         INNER JOIN messages ON messages.id = (SELECT MAX(m3.id) FROM messages AS m3 WHERE (m3.sender = id_user AND m3.receiver = '".$id_user."')  OR (m3.sender = '".$id_user."' AND m3.receiver = id_user))
                                                         WHERE contacts.id = '".$id_user."'
                                                         AND (SELECT COUNT(m1.id) FROM messages AS m1 WHERE (m1.sender = contacts.id_user AND m1.receiver = '".$id_user."') OR (m1.sender = '".$id_user."' AND m1.receiver = contacts.id_user)) > 0");
            $context->response['chats'] = $obtain_last_conversation;

        }

        public function getMessages($context){
           $id_user = $context->getUserId();
           $id_receiver = $context->db->real_escape_string($context->params["id_receiver"]);
           $obtain_all_messages = $context->query("SELECT CONCAT_WS(' ', u2.name, u2.surname) AS contact, CONCAT_WS(' ', u.name, u.surname) AS name, m.message, m.timestamp 
                                                FROM messages AS m
                                                INNER JOIN users AS u2 ON u2.id = '".$id_receiver."'
                                                INNER JOIN users AS u ON m.sender = u.id
                                                WHERE (sender = '".$id_user."' AND receiver = '".$id_receiver."') OR (receiver = '".$id_user."' AND sender = '".$id_receiver."') order by timestamp ASC");
            error_log("ID receiver: ".$id_receiver);
            //error_log("SELECT CONCAT_WS(' ', u2.name, u2.surname) AS contact, u.name, m.message, m.timestamp FROM messages AS m INNER JOIN users AS u2 ON u2.id = '".$id_receiver."' INNER JOIN users AS u ON m.sender = u.id WHERE (sender = '".$id_user."' AND receiver = '".$id_receiver."') OR (receiver = '".$id_user."' AND sender = '".$id_receiver."') order by timestamp ASC");
           $context->response['messages'] = $obtain_all_messages;
        }

        public function getQrData($context){
            $id_user = $context->getUserId();
            $user_data = $context->query("SELECT id, username, name, surname FROM users WHERE id = '".$id_user."'");
            error_log("SELECT id, username, name, surname FROM users WHERE id = '".$id_user."'");
            //$username = $user_data[0]['username'];
            //$name = $user_data[0]['name'];
            //$surname = $user_data[0]['surname'];

            $user_data_array = array(
                'id' => $user_data[0]['id'], 
                'username' => $user_data[0]['username'], 
                'name' => $user_data[0]['name'], 
                'surname' => $user_data[0]['surname']
            );

            //$friend_code = hash('sha256', $id_user."-".$username."-".$name."-".$surname);
            //$friend_code = base64_encode($id_user."-".$username."-".$name."-".$surname);

            //$context->response['friend_code'] = $friend_code;
            $context->response['friend_code'] = $user_data_array;
            
        }

        public function addFriend($context){ 
           $id_user = $context->getUserId(); 
           $friend_code = $context->db->real_escape_string($context->params["friend_code"]); 
 
           $friend_code_array = explode('-',$friend_code); 
           $id_friend = $friend_code_array[0]; 
           $username_friend = $friend_code_array[1]; 
           $name_friend = $friend_code_array[2]; 
           $surname_friend = $friend_code_array[3]; 
 
           $add_friend_contact = $context->query("INSERT INTO contacts(id,id_user) VALUES ('".$id_user."','".$id_friend."')"); 
           $add_friend_user = $context->query("INSERT INTO contacts(id,id_user) VALUES ('".$id_friend."','".$id_user."')"); 
 
            $context->response['success'] = 1; 
            $context->response['friend'] = $name_friend." ".$surname_friend; 
        } 
 
        public function getSideMenuData($context){ 
            $user_data = $context->getUserData(); 
 
            if($user_data[0]['gender'] == "Male"){ 
               $gender = "#2196F3"; 
            }else{ 
               $gender = "#E91E63"; 
            } 
 
            $user_data_array = array( 
                'username' => $user_data[0]['username'],  
                'name' => $user_data[0]['name']." ".$user_data[0]['surname'], 
                'email' => $user_data[0]['email'], 
                'color' => $gender 
            ); 
 
            $context->response['sidemenu'] = $user_data_array; 
        }
        public function pushConversation($context) {
            $id_user = $context->getUserId();
            $id_receiver  = $context->db->real_escape_string($context->params['id_receiver']);
            $message= $context->db->real_escape_string($context->params["message"]);
            error_log("INSERT INTO messages (sender, receiver, message) VALUES ('$id_user','$id_receiver','$message')");
            //$context->error($message);
            $insert_into_conversation= $context->query("INSERT INTO messages (sender, receiver, message) VALUES ('$id_user','$id_receiver','$message')"); 

            $title = "Message from user: ".$id_user;
            $body = $message;
            
            //SEND PUSH NOTIFICATION
            $payload = array(
	            "time_to_live"=>60,
                "notification"=>array(
		            "title"=> $title,
		            "body"=> $body
 	                )
                );
            $context->response['status'] = $context->sendFCMPayload($id_user, $payload);
            $context->response['success'] = 1; 
        }
    }
?> -->