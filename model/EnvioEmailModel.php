<?php
Class EnvioEmailModel extends Connection{

	private $html;
	private $titulo;
	private $smtp_url;
	private $smtp_email;
	private $smtp_senha;
	private $smtp_porta;
	private $id_usuarios;

	//SMTPSecure TLS = 587 ou SSL = 465
	private function enviarEmail(
        $host, 
        $username, 
        $password, 
        $port, 
        $from, 
        $fromName, 
        $address, 
        $subject, 
        $body, 
        $secure='tls', 
        $debug=false){

	    $mail = new PHPMailer();
	    $mail->Timeout = 10;
		
	    $mail->CharSet = 'UTF-8';
	    $mail->IsSMTP();// Define que a mensagem será SMTP

	    $mail->SMTPAuth = TRUE; // Usa autenticação SMTP? (opcional)
	    $mail->SMTPSecure = $secure;

		$mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
			)
		);

	    //$mail->SMTPAuth 	= false;
		//$mail->SMTPSecure 	= false;

	    $mail->Host = $host; // Endereço do servidor SMTP
	    $mail->Username = $username; // Usuário do servidor SMTP
	    $mail->Password = $password; // Senha do servidor SMTP
	    $mail->Port = $port; // Senha do servidor SMTP

	    $mail->SMTPDebug = $debug;
		//$mail->SMTPSecure 	= 'SSL';	// SSL REQUERIDO pelo GMail
	    // Define o remetente
	    // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

	    $mail->From = $from; // Seu e-mail
	    $mail->FromName = $fromName; // Seu nome
	    // Define os destinatário(s)
	    // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

	    if (is_array($address)) {
			foreach ($address as $key => $value) {
				$mail->AddAddress($value);
			}
		} else {
			$mail->AddAddress($address);
		}

	    $mail->IsHTML(true); // Define que o e-mail será enviado como HTML
	    $mail->Subject = mb_convert_encoding($subject, "UTF-8", "auto"); // Assunto da mensagem
	    $mail->Body = $body;
	    
        try {
            $enviado = $mail->Send();
            // Limpa os destinatários e os anexos
            //$mail->ClearAllRecipients();
            //$mail->ClearAttachments();
            
            // Exibe uma mensagem de resultado
            if ($enviado) {
                return array('success'=>TRUE, 'msg'=>'E-mail enviado com sucesso.');
            } else {
                return array('success'=>FALSE, 'msg'=>$mail->ErrorInfo);
            }
        } catch (Exception $e) {
            return array('success'=>FALSE, 'msg'=>$e->getMessage());
        }
	}

	public function emailRegisterPassword($id_usuarios){

        $class_usuarios = new UsuariosModel();
        $usuario = $class_usuarios->loadId($id_usuarios);

		$class_empresas = new EmpresasModel();
		$empresa = $class_empresas->loadId($usuario['id_empresas']);

        //return array('usuario'=>$usuario, 'empresa'=>$empresa);

		$host = $empresa['smtp'];
		$username = $empresa['smtp_email'];
		$password = $empresa['smtp_senha'];
		$port = $empresa['smtp_porta'];
		$from = $empresa['smtp_email'];
		$fromName = 'Cadastro de senha';

        $link = site_url().'/web-register-password/'.md5($usuario['id_usuarios'].$usuario['email']);

        $body = '<h5>Bem vindo, '.$usuario['nm_pessoa'].'!</h5>';
        $body.= '<p>Segue seu link para cadastrar sua senha para acesso ao sistema.</p>';
        $body.= '<p>Link: <a href="'.$link.'" target="_blank">'.$link.'</a></p>';

        $envio = $this->enviarEmail(
            $host, 
            $username, 
            $password, 
            $port, 
            $from, 
            $from, 
            $usuario['email'], 
            $fromName, 
            $body
        );
        return $envio;
	}

}
?>