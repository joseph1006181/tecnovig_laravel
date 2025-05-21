<?php
namespace App\Http\Controllers;
USE App\Http\Controllers\Controller;
use PDF;
use DB;
use Mail; 
class mensajes extends Controller
{
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


	public static function autorizacion_salida($id)
	{
		$autorizacion =  DB::select("SELECT usuario.nombre as autoriza, cliente_residente.area as area_id, cliente_residente.cedula, cliente_residente.nombre as residente, cliente_area.descripcion as area, cliente_residente_autorizacion_salida.id, cliente_residente_autorizacion_salida.fecha, cliente_residente_autorizacion_salida.hora_salida, cliente_residente_autorizacion_salida.motivo,cliente_residente_autorizacion_salida.adjunto from usuario,  cliente_residente, cliente_area, cliente_residente_autorizacion_salida where usuario.id=cliente_residente_autorizacion_salida.autoriza and cliente_residente_autorizacion_salida.residente=cliente_residente.id and cliente_residente.area=cliente_area.id and cliente_residente_autorizacion_salida.id= $id order by cliente_residente_autorizacion_salida.id desc ")[0];
		$interesados = DB::select("SELECT usuario.nombre, usuario.correo from usuario, cliente_rol_area where cliente_rol_area.area=$autorizacion->area_id and usuario.cliente_rol=cliente_rol_area.rol");
		foreach($interesados as $usuario)
		{
			if(Controller::esCorreo($usuario->correo))
			{
				Mail::send('emails.autorizacion_salida_estudiante_aleman', compact('autorizacion'), function($message) use ($usuario, $autorizacion){
					$message->to($usuario->correo)->subject('Salida autorizada '. $autorizacion->residente);
					// $message->to('marcoferzap@gmail.com')->subject('Salida autorizada '. $autorizacion->residente);
					$message->from('notificaciones@software.tecnovig.com', 'TECNOVIG');
				});
			}
		}
	}
	public static function enviar_autorizaciones()
	{
        $residentes = DB::select("SELECT cliente_residente.nombre, cliente_residente.correo, cliente_residente.id from cliente_residente where cliente_residente.contacto_correo = 0 and contacto_correo_enviado = 0 and correo !='' and correo like '%@%' and correo like '%.%' and estado = 1 limit 100 ");
		foreach($residentes as $residente)
		{
			if(Controller::esCorreo($residente->correo))
			{
				Mail::send('emails.autorizacion_correo', compact('residente'), function($message) use ($residente){
					$message->to($residente->correo)->subject('Autorización de correo');
					$message->from('notificaciones@software.tecnovig.com', 'TECNOVIG');
				});
			}
			DB::table('cliente_residente')->where('id', $residente->id)->update(['contacto_correo_enviado' => 1, 'contacto_correo_fecha'=>date("Y-m-d H:i:s")]);
		}
	}
	public static function enviar_autorizacion($id)
	{
        $residentes = DB::select("SELECT cliente_residente.nombre, cliente_residente.correo, cliente_residente.id from cliente_residente where id=$id and correo !='' and correo like '%@%' and correo like '%.%' and estado = 1");
		foreach($residentes as $residente)
		{
			if(Controller::esCorreo($residente->correo))
			{
				Mail::send('emails.autorizacion_correo', compact('residente'), function($message) use ($residente){
					$message->to($residente->correo)->subject('Autorización de correo');
					$message->from('notificaciones@software.tecnovig.com', 'TECNOVIG');
				});
			}
			DB::table('cliente_residente')->where('id', $residente->id)->update(['contacto_correo_enviado' => 1, 'contacto_correo_fecha'=>date("Y-m-d H:i:s")]);
		}
	}
	function autorizaciones()
    {
        echo "<script>alert('Sus preferencias han sido guardadas.'); window.close() </script>";
    }
	function autorizaciones_contacto($accion, $id)
    {
        if($accion == "rechazar")
        {
            DB::table('cliente_residente')->where('id', base64_decode($id))->update(['contacto_correo' => 2]);
        }else{
            DB::table('cliente_residente')->where('id', base64_decode($id))->update(['contacto_correo' => 1]);
        }
		return redirect('/wapi/autorizaciones');
    }
   
	public static function intruso_aleman()
	{
		$config = DB::table('config_envio')->where('envio', 'envio_requerimiento')->get()[0];
		Mail::send('emails.send', compact('config'), function($message){
		    $message->to('marcoferzap@gmail.com')->subject('Intruso API Colegio Alemán: '. Controller::IP());
		    $message->from('notificaciones@software.tecnovig.com', 'TECNOVIG');
		});
	}
	public static function enviar_indicador_mensual($archivo)
	{
		Mail::send('emails.indicador_mensual_operaciones', [], function($message) use ($archivo){
		    $message->to('administracion@vigsecol.com')->subject('Indicador mensual gestión operativa');
		    $message->to('gerencia@vigsecol.com')->subject('Indicador mensual gestión operativa');
		    $message->to('sig@vigsecol.com')->subject('Indicador mensual gestión operativa');
		    $message->to('operaciones@vigsecol.com')->subject('Indicador mensual gestión operativa');
		    $message->to('marcoferzap@gmail.com')->subject('Indicador mensual gestión operativa');
			foreach($archivo as $a){
				$message->attach($a);
			}
		    $message->from('notificaciones@software.tecnovig.com', 'TECNOVIG');
		});
	}
	public static function notificaciones_no_vistas($correo, $nombre, $cantidad_notificaciones)
	{
		if(Controller::esCorreo($correo))
		{
			/// cuando se vayan a usar los botones se usará un parametro por get llamado sw que va con el id de la sesión encriptado 2 veces en base64_encode
			Mail::send('emails.notificaciones_no_vistas', compact('cantidad_notificaciones', 'nombre'), function($message) use($correo){
				$message->to($correo)->subject('NOTIFICACIONES POR REVISAR 🔔🔔');
				$message->to('notificaciones@tecnovig.com')->subject('NOTIFICACIONES POR REVISAR 🔔🔔');
				$message->from('notificaciones@software.tecnovig.com', 'TECNOVIG');
			});
		}
	}
	public static function notificacion($array, $usuarios)
	{
		$contenido = $array['descripcion'].$array['descripcion_adicional'];
		foreach($usuarios as $u)
		{
			Mail::send('emails.notificacion', compact('contenido', 'array', 'u'), function($message) use($u){
				if(Controller::esCorreo($u->correo))
				{
					$message->to($u->correo)->subject('🔔 NOTIFICACIÓN NUEVA 1️⃣');
				} 
				$message->to('notificaciones@tecnovig.com')->subject('🔔 NOTIFICACIÓN NUEVA 1️⃣');
				$message->from('notificaciones@software.tecnovig.com', 'TECNOVIG');
			});
		}
	}
	public static function vehiculos_pasados_tiempo($vehiculos_residentes, $vehiculos_visitantes, $cliente)
	{
		foreach($vehiculos_residentes as $key => $v)
		{
			if(!$v->pasado){
				unset($vehiculos_residentes[$key]);
			}
		}
		foreach($vehiculos_visitantes as $key => $v)
		{
			if(!$v->pasado){
				unset($vehiculos_visitantes[$key]);
			}
		}
		$config = DB::table('config_envio')->where('envio', 'limite_tiempo_estacionamiento')->get()[0];

		$fecha = date("Y-m-d");
		$ultimo_mensaje_enviado = DB::table("mensaje_enviado")->where('asunto', $config->asunto)->where('fecha', 'like', "$fecha %")->get();
		if(empty($ultimo_mensaje_enviado[0]))
		{
			Mail::send('emails.limite_tiempo_estacionamiento', compact('cliente', 'vehiculos_residentes', 'vehiculos_visitantes'), function($message) use($cliente, $config){
				$message->to($cliente->correo)->subject($config->asunto);
				$message->to('operaciones@vigsecol.com')->subject($config->asunto);
				$message->from('notificaciones@software.tecnovig.com', 'TECNOVIG');
			});
			$id = Controller::AI('mensaje_enviado');
			DB::table('mensaje_enviado')->insert([
				'id'=>$id,
				'tipo'=>"c",
				'asunto'=>$config->asunto,
				'contenido'=>$config->contenido_correo,
				'usuario'=>"",
				'correo'=>$cliente->correo,
				'respuesta'=>"Ok",
			]);
		}
	}
	public static function panico($id_cliente)
	{
		$cliente = DB::table('cliente')->where('id', $id_cliente)->get()[0];
		Mail::send('emails.panico', compact('cliente'), function($message) use($cliente){
		    $message->to('marcoferzap@gmail.com')->subject("🚨🚨 PÁNICO 🚨🚨");
		    $message->to('operaciones@gmail.com')->subject("🚨🚨 PÁNICO 🚨🚨");
		    $message->to('analistanorte@gmail.com')->subject("🚨🚨 PÁNICO 🚨🚨");
		    $message->to('analistasur@gmail.com')->subject("🚨🚨 PÁNICO 🚨🚨");
		    $message->to('control@gmail.com')->subject("🚨🚨 PÁNICO 🚨🚨");
		    $message->from('notificaciones@software.tecnovig.com', 'TECNOVIG');
		});
	}
	public static function respuesta_solicitud($solicitud, $responde)
	{
		if(Controller::esCorreo($solicitud->correo))
		{
			Mail::send('emails.respuesta_solicitud', compact('solicitud', 'responde'), function($message) use($responde, $solicitud){
				$message->to($solicitud->correo)->subject("Solicitud No. $solicitud->id");
				$message->from('notificaciones@software.tecnovig.com', 'TECNOVIG');
				if($solicitud->respuesta_anexo)
				{
					$message->attach($solicitud->respuesta_anexo);
				}
			});
		}
	}
	public static function nueva_solicitud_personal($solicitud, $responsables)
	{
		Mail::send('emails.nueva_solicitud_personal', compact('solicitud'), function($message) use($responsables, $solicitud){
			if(Controller::esCorreo($solicitud->correo))
			{
				$message->to($solicitud->correo)->subject("Solicitud No. $solicitud->id");
			}
			foreach($responsables as $r)
			{
				if(Controller::esCorreo($r->correo))
				{
					$message->to($r->correo)->subject("Solicitud No. $solicitud->id");
				}
			}
		    $message->from('notificaciones@software.tecnovig.com', 'TECNOVIG');
		});
	}
	public static function enviar_consigna($consigna, $pdf_path)
	{
		$config = DB::table('config_envio')->where('envio', 'envio_consigna')->get()[0];
		$contactos = DB::table('cliente_contacto')->where('enviar_consigna', 1)->where('cliente', $consigna->cliente_id)->get();
		$zona = DB::select("SELECT * from zona where id='$consigna->cliente_zona'");
		// $analista = DB::select("SELECT usuario.* from  zona, usuario where zona.analista = usuario.id and zona.id='$consigna->cliente_zona'");
		Mail::send('emails.send', compact('config'), function($message) use($config, $consigna, $pdf_path, $contactos, $zona){
			foreach($contactos as $c)
			{
				if(Controller::esCorreo($c->correo))
				{
					$message->to($c->correo)->subject($config->asunto);
				}
			}
			foreach($zona as $c)
			{
				if(Controller::esCorreo($c->correo))
				{
					$message->to($c->correo)->subject($config->asunto);
				}
			}
		    $message->to('operaciones@vigsecol.com')->subject($config->asunto);
		    $message->to('servicioalcliente@vigsecol.com')->subject($config->asunto);
			$message->attach($pdf_path);
		    $message->from('notificaciones@software.tecnovig.com', $config->remitente);
		});
	}
	public static function notifica_visita_salida($visitante, $residente, $hora_salida)
	{
		$visitante->nombre = "$visitante->nombre1 $visitante->nombre2 $visitante->apellido1 $visitante->apellido2";
		if(Controller::esCorreo($residente->correo))
		{
			Mail::send('emails.notifica_visita_salida', compact('residente', 'visitante'), function($message) use($residente){
				$message->to($residente->correo)->subject("Salida de visitante");
				$message->from('notificaciones@software.tecnovig.com', 'Tecnovig');
			});
		}
		else{
			// dd("no es correo");
		}
	}
	public static function notifica_visita($registro, $residente)
	{
		$residente = DB::table('cliente_residente')->where('id', $residente)->get()[0];
		if($residente->contacto_correo == 1){
			$visitante = DB::select("SELECT cliente_visitante.*, cliente_visitante_registro.observaciones, cliente_visitante_registro.fecha,  cliente_visitante_registro.hora from cliente_visitante, cliente_visitante_registro where cliente_visitante_registro.id=$registro and cliente_visitante_registro.visitante = cliente_visitante.id ")[0];
			$visitante->nombre = "$visitante->nombre1 $visitante->nombre2 $visitante->apellido1 $visitante->apellido2";
			$nueva_ruta = "uploads/clientes/visitante/$visitante->id.png";
			if(file_exists($nueva_ruta))
			{
				DB::table('cliente_visitante')->where('id', $visitante->id)->update(['foto'=>$nueva_ruta]);
				$visitante->foto = $nueva_ruta;
			}
			else{
				if($visitante->foto && $visitante->foto!=$nueva_ruta && file_exists($visitante->foto))
				{
					copy($visitante->foto, $nueva_ruta);
					unlink($visitante->foto);
					$visitante->foto = $nueva_ruta;
					DB::table('cliente_visitante')->where('id', $visitante->id)->update(['foto'=>$nueva_ruta]);
				}
			}
			if(Controller::esCorreo($residente->correo))
			{
				Mail::send('emails.notifica_visita', compact('residente', 'visitante'), function($message) use($residente){
					$message->to($residente->correo)->subject("Ingreso de visitante autorizado");
					// $message->to('marcoferzap@gmail.com')->subject("Ingreso de visitante autorizado");
					$message->from('notificaciones@software.tecnovig.com', 'Tecnovig');
				});
			}
		}
	}
	public static function enviar_solicitud_permiso($solicitud)
	{	
		$config = DB::table('config_envio')->where('envio', 'envio_solicitud_permiso')->get()[0];
		$config_gh = DB::table('conf_gestion_humana')->get()[0];
		Mail::send('emails.send', compact('config'), function($message) use($config, $solicitud){
			if($_SESSION['rol']==2)
			{
				$message->to('programacion@vigsecol.com')->subject($config->asunto);
				$message->to('analistanorte@vigsecol.com')->subject($config->asunto);
				$message->to('analistasur@vigsecol.com')->subject($config->asunto);
			}
		    $message->to('nomina@vigsecol.com')->subject($config->asunto);
		    $message->to('gestionhumana@vigsecol.com')->subject($config->asunto);
		    $message->to('asistentegh@vigsecol.com')->subject($config->asunto);
		    // $message->to('marcoferzap@gmail.com')->subject($config->asunto);
			if(Controller::esCorreo($solicitud->usuario_correo))
			{
				$message->to($solicitud->usuario_correo)->subject($config->asunto);
			}
			$message->attach($solicitud->archivo);
			if($solicitud->anexo)
			{
				$message->attach($solicitud->anexo);

			}
		    $message->from('notificaciones@software.tecnovig.com', $config->remitente);
		});
	}
	public static function enviar_capacitacion($capacitacion, $pdf_path, $participantes)
	{
		$config = DB::table('config_envio')->where('envio', 'envio_capacitacion')->get()[0];
		Mail::send('emails.send', compact('config'), function($message) use($config, $capacitacion, $pdf_path, $participantes){
			foreach ($participantes as $key => $value) {
				if(Controller::esCorreo($value->correo))
				{
		    		$message->to($value->correo)->subject($config->asunto." No. $capacitacion->id");
				}
			}
		    $message->to('gestionhumana@vigsecol.com')->subject($config->asunto." No. $capacitacion->id");
		    $message->to('asistentegh@vigsecol.com')->subject($config->asunto." No. $capacitacion->id");
			$message->attach($pdf_path);
		    $message->from('notificaciones@software.tecnovig.com', $config->remitente);
		});
	}
	public static function enviar_mensaje_requerimiento($id, $pdf_path, $anexos)
	{	
		$config = DB::table('config_envio')->where('envio', 'envio_requerimiento')->get()[0];
		$config->contenido_correo = str_replace('{id}', $id, $config->contenido_correo);
		Mail::send('emails.send', compact('config'), function($message) use($config, $anexos, $pdf_path){
		    $message->to('marcoferzap@gmail.com')->subject($config->asunto);
		    $message->to('ingenieria@vigsecol.com')->subject($config->asunto);
		    $message->to('ingenieria@tecnovig.com')->subject($config->asunto);
			$message->attach($pdf_path);
		    foreach ($anexos as $key => $value) {
				$message->attach($value->ubicacion);
		    }
		    $message->from('notificaciones@software.tecnovig.com', $config->remitente);
		});
	}
	public static function prueba_correo()
	{
		$config = DB::table('config_envio')->where('envio', 'envio_requerimiento')->get()[0];
		Mail::send('emails.send', compact('config'), function($message){
		    $message->to('marcoferzap@gmail.com')->subject('Prueba de correo');
		    $message->from('notificaciones@software.tecnovig.com', 'TECNOVIG');
		});
	}
	public static function enviar_nuevo_pqrs_cliente($pqrs)
	{
		Mail::send('emails.pqrs_cliente', compact('pqrs'), function($message) use($pqrs){
		    $message->to('operaciones@vigsecol.com')->subject("Nuevo PQRS");
		    $message->to('dcomercial@vigsecol.com')->subject("Nuevo PQRS");
		    $message->to('servicioalcliente@vigsecol.com')->subject("Nuevo PQRS");
		    $message->to('gerencia@vigsecol.com')->subject("Nuevo PQRS");
		    $message->to($pqrs->correo)->subject("Nuevo PQRS");
		    $message->to('marcoferzap@gmail.com')->subject("Nuevo PQRS");
		    $message->from('notificaciones@software.tecnovig.com', "Tecnovig");
		});
	}
	public static function enviar_pqrs_cliente($pqrs, $pdf_path)
	{	
		$config = DB::table('config_envio')->where('envio', 'envio_pqrs')->get()[0];
		Mail::send('emails.send', compact('config'), function($message) use($config, $pdf_path, $pqrs){
		    $message->to('operaciones@vigsecol.com')->subject($config->asunto);
		    $message->to('dcomercial@vigsecol.com')->subject($config->asunto);
		    $message->to('servicioalcliente@vigsecol.com')->subject($config->asunto);
		    $message->to('gerencia@vigsecol.com')->subject($config->asunto);
		    $message->to($pqrs->correo)->subject($config->asunto);
			$message->attach($pdf_path);
		    $message->from('notificaciones@software.tecnovig.com', $config->remitente);
		});
	}
	public static function enviar_novedad_cliente($id, $novedad, $archivo)
	{	
		$config = DB::table('config_envio')->where('envio', 'envio_novedad_cliente')->get()[0];
		$cliente = DB::table('cliente')->where('id', $novedad->id_cliente)->get()[0];
		$zona = DB::select("SELECT * from zona where id='$cliente->zona'");
		$contactos = DB::table('cliente_contacto')->where('enviar_novedad_cliente', 1)->where('cliente', $cliente->id)->get();
		Mail::send('emails.send', compact('config'), function($message) use($contactos, $config, $zona, $archivo){
		    foreach ($contactos as $key => $value) {
				if(Controller::esCorreo($value->correo))
				{
		    		$message->to($value->correo)->subject($config->asunto);
				}
			}
			if((!empty($zona[0])))
			{
				$message->to($zona[0]->correo)->subject($config->asunto);
			}
			$message->to('operaciones@vigsecol.com')->subject($config->asunto);
			$message->attach($archivo);
		    $message->from('notificaciones@software.tecnovig.com', $config->remitente);

		});
	}
	public static function enviar_novedad_vigsecol($id, $novedad, $archivo)
	{	
		$config = DB::table('config_envio')->where('envio', 'envio_novedad_vigsecol')->get()[0];
		$cliente = DB::table('cliente')->where('id', $novedad->id_cliente)->get()[0];
		$zona = DB::select("SELECT * from zona where id='$cliente->zona'");
		Mail::send('emails.send', compact('config'), function($message) use($config, $zona, $archivo){
			if((!empty($zona[0])))
			{
				$message->to($zona[0]->correo)->subject($config->asunto);
			}
			$message->to('operaciones@vigsecol.com')->subject($config->asunto);
			$message->attach($archivo);
		    $message->from('notificaciones@software.tecnovig.com', $config->remitente);

		});
	}
	public static function pass2contacto($contacto, $pass)
	{
		if(Controller::esCorreo($contacto->correo))
		{
			$config = DB::table('config_envio')->where('envio', 'envio_contrasena_a_contacto_cliente')->get()[0];
			$config->contenido_correo = str_replace('{nombre_contacto}', $contacto->nombre, $config->contenido_correo);
			$config->contenido_correo = str_replace('{contrasena}', $pass, $config->contenido_correo);
			$config->contenido_correo = str_replace('{link}', $_SERVER['SCRIPT_URI'], $config->contenido_correo);
			Mail::send('emails.send', compact('config'), function($message) use($contacto, $config){
			    $message->to($contacto->correo)->subject($config->asunto);
			    $message->from('notificaciones@software.tecnovig.com', $config->remitente);
			});
		}
	}
	public static function mensajes_correspondencia($correspondencia)
	{
		$residente = DB::table('cliente_residente')->where('id', $correspondencia->residente)->get()[0];
		$config = DB::table('config_envio')->where('envio', 'correspondencia')->get()[0];
		$config->contenido_correo = str_replace('{residente}', $residente->nombre, $config->contenido_correo);
		$config->contenido_correo = str_replace('{descripcion}', $correspondencia->descripcion, $config->contenido_correo);
		$nueva_ruta = "uploads/clientes/correspondencia/$correspondencia->id.png";
		if(file_exists($nueva_ruta))
		{
			DB::table('cliente_visitante')->where('id', $correspondencia->id)->update(['foto'=>$nueva_ruta]);
			$correspondencia->foto = $nueva_ruta;
		} 
		else{
			if($correspondencia->foto && $correspondencia->foto!=$nueva_ruta)
			{
				copy($correspondencia->foto, $nueva_ruta);
				unlink($correspondencia->foto);
				$correspondencia->foto = $nueva_ruta;
				DB::table('cliente_residente_correspondencia')->where('id', $correspondencia->id)->update(['foto'=>$nueva_ruta]);
			}
		}
		if(Controller::esCorreo($residente->correo))
		{
			if($residente->contacto_correo == 1)
			{
						Mail::send('emails.send', compact('config'), function($message) use($correspondencia, $config, $residente){
							if (Controller::esCorreo($residente->correo)) {
								$message->to($residente->correo)->subject($config->asunto);
							}
							$message->from('notificaciones@software.tecnovig.com', $config->remitente);
							$message->attach($correspondencia->foto);
						});
				}

				if($_SESSION['id']===455)
				{
						// Mail::send('emails.nofitica_correspondencia', compact('config', 'residente', 'correspondencia'), function($message) use($correspondencia, $config, $residente){
						// 	$message->to($residente->correo)->subject($config->asunto);
						// 	$message->from('notificaciones@software.tecnovig.com', $config->remitente);
						// });
						
				}
				$id = Controller::AI('mensaje_enviado');
				DB::table('cliente_residente_correspondencia')->where('id', $correspondencia->id)->update(['mensaje_correo'=>$id]);
				
			}else{
				DB::table('cliente_residente_correspondencia')->where('id', $correspondencia->id)->update(['mensaje_correo'=>9999999999]);
			}
			DB::table('mensaje_enviado')->insert([
				'id'=>$id,
				'tipo'=>"c",
				'asunto'=>$config->asunto,
				'contenido'=>$config->contenido_correo,
				'usuario'=>"",
				'residente'=>$residente->id,
				'correo'=>$residente->correo,
				'telefono'=>$residente->correo,
				'respuesta'=>"Ok",
			]);
	}
	public static function confirma_entrega_correspondencia($correspondencia)
	{
		$residente = DB::table('cliente_residente')->where('id', $correspondencia->residente)->get()[0];
		$config = DB::table('config_envio')->where('envio', 'correspondencia')->get()[0]; 
		$config->contenido_correo = str_replace('{residente}', $residente->nombre, $config->contenido_correo);
		$config->contenido_correo = str_replace('{descripcion}', $correspondencia->descripcion, $config->contenido_correo);
		$nueva_ruta = "uploads/clientes/correspondencia/$correspondencia->id.png";
		if(file_exists($nueva_ruta))
		{
			DB::table('cliente_visitante')->where('id', $correspondencia->id)->update(['foto'=>$nueva_ruta]);
			$correspondencia->foto = $nueva_ruta;
		}
		else{
			if($correspondencia->foto && $correspondencia->foto!=$nueva_ruta)
			{
				copy($correspondencia->foto, $nueva_ruta);
				unlink($correspondencia->foto);
				$correspondencia->foto = $nueva_ruta;
				DB::table('cliente_residente_correspondencia')->where('id', $correspondencia->id)->update(['foto'=>$nueva_ruta]);
			}
		}
		if($residente->contacto_correo == 1)
		{
		if(Controller::esCorreo($residente->correo))
			{
				Mail::send('emails.nofitica_entrega_correspondencia', compact('config', 'residente', 'correspondencia'), function($message) use($correspondencia, $config, $residente){
					$message->to($residente->correo)->subject("VIGSECOL | CORRESPONDENCIA ENTREGADA");
					$message->from('notificaciones@software.tecnovig.com', $config->remitente);
				});
			}
		}
	}
	public static function reporte_condiciones_inseguras($ubicacion)
	{
		$config = DB::table('config_envio')->where('envio', 'reporte_condiciones_inseguras')->get()[0];
		Mail::send('emails.send', compact('config'), function($message) use($ubicacion, $config){
		    $message->to('sst@vigsecol.com')->subject($config->asunto);
		    $message->to('operaciones@vigsecol.com')->subject($config->asunto);
			$message->from('notificaciones@software.tecnovig.com', $config->remitente);
		   	$message->attach($ubicacion);
		});
	}
	public static function clientes_enviar_visita($ubicacion, $cliente, $usuario)
	{
		$config = DB::table('config_envio')->where('envio', 'clientes_enviar_visita')->get()[0];
		$cliente = DB::table('cliente')->where('id', $cliente)->get()[0];
		$contactos = DB::table('cliente_contacto')->where('enviar_visita_cliente', 1)->where('cliente', $cliente->id)->get();
		$usuario = DB::table('usuario')->where('id', $usuario)->get()[0];
		Mail::send('emails.send', compact('config'), function($message) use($ubicacion, $config, $cliente, $contactos, $usuario){
			foreach ($contactos as $key => $value) {
				if(Controller::esCorreo($value->correo))
				{
		    		$message->to($value->correo)->subject($config->asunto);
				}
			}
			$message->to('operaciones@vigsecol.com')->subject($config->asunto);
			$message->to($usuario->correo)->subject($config->asunto);
			$message->from('notificaciones@software.tecnovig.com', $config->remitente);
		   	$message->attach($ubicacion);
		});
	}
	public static function tecnico_enviar_reporte_tecnico($ubicacion, $cliente, $usuario)
	{
		$config = DB::table('config_envio')->where('envio', 'tecnico_enviar_reporte_tecnico')->get()[0];
		$contactos = DB::table('cliente_contacto')->where('enviar_reporte_tecnico', 1)->where('cliente', $cliente->id)->get();
		$usuario = DB::table('usuario')->where('id', $usuario)->get()[0];
		Mail::send('emails.send', compact('config'), function($message) use($ubicacion, $config, $cliente, $contactos, $usuario){
			// foreach ($contactos as $key => $value) {
			// 	if(Controller::esCorreo($value->correo))
			// 	{
		    // 		$message->to($value->correo)->subject($config->asunto);
			// 	}
			// }
			$message->to($usuario->correo)->subject($config->asunto);
			$message->from('notificaciones@software.tecnovig.com', $config->remitente);
		   	$message->attach($ubicacion);
		});
	}
	public static function supervisor_record_patrullaje($ubicacion, $cliente, $usuario)
	{
		$config = DB::table('config_envio')->where('envio', 'supervisor_record_patrullaje')->get()[0];
		$cliente = DB::table('cliente')->where('id', $cliente)->get()[0];
		// $analista = DB::select("SELECT usuario.* from  zona, usuario where zona.analista = usuario.id and zona.id='$cliente->zona'");
		$zona = DB::select("SELECT * from zona where id='$cliente->zona'");
		$contactos = DB::table('cliente_contacto')->where('enviar_record_patrullaje', 1)->where('cliente', $cliente->id)->get();
		$usuario = DB::table('usuario')->where('id', $usuario)->get()[0];
		Mail::send('emails.send', compact('config'), function($message) use($ubicacion, $config, $cliente, $contactos, $usuario, $zona){
			foreach ($contactos as $key => $value) {
				if(Controller::esCorreo($value->correo))
				{
		    		$message->to(trim($value->correo))->subject($config->asunto);
				}
			}
			if((!empty($zona[0])))
			{
				$message->to($zona[0]->correo)->subject($config->asunto);
			}
			$message->to($usuario->correo)->subject($config->asunto);
			$message->from('notificaciones@software.tecnovig.com', $config->remitente);
		   	$message->attach($ubicacion);
		});
	}
	public static function vigilante_marcacion_punto($ubicacion, $cliente)
	{
		$config = DB::table('config_envio')->where('envio', 'vigilante_marcacion_punto')->get()[0];
		$cliente = DB::table('cliente')->where('id', $cliente)->get()[0];
		$zona = DB::select("SELECT * from zona where id='$cliente->zona'");
		// $analista = DB::select("SELECT usuario.* from  zona, usuario where zona.analista = usuario.id and zona.id='$cliente->zona'");
		$contactos = DB::table('cliente_contacto')->where('enviar_record_patrullaje', 1)->where('cliente', $cliente->id)->get();
		Mail::send('emails.send', compact('config'), function($message) use($ubicacion, $config, $cliente, $contactos, $zona){
			foreach ($contactos as $key => $value) {
				if(Controller::esCorreo($value->correo))
				{
		    		$message->to(trim($value->correo))->subject($config->asunto);
				}
			}
			if((!empty($zona[0])))
			{
				$message->to($zona[0]->correo)->subject($config->asunto);
			}
			$message->from('notificaciones@software.tecnovig.com', $config->remitente);
		   	$message->attach($ubicacion);
		});
	}
}