<?php

namespace App\classes;

use PHPMailer\PHPMailer\PHPMailer;
use App\Models\configuraciones\negocio;
use App\Models\sucursales;

/*
EMAIL_HOST= smtp.gmail.com
EMAIL_PORT= 587
EMAIL_USER= j2softwarepos@gmail.com
EMAIL_PASS= klwlrojndptiqbcl
*/

class Email {

    public $email;
    public $nombre;
    public $token;
    public $html;
    public $alerta;
    
    public function __construct($email, $nombre, $token, $password='', $html='')
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
        $this->html = $html;
        $this->password = $password;
    }

    public function enviarConfirmacion():array { //cunado se registra por primera vez
        $host = $_SERVER['HTTP_HOST'];  //app_barber.test, cliente1.app_barber.test, cliente2.app_barber.test
        $cliente = explode('.', $host);

        // create a new object
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $_ENV['EMAIL_HOST'];//'smtp.gmail.com'; 
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls'; //'ssl'; //ENCRYPTION_STARTTLS - PHPMailer::ENCRYPTION_SMTPS; //'ssl' = si la url tiene el candado, si no =  'tls'
        $mail->Port = $_ENV['EMAIL_PORT']; //465; para ssl y 587 para tls
        $mail->Username = $_ENV['EMAIL_USER']; //'julianithox1@gmail.com';
        $mail->Password = $_ENV['EMAIL_PASS']; //'xxxxxxxxxxxxx'; 
    
        $sucursal = sucursales::find('id', id_sucursal());
        $mail->setFrom($_ENV['EMAIL_USER'], $sucursal->negocio);
        $mail->addReplyTo($sucursal->email, $sucursal->negocio);

        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            $this->alerta['error'][] = "Correo electronico: {$this->email} no valido";
            return $this->alerta;
        };
        $mail->addAddress($this->email, $this->nombre);
        $mail->Subject = 'Detalle de la orden';

        // Set HTML
        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        /*$contenido = "<html>
                        <head><meta charset='UTF-8'></head>
                        <p> <strong>Hola {$this->nombre}</strong> Gracias por visitar nuestra tienda: <span>$sucursal->negocio</span> Te Hemos enviado el detalle de tu compra.</p>
                        <p>Si no realizo esta orden, puedes ignorar este mensaje</p>
                    </html>";*/

        $mail->Body = $this->html;
        $mail->addEmbeddedImage(__DIR__ . "/../../public/build/img/$sucursal->logo", 'logo_id');
        //Enviar el mail
        try {
            $mail->send();
            $this->alerta['exito'][] = "Email enviado exitosamente";
        } catch (\Throwable $th) {
            $this->alerta['error'][] = "Error al enviar email. $th->getMessage(), $mail->ErrorInfo";
        }
        return $this->alerta;
    }


    public function enviarInstrucciones() {
        // create a new object
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $_ENV['EMAIL_HOST']; // 'smtp.gmail.com'; ;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls'; // 'ssl';
        $mail->Port = $_ENV['EMAIL_PORT']; //465; 
        $mail->Username = $_ENV['EMAIL_USER']; // 'julianithox1@gmail.com'; 
        $mail->Password = $_ENV['EMAIL_PASS']; // 'ddvcysabiytmkwca'; 
    
        $negocio = negocio::get(1);
        $mail->setFrom($negocio[0]->email);
        $mail->addAddress($this->email, $this->nombre);
        $mail->Subject = 'Reestablece tu password';

        // Set HTML
        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = '<html>';
        $contenido .= "<p><strong>Hola " . $this->nombre .  "</strong> Has solicitado reestablecer tu password, sigue el siguiente enlace para hacerlo.</p>";
        $contenido .= "<p>Presiona aquí: <a href='http://".$cliente[0].".app_barber.test/recuperar?token=" . $this->token . "'>Reestablecer Password</a>";        
        $contenido .= "<p>Si tu no solicitaste este cambio, puedes ignorar el mensaje</p>";
        $contenido .= '</html>';
        $mail->Body = $contenido;

        //Enviar el mail
        $mail->send();
    }
}