
	function reservas_espacios(){          

		$id = $_SESSION["cliente"];

		$cliente_residente = DB::select("SELECT * from cliente_residente where cliente = $id   ");

		$espacios = DB::select("SELECT * from cliente_espacio where cliente = $id AND estado != 0  ");
		//$reservas = DB::select("SELECT * from cliente_espacio_reserva where fecha != '12/03/2025' ");
		$reservas = DB::select("SELECT cliente_espacio_reserva.*, 
       cliente_espacio.descripcion, 
	   
       cliente_residente.nombre,
       cliente_residente.correo
  
       FROM cliente_espacio_reserva   
       INNER JOIN cliente_espacio ON cliente_espacio_reserva.espacio = cliente_espacio.id
       INNER JOIN cliente_residente ON cliente_espacio_reserva.residente = cliente_residente.id
        WHERE cliente_espacio_reserva.fecha != '12/03/2025'  AND cliente_espacio_reserva.estado != 0");

		return view('cliente.espacios_comunes', compact('espacios', 'reservas', 'cliente_residente'));

	}


	function crear_reserva() {

		extract($_POST);

		try {

			$fecha = $_POST['fecha'];
			$espacio = $_POST['espacio'];
			$residente = $_POST['residente'];
			$hora_inicio = $_POST['hora_inicio'];
			$hora_fin = $_POST['hora_fin'];
			$observaciones = $_POST['observaciones'];


			$filas_afectadas = DB::affectingStatement("
		 	INSERT INTO cliente_espacio_reserva (fecha, espacio, residente, hora_inicio, hora_fin, observaciones, estado)
		 	SELECT '$fecha' , '$espacio', '$residente', '$hora_inicio', '$hora_fin',' $observaciones', 1
		 	WHERE NOT EXISTS (
		 		SELECT 1
		 		FROM cliente_espacio_reserva
		 		WHERE espacio = '$espacio'
		 		  AND fecha = '$fecha'
		 		  AND (hora_inicio < '$hora_fin' AND hora_fin > '$hora_inicio')  
		 		  AND estado != 0
		 	)
		 ");

			if ($filas_afectadas > 0) {


				echo json_encode(["status" => "success", "message" => "Reserva creada con éxito"]);
			} else {
				echo json_encode(["status" => "error", "message" => "No se pudo crear la reserva, ya existe una en ese rango de horas."]);
			}




		} catch (\Throwable $th) {
			echo json_encode([
				"status" => "error",
				"message" => "No se pudo crear la reserva: " . $th->getMessage()
			]);
		}

	}

function cancelar_reserva(){
		try {
			// Obtener datos del POST
			$correo = $_POST['correo'] ?? null;
			$nombre = $_POST['nombre'] ?? null;
			$fecha = $_POST['fecha'] ?? null;
			$espacio = $_POST['espacio'] ?? null;
			$horaInicio = $_POST['horaInicio'] ?? null;
			$horaFin = $_POST['horaFin'] ?? null;
			$idReserva = $_POST['idReserva'] ?? null;
			$confirmacion = $_POST['confirmacion'] ?? null;
			$notaAutorizacion = $_POST['notaAutorizacion'] ?? null;
			$estado = $_POST['estado'] ?? 1;



			// Validar datos requeridos
			if (!$correo || !$nombre || !$fecha || !$espacio) {
				echo json_encode([
					"status" => "error",
					"message" => "Faltan datos requeridos para enviar la notificación."
				]);
				return;
			}

			// Actualizar estado de confirmación en la base de datos
			DB::table('cliente_espacio_reserva')
				->where('id', $idReserva)
				->update([
					'confirmacion' => $confirmacion,
					'nota_autorizacion' => $notaAutorizacion,
					'estado' => $estado,



				]);

			// Enviar notificación por correo
			mensajes::notificar_aprobacion_reserva(
				$correo,
				$nombre,
				$fecha,
				$espacio,
				$horaInicio,
				$horaFin,

				$notaAutorizacion,
				$confirmacion
			);

			// Respuesta exitosa
			echo json_encode([
				"status" => "success",
				"message" => "Notificación enviada correctamente a $nombre"
			]);
		} catch (\Throwable $th) {
			echo json_encode([
				"status" => "error",
				"message" => "Error al enviar la notificación: " . $th->getMessage()
			]);
		}

	}

 
	function autorizar_reserva() {


		try {
			// Obtener datos del POST
			$correo = $_POST['correo'] ?? null;
			$nombre = $_POST['nombre'] ?? null;
			$fecha = $_POST['fecha'] ?? null;
			$espacio = $_POST['espacio'] ?? null;
			$horaInicio = $_POST['horaInicio'] ?? null;
			$horaFin = $_POST['horaFin'] ?? null;
			$idReserva = $_POST['idReserva'] ?? null;
			$confirmacion = $_POST['confirmacion'] ?? null;
			$notaAutorizacion = $_POST['notaAutorizacion'] ?? null;
			$estado = $_POST['estado'] ?? 1;



			// Validar datos requeridos
			if (!$correo || !$nombre || !$fecha || !$espacio) {
				echo json_encode([
					"status" => "error",
					"message" => "Faltan datos requeridos para enviar la notificación."
				]);
				return;
			}

			// Actualizar estado de confirmación en la base de datos
			DB::table('cliente_espacio_reserva')
				->where('id', $idReserva)
				->update([
					'confirmacion' => $confirmacion,
					'nota_autorizacion' => $notaAutorizacion,
					'estado' => $estado,



				]);

			// Enviar notificación por correo
			mensajes::notificar_aprobacion_reserva(
				$correo,
				$nombre,
				$fecha,
				$espacio,
				$horaInicio,
				$horaFin,

				$notaAutorizacion,
				$confirmacion
			);

			// Respuesta exitosa
			echo json_encode([
				"status" => "success",
				"message" => "Notificación enviada correctamente a $nombre"
			]);
		} catch (\Throwable $th) {
			echo json_encode([
				"status" => "error",
				"message" => "Error al enviar la notificación: " . $th->getMessage()
			]);
		}

	}

 function no_autorizar_reserva()
	{


		try {
			// Obtener datos del POST
			$correo = $_POST['correo'] ?? null;
			$nombre = $_POST['nombre'] ?? null;
			$fecha = $_POST['fecha'] ?? null;
			$espacio = $_POST['espacio'] ?? null;
			$horaInicio = $_POST['horaInicio'] ?? null;
			$horaFin = $_POST['horaFin'] ?? null;
			$idReserva = $_POST['idReserva'] ?? null;
			$confirmacion = $_POST['confirmacion'] ?? null;
			$notaAutorizacion = $_POST['notaAutorizacion'] ?? null;
			$estado = $_POST['estado'] ?? 1;



			// Validar datos requeridos
			if (!$correo || !$nombre || !$fecha || !$espacio) {
				echo json_encode([
					"status" => "error",
					"message" => "Faltan datos requeridos para enviar la notificación."
				]);
				return;
			}

			// Actualizar estado de confirmación en la base de datos
			DB::table('cliente_espacio_reserva')
				->where('id', $idReserva)
				->update([
					'confirmacion' => $confirmacion,
					'nota_autorizacion' => $notaAutorizacion,
					'estado' => $estado,



				]);

			// Enviar notificación por correo
			mensajes::notificar_aprobacion_reserva(
				$correo,
				$nombre,
				$fecha,
				$espacio,
				$horaInicio,
				$horaFin,

				$notaAutorizacion,
				$confirmacion
			);

			// Respuesta exitosa
			echo json_encode([
				"status" => "success",
				"message" => "Notificación enviada correctamente a $nombre"
			]);
		} catch (\Throwable $th) {
			echo json_encode([
				"status" => "error",
				"message" => "Error al enviar la notificación: " . $th->getMessage()
			]);
		}



	}

	function crear_espacio()
	{                     //*CREATED

		extract($_POST);

		try {

			DB::table('cliente_espacio')->insert([
				'descripcion' => $descripcionEspacio,
				'horas_min' => $minHorasFormCrearEspacio,
				'horas_max' => $maxHorasFormCrearEspacio,
				'hora_inicio' => $horaInicioFormCrearEspacio,
				'hora_fin' => $horaFinFormCrearEspacio,
				'estado' => 1,
				'cliente' => $_SESSION['cliente'],

			]);


		} catch (\Throwable $th) {
			$error = "No se pudo guardar el registro.";

			echo "<script>alert('❌ Error: $error');</script>";
		}

		return back();

	}

	function actualizar_espacios()
	{               //*UPDATE

		extract($_POST);

		try {

			DB::table('cliente_espacio')->where('id', $idEditar)->update([
				'descripcion' => $descripcion,
				'horas_min' => $minHoras,
				'horas_max' => $maxHoras,
				'hora_inicio' => $horaInicio,
				'hora_fin' => $horaFin,
				'estado' => 1,
				'cliente' => $_SESSION['cliente'],

			]);




		} catch (\Throwable $th) {
			$error = "No se pudo guardar el registro.";

			echo "<script>alert('❌ Error: $error');</script>";
		}
		return back();

	}

 	function eliminar_espacio()
	{                  //*DELETE

		extract($_POST);

		try {

			DB::table('cliente_espacio')->where('id', $idEliminar)->update(['estado' => 0]);

		} catch (\Throwable $th) {
			$error = "No se pudo guardar el registro.";

			echo "<script>alert('❌ Error: $error');</script>";
		}
		return back();



	}

 
	function comunicados()
	{


		$comunicados = DB::select("SELECT * from comunicados ");



		return view('cliente.comunicados', compact('comunicados'));


	}

 	function comunicados_subir_imagen()
	{
		extract($_POST);
		$ubicacion = "uploads/clientes/";

		Controller::folders($ubicacion); // Asegúrate de que crea el folder si no existe

		$nombreArchivo = time() . $_FILES['img']["name"];
		$rutaFinal = $ubicacion . $nombreArchivo;

		if (move_uploaded_file($_FILES['img']['tmp_name'], $rutaFinal)) {
			try {
				DB::table('comunicados')->insert([
					'cliente' => $_SESSION['cliente'],
					'usuario' => $_SESSION['id'],
					'fecha' => date("Y-m-d H:i:s"), // o usa now() si Laravel está bien configurado
					'imagen' => "/$rutaFinal",
					'fechaDuracion' => $fechaDuracion ?? null,
					'activo' => 1,
					'link' => $link ?? null
				]);

				echo json_encode([
					"status" => "success",
					"message" => "Imagen subida y datos guardados correctamente."
				]);
			} catch (Exception $e) {
				// Si ocurre un error en el insert
				echo json_encode([
					"status" => "error",
					"message" => "Error al guardar en la base de datos: " . $e->getMessage()
				]);
			}
		} else {
			echo json_encode([
				"status" => "error",
				"message" => "Error al subir la imagen al servidor."
			]);
		}

	}

 
	function editar_imagen(){
		extract($_POST);

		try {
			// Intentar la actualización
			DB::table('comunicados')->where('id', $id)->update([



				'fechaDuracion' => !empty($fechaDuracion) ? $fechaDuracion : null,

				'link' => !empty($link) ? $link : null
			]);

			// Enviar respuesta exitosa
			echo json_encode([
				"status" => "success",
				"message" => "Datos editados con éxito."
			]);
		} catch (\Throwable $th) {
			// Captura de errores y envío como respuesta
			echo json_encode([
				"status" => "error",
				"message" => "Error al guardar en la base de datos: " . $th->getMessage()
			]);
		}
	}

function comunicados_eliminarImagen() { 
		extract($_POST);

		try {
			// Intentar la actualización
			DB::table('comunicados')->where('id', $id)->update([



				'activo' => 0,

			]);

			// // Enviar respuesta exitosa
			echo json_encode([
				"status" => "success",
				"message" => "Datos eliminados"
			]);
		} catch (\Throwable $th) {
			// Captura de errores y envío como respuesta
			echo json_encode([
				"status" => "error",
				"message" => "Error al eliminar en la base de datos: " . $th->getMessage()
			]);
		}
	}
