public static function notificar_aprobacion_reserva($correo, $nombre, $fecha ,$espacio , $horaInicio, $horaFin,$notaAutorizacion,$confirmacion )
	{
	
			if(Controller::esCorreo($correo))
			{
				Mail::send('emails.notificar_aprobacion_reserva',compact('nombre' , 'fecha','espacio','horaInicio','horaFin','notaAutorizacion','confirmacion') , function($message) use ($correo){
					$message->to($correo)->subject('Notificacion de reserva');
					$message->from('notificaciones@software.tecnovig.com', 'TECNOVIG');
				});
			}
	}
