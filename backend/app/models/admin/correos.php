<?php
Load::lib('phpmailer/class.phpmailer');
Load::lib('phpmailer/class.smtp');
class Correos {

    protected $_mail = NULL;
    protected $_error = 'error';

    public function __construct() {
        $this->_mail = new PHPMailer();
        $this->_mail->IsSMTP();
        
        $this->_mail->SMTPAuth = TRUE;
//        $this->_mail->SMTPSecure = 'ssl';
        
        $this->_mail->Host =   Config::get('backend.email.server');
        $this->_mail->Port = Config::get('backend.email.port');
        $this->_mail->Username = Config::get('backend.email.user');//escribir el correo
        $this->_mail->Password =Config::get('backend.email.password');//escribir la clave
        $this->_mail->From = Config::get('backend.email.user'); //escribir el remitente
        $this->_mail->FromName = Config::get('backend.email.from');
    }

    /**
     * Envia un correo de registro exitoso al usuario.
     * 
     * @param  Usuarios $usuario 
     * @return boolean        
     */
    public function enviarRegistro(Usuarios $usuario, $clave, $hash) {
        ob_start();
        View::partial('email/registro', NULL, 
             array(
                'user' => $usuario,
                'clave'=>$clave,
                'hash'=> $hash
             )
         );
        $mensaje = ob_get_clean(); 
        $this->_mail->Subject = "Tu cuenta ha sido creada con exito - " . Config::get('backend.app.nombre');
        $this->_mail->AltBody = strip_tags($mensaje);
        $this->_mail->MsgHTML($mensaje);
        $this->_mail->IsHTML(TRUE);
        $this->_mail->AddAddress($usuario->email, $usuario->nombres);
        return $this->_enviar();
    }

    protected function _enviar(){
        ob_start();
        $res = $this->_mail->Send();
        ob_clean();
        $this->_error = $this->_mail->ErrorInfo;
        return $res;
    }
    
    /**
     *Retorna el ultimo error  
     */
    function getError(){
        return $this->_error;
    }

}
