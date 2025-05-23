<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Controllers\mensajes;
use App\Http\Controllers\WhatsAppApi;
use App\Http\Controllers\authController as auth;
use DB;
use PDF;
use DateTime;
use stdClass;
class clienteController extends Controller
{
	function rutas_aleman()
	{
		return view('cliente.aleman.rutas');
	}
	function minuta()
	{
		return view('cliente.minuta');
	}
	function correspondencia()
	{
		$correspondencia = DB::select("SELECT cliente_residente_correspondencia.*,  cliente_residente.nombre as residente, cliente_residente.espacio, usuario.nombre as vigilante from cliente_residente, cliente_residente_correspondencia, usuario where usuario.id=cliente_residente_correspondencia.vigilante and mensaje_correo!=0 and cliente_residente.cliente='" . $_SESSION['cliente'] . "' and cliente_residente.id=cliente_residente_correspondencia.residente order by cliente_residente_correspondencia.fecha desc");
		return view('cliente.correspondencia', compact('correspondencia'));
	}
	function visitantes()
	{
		$and = "";
		$fecha_inicio = date('Y-m-d');
		if (!empty($_GET['fecha_inicio'])) {
			$fecha_inicio = $_GET['fecha_inicio'];
		}
		$fecha_fin = date('Y-m-d');
		if (!empty($_GET['fecha_fin'])) {
			$fecha_fin = $_GET['fecha_fin'];
		}
		$visitantes = DB::select("SELECT cliente_residente.espacio, cliente_residente.nombre as nombre_residente, cliente_residente.telefono as telefono_residente, cliente_visitante_registro.fecha, cliente_visitante_registro.hora, cliente_visitante.id,CONCAT(cliente_visitante.nombre1, ' ', cliente_visitante.nombre2, ' ', cliente_visitante.apellido1, ' ', cliente_visitante.apellido2) as nombre_visitante, cliente_visitante_registro.salida_hora, cliente_visitante_registro.salida_fecha, cliente_visitante.telefono as telefono_visitante, cliente_visitante.foto from cliente_visitante_registro, cliente_visitante, cliente_residente where cliente_visitante_registro.visitante = cliente_visitante.id and  cliente_visitante_registro.fecha between '$fecha_inicio' and '$fecha_fin' and cliente_visitante_registro.residente=cliente_residente.id and cliente_residente.cliente='" . $_SESSION['cliente'] . "'  $and order by cliente_visitante_registro.id desc limit 100");
		return view('cliente.visitantes', compact('fecha_inicio', 'fecha_fin', 'visitantes'));
	}
	function control_vehiculos()
	{
		$fecha_inicio = date('Y-m-d');
		$fecha_fin = date('Y-m-d');
		extract($_GET);
		$and = "";
		$data = DB::select("SELECT vehiculo_control.*, cliente.nombre as cliente, cliente.id as cliente_id, usuario.nombre as vigilante from vehiculo_control, cliente, usuario where vehiculo_control.cliente=cliente.id and vehiculo_control.vigilante=usuario.id and vehiculo_control.fecha between '$fecha_inicio' and '$fecha_fin'  AND cliente.id='" . $_SESSION['cliente'] . "'");
		$revision = DB::select("SELECT * from vehiculo_control_revision where vehiculo_control in (select id from vehiculo_control where fecha between '$fecha_inicio' and '$fecha_fin' $and)");
		$item_revision = DB::table('vehiculo_revisar')->get();
		foreach ($data as $key => $value) {
			$data[$key]->revision = [];
			foreach ($item_revision as $key1 => $value1) {
				$data[$key]->revision[$value1->id] = 0;
			}
			foreach ($revision as $key1 => $value1) {
				if ($value1->vehiculo_control == $value->id) {
					$data[$key]->revision[$value1->item_revision] = 1;
				}
			}
		}
		if (!empty($_GET['download'])) {
			$clientes = DB::select("SELECT cliente.* from vehiculo_control, cliente, usuario where vehiculo_control.cliente=cliente.id and vehiculo_control.vigilante=usuario.id and vehiculo_control.fecha between '$fecha_inicio' and '$fecha_fin' AND cliente.id='" . $_SESSION['id'] . "' group by cliente.id");
			$pdf = PDF::loadView('pdf.reportes.control_vehiculos', compact('fecha_inicio', 'fecha_fin', 'clientes', 'item_revision', 'data'));
			$pdf->setPaper('letter', 'landscape');
			return $pdf->stream("Reporte control de equipos de comunicación.pdf");
		}
		return view('cliente.control_vehiculos', compact('fecha_inicio', 'fecha_fin', 'item_revision', 'data'));
	}
	function importar_dependencias()
	{
		$problemas = [];
		$registros_cargados = [];
		$cliente = $_SESSION['cliente'];
		if (!empty($_GET['data'])) {
			$areas = DB::table('cliente_area')->where('cliente', 0)->orWhere('cliente', $_SESSION['cliente'])->orderBy('descripcion')->get();
			$data = json_decode(file_get_contents('https://software.ocrovi.com/data.json'));
			if (!empty($data->espacio) && !empty($data->nombre) && !empty($data->cedula) && !empty($data->telefono) && !empty($data->correo) && !empty($data->area)) {
				$residentes = DB::table('cliente_residente')->where('cliente', $cliente)->where('estado', 1)->get();
				foreach ($data->cedula as $key => $value) {
					$area_desc = trim($data->area[$key]);
					$area = 0;
					$tipo = 0;
					foreach ($areas as $a) {
						if (strtolower($a->descripcion) == strtolower($area_desc)) {
							$area = $a->id;
							$tipo = $a->tipo_residente;
						}
					}
					if ($area == 0) {
						$problemas[] = "El área '$area_desc' no se reconoce.";
					} else {
						$value = str_replace(',', '', $value);
						$value = str_replace('.', '', $value);
						$value = str_replace(' ', '', $value);
						$cedula = $value;
						$existe_residente = false;
						$id_residente = 0;
						foreach ($residentes as $key1 => $value1) {
							if ($value1->cedula == $cedula) {
								$id_residente = $value1->id;
								$existe_residente = true;
							}
						}
						if (!$existe_residente) {
							DB::table('cliente_residente')->insert([
								'espacio' => $data->espacio[$key],
								'nombre' => $data->nombre[$key],
								'cedula' => $cedula,
								'cedula' => $cedula,
								'area' => $area,
								'tipo' => $tipo,
								'telefono' => $data->telefono[$key],
								'correo' => $data->correo[$key],
								'cliente' => $cliente,
							]);
							$registros_cargados[] = "Cédula: $cedula Nombre: " . $data->nombre[$key];
						} else {
							DB::table('cliente_residente')->where('id', $id_residente)->update([
								'espacio' => $data->espacio[$key],
								'nombre' => $data->nombre[$key],
								'area' => $area,
								'tipo' => $tipo,
								'telefono' => $data->telefono[$key],
								'correo' => $data->correo[$key],
							]);
							$problemas[] = "El residente  " . $data->nombre[$key] . " con cédula $cedula ya existe. Sus datos fueron actualizados";
						}
					}
				}
			} else {
				$problemas[] = "No se ha podido cargar debido a que faltan campos por diligenciar en el excel";
			}
		}
		return redirect('/residentes')->with('problemas', $problemas)->with('registros_cargados', $registros_cargados)->with('subido', 'subido');
	}
	function vehiculos()
	{
		$and = "";
		$fecha_inicio = date('Y-m-d');
		if (!empty($_GET['fecha_inicio'])) {
			$fecha_inicio = $_GET['fecha_inicio'];
		}
		$fecha_fin = date('Y-m-d');
		if (!empty($_GET['fecha_fin'])) {
			$fecha_fin = $_GET['fecha_fin'];
		}
		$registros = DB::select("SELECT vehiculo_registro.*, cliente_residente.espacio, cliente_residente.nombre as residente, vehiculo_marca.descripcion as marca from vehiculo_marca, vehiculo_registro, cliente_residente where cliente_residente.id=vehiculo_registro.residente and  vehiculo_marca.id=vehiculo_registro.marca and vehiculo_registro.cliente='" . $_SESSION['cliente'] . "' and substring(ingreso, 1, 10) between '$fecha_inicio' and '$fecha_fin' order by vehiculo_registro.id desc");
		return view('cliente.vehiculos', compact('registros', 'fecha_inicio', 'fecha_fin'));
	}
	function residentes()
	{
		$tipos_vehiculo = DB::table('vehiculo_tipo')->orderBy('descripcion')->get();
		$marcas_vehiculo = DB::table('vehiculo_marca')->orderBy('descripcion')->get();
		$residentes = DB::table('cliente_residente')->where('cliente', $_SESSION['cliente'])->where('estado', 1)->get();
		$clientes = DB::table('cliente')->where('estado', 1)->orderBy('nombre')->get();
		if (!empty($_GET['make'])) {
			DB::table('cliente_residente_vehiculo')->where('id', base64_decode($_GET['id_v']))->update(['estado' => 0]);
		}
		$tipos = DB::table('cliente_tipo_residente')->where('cliente', 0)->orWhere('cliente', $_SESSION['cliente'])->orderBy('descripcion')->get();
		$areas = DB::table('cliente_area')->where('cliente', 0)->orWhere('cliente', $_SESSION['cliente'])->orderBy('descripcion')->get();
		foreach ($residentes as $key => $r) {
			if ($r->area) {
				foreach ($areas as $a) {
					if ($a->id == $r->area) {
						$residentes[$key]->area = $a->descripcion;
					}
				}
			}
		}
		return view('cliente.residentes', compact('residentes', 'tipos_vehiculo', 'marcas_vehiculo', 'clientes', 'tipos', 'areas'));
	}
	function administrar_residentes_cambio_cliente($id)
	{
		DB::table('cliente_residente')->where('id', $id)->update(['cliente' => $_POST['cliente']]);
		return back();
	}
	function residentes_asignar_constrasela_post()
	{
		extract($_POST);
		if ($usuario == 0) {
			$residente = DB::table('cliente_residente')->where('id', $id)->get()[0];
			$usuario = Controller::AI('usuario');
			DB::table('usuario')->insert([
				'id' => $usuario,
				'nombre' => $residente->nombre,
				'cedula' => $residente->correo,
				'password' => auth::encript($pass),
				'rol' => 12,
			]);
			DB::table('cliente_residente')->where('id', $id)->update(['usuario' => $usuario]);
		} else {
			DB::table('usuario')->where('id', $usuario)->update([
				'password' => auth::encript($pass),
			]);
		}
		return back();
	}
	function residentes_vehiculo_crear_post()
	{
		extract($_POST);
		DB::table('cliente_residente_vehiculo')->insert([
			'tipo' => $tipo,
			'placa' => $placa,
			'marca' => $marca,
			'modelo' => $modelo,
			'color' => $color,
			'residente' => $residente,
			'control_kilometraje' => $control_kilometraje,
		]);
		return back();
	}
	function residentes_crear_post()
	{
		$tipo = 1;
		$area = 1;
		extract($_POST);
		$id = Controller::AI('cliente_residente');
		if (empty($cliente)) {
			$cliente = $_SESSION['cliente'];
		}
		DB::table('cliente_residente')->insert([
			'id' => $id,
			'espacio' => $espacio,
			'cedula' => $cedula,
			'nombre' => strtoupper($nombre),
			'telefono' => $telefono,
			'tipo' => $tipo,
			'area' => $area,
			'cliente' => $cliente,
		]);
		WhatsAppApi::enviar_autorizacion($id);
		return back();
	}
	function residentes_editar_post()
	{
		extract($_POST);
		DB::table('cliente_residente')->where('id', $id)->update([
			'espacio' => $espacio,
			'cedula' => $cedula,
			'nombre' => $nombre,
			'telefono' => $telefono,
			'correo' => $correo,
		]);
		return back();
	}
	function configuracion_administrar_areas()
	{
		extract($_POST);
		DB::table('cliente_rol_area')->where('rol', $rol)->delete();
		if (!empty($area)) {
			foreach ($area as $a) {
				DB::table('cliente_rol_area')->insert([
					'area' => $a,
					'rol' => $rol,
				]);
			}
		}
		return back();
	}
	function configuracion()
	{
		$actioner = "gestion_residentes";
		extract($_GET);
		switch ($actioner) {
			case 'gestion_residentes':
				$tipos = DB::table('cliente_tipo_residente')->where('cliente', 0)->orWhere('cliente', $_SESSION['cliente'])->orderBy('descripcion')->get();
				$areas = DB::table('cliente_area')->where('cliente', 0)->orWhere('cliente', $_SESSION['cliente'])->orderBy('descripcion')->get();
				return view('cliente.configuracion', compact('actioner', 'tipos', 'areas'));
			case 'gestion_usuarios':
				$tipos = DB::table('cliente_tipo_residente')->where('cliente', 0)->orWhere('cliente', $_SESSION['cliente'])->orderBy('descripcion')->get();
				$areas = DB::table('cliente_area')->where('cliente', 0)->orWhere('cliente', $_SESSION['cliente'])->orderBy('descripcion')->get();
				$usuarios = DB::table('usuario')->select(['id', 'nombre', 'correo', 'cliente_rol'])->where('cliente', $_SESSION['cliente'])->where('rol', 14)->get();
				$roles = DB::table('cliente_rol')->where('cliente', $_SESSION['cliente'])->orderBy('descripcion')->get();
				foreach ($usuarios as $key => $a) {
					$usuarios[$key]->rol = Controller::search('cliente_rol', $a->cliente_rol);
				}
				return view('cliente.configuracion', compact('actioner', 'roles', 'usuarios', 'areas', 'tipos'));
			case 'sst':
				$items = DB::table('cliente_aleman_lista_chequeo_contratista')->get();
				return view('cliente.configuracion', compact('actioner', 'items'));
		}
		return view('cliente.configuracion', compact('actioner'));
	}
	function configuracion_asignar_pass_usuario()
	{
		DB::table('usuario')->where('id', $_POST['id'])->update(['password' => auth::encript($_POST['password'])]);
		return back();
	}
	function configuracion_crear_tipo_residente()
	{
		DB::table('cliente_tipo_residente')->insert(['cliente' => $_SESSION['cliente'], 'descripcion' => $_POST['descripcion']]);
		return back();
	}
	function configuracion_crear_usuario()
	{
		extract($_POST);
		DB::table('usuario')->insert([
			'nombre' => $nombre,
			'cedula' => $correo,
			'correo' => $correo,
			'cliente_rol' => $cliente_rol,
			'cliente' => $_SESSION['cliente'],
			'rol' => 14,
			'password' => auth::encript($password),
		]);
		return back();
	}
	function configuracion_crear_area()
	{
		DB::table('cliente_area')->insert(['cliente' => $_SESSION['cliente'], 'descripcion' => $_POST['descripcion'], 'maneja_horario' => $_POST['maneja_horario'], 'tipo_residente' => $_POST['tipo_residente']]);
		return back();
	}
	function configuracion_crear_rol()
	{
		$id = Controller::AI('cliente_rol');
		DB::table('cliente_rol')->insert([
			'id' => $id,
			'cliente' => $_SESSION['cliente'],
			'descripcion' => $_POST['descripcion'],
			'acceso_horarios' => $_POST['acceso_horarios'],
			'acceso_permiso_salida' => $_POST['acceso_permiso_salida'],
			'acceso_actividades_extra' => $_POST['acceso_actividades_extra'],
			'acceso_registro' => $_POST['acceso_registro'],
		]);
		if (!empty($area)) {
			foreach ($area as $a) {
				DB::table('cliente_rol_area')->insert([
					'area' => $a,
					'rol' => $id,
				]);
			}
		}
		return back();
	}
	function horarios()
	{
		if ($_SESSION['rol'] == 14) {
			$areas = DB::select("SELECT cliente_area.* from cliente_area, cliente_rol_area where cliente_rol_area.rol='" . $_SESSION['cliente_rol'] . "' and cliente_area.id=cliente_rol_area.area order by cliente_area.descripcion ");
		} else {
			$areas = DB::table('cliente_area')->where('cliente', $_SESSION['cliente'])->where('maneja_horario', 1)->orWhere('cliente', 0)->where('maneja_horario', 1)->orderBy('descripcion')->get();
		}
		$dias = DB::table('conf_dia')->get();
		$horario_area = DB::select("SELECT cliente_area_horario.* from cliente_area, cliente_area_horario where cliente_area.cliente='" . $_SESSION['cliente'] . "' and cliente_area.id=cliente_area_horario.area");
		$id_registro = [];
		$hora_ingreso = [];
		$hora_salida = [];
		foreach ($horario_area as $ha) {
			if (empty($id_registro[$ha->area])) {
				$id_registro[$ha->area] = [];
				$hora_ingreso[$ha->area] = [];
				$hora_salida[$ha->area] = [];
			}
			$id_registro[$ha->area][$ha->dia] = $ha->id;
			$hora_ingreso[$ha->area][$ha->dia] = $ha->hora_ingreso;
			$hora_salida[$ha->area][$ha->dia] = $ha->hora_salida;
		}
		foreach ($areas as $area) {
			foreach ($dias as $dia) {
				if (empty($id_registro[$area->id][$dia->id])) {
					$id = Controller::AI('cliente_area_horario');
					$id_registro[$area->id][$dia->id] = $id;
					$hora_ingreso[$area->id][$dia->id] = "";
					$hora_salida[$area->id][$dia->id] = "";
					DB::table('cliente_area_horario')->insert([
						'id' => $id,
						'area' => $area->id,
						'dia' => $dia->id,
					]);
				}
			}
		}
		return view('cliente.usuario.horarios', compact('areas', 'dias', 'id_registro', 'hora_ingreso', 'hora_salida'));
	}





	//* metodos para la URL /cliente/reserva_espacios/



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







	function reservas_espacios()
	{                     //*READ

		// mensajes::notificar_aprobacion_reserva($usuario, $reser)

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



	function comunicados_eliminarImagen()
	{
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









	function editar_imagen()
	{
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




	function crear_reserva()
	{

		extract($_POST);

		try {

			$fecha = $_POST['fecha'];
			$espacio = $_POST['espacio'];
			$residente = $_POST['residente'];
			$hora_inicio = $_POST['hora_inicio'];
			$hora_fin = $_POST['hora_fin'];
			$observaciones = $_POST['observaciones'];


			// $response = [
			// 	"fecha" => $_POST['fecha'],
			// 	"espacio" => $_POST['espacio'],
			// 	"residente" => $_POST['residente'],
			// 	"hora_inicio" => $_POST['hora_inicio'],
			// 	"hora_fin" => $_POST['hora_fin'], // Revisa si es "hora_fin"
			// 	"observaciones"    => $_POST['observaciones']
			// ];

			// echo "Fecha: $fecha<br>";
			// echo "Espacio: $espacio<br>";
			// echo "Residente: $residente<br>";
			// echo "Hora de inicio: $hora_inicio<br>";
			// echo "Hora de fin: $hora_fin<br>";
			// echo "Observaciones: $observaciones<br>";


			// Fecha: 2025-03-21
			// Espacio: 3
			// Residente: 1
			// Hora de inicio: 08:00
			// Hora de fin: 16:00
			// Observaciones: ssssss



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


			// DB::table('cliente_espacio_reserva')->insert([
			// 	"fecha" => $_POST['fecha'],
			// 	"espacio" => $_POST['espacio'],
			// 	"residente" => $_POST['residente'],
			// 	"hora_inicio" => $_POST['hora_inicio'],
			// 	"hora_fin" => $_POST['hora_fin'],
			// 	"observaciones" => $_POST['observaciones'],
			// 	"estado" => 1
			// ]);










			//return back();
		} catch (\Throwable $th) {
			// ❌ Respuesta en JSON si hay error
			echo json_encode([
				"status" => "error",
				"message" => "No se pudo crear la reserva: " . $th->getMessage()
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











	function autorizar_reserva()
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

	function cancelar_reserva()
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










	function autorizacion_salida()
	{
		if ($_SESSION['rol'] == "cliente") {
			$autorizaciones = DB::select("SELECT usuario.nombre as autoriza, cliente_residente.cedula, cliente_residente.nombre as residente, cliente_area.descripcion as area, cliente_residente_autorizacion_salida.id, cliente_residente_autorizacion_salida.fecha, cliente_residente_autorizacion_salida.hora_salida, cliente_residente_autorizacion_salida.motivo,cliente_residente_autorizacion_salida.adjunto from usuario,  cliente_residente, cliente_area, cliente_residente_autorizacion_salida where usuario.id=cliente_residente_autorizacion_salida.autoriza and cliente_residente_autorizacion_salida.residente=cliente_residente.id and cliente_residente.area=cliente_area.id order by cliente_residente_autorizacion_salida.id desc ");
		} else {
			$autorizaciones = DB::select("SELECT usuario.nombre as autoriza, cliente_residente.cedula, cliente_residente.nombre as residente, cliente_area.descripcion as area, cliente_residente_autorizacion_salida.id, cliente_residente_autorizacion_salida.fecha, cliente_residente_autorizacion_salida.hora_salida, cliente_residente_autorizacion_salida.motivo,cliente_residente_autorizacion_salida.adjunto from usuario,  cliente_residente, cliente_area, cliente_residente_autorizacion_salida where usuario.id=cliente_residente_autorizacion_salida.autoriza and cliente_residente_autorizacion_salida.residente=cliente_residente.id and cliente_residente.area=cliente_area.id and cliente_residente_autorizacion_salida.autoriza='" . $_SESSION['id'] . "' order by cliente_residente_autorizacion_salida.id desc ");
		}
		if ($_SESSION['rol'] == 14) {
			$residentes = DB::select("SELECT cliente_residente.cedula, cliente_residente.nombre, cliente_residente.id, cliente_area.descripcion as area from cliente_area, cliente_residente, cliente_rol_area where cliente_rol_area.rol='" . $_SESSION['cliente_rol'] . "' and cliente_rol_area.area=cliente_residente.area and cliente_residente.area=cliente_area.id and cliente_residente.cliente='" . $_SESSION['cliente'] . "'  order by cliente_area.descripcion, cliente_residente.nombre");
		} else {
			$residentes = DB::select("SELECT cliente_residente.cedula, cliente_residente.nombre, cliente_residente.id, cliente_area.descripcion as area from cliente_area, cliente_residente where cliente_residente.area=cliente_area.id and cliente_residente.cliente='" . $_SESSION['cliente'] . "'  order by cliente_area.descripcion, cliente_residente.nombre");
		}
		return view('cliente.usuario.autorizacion_salida', compact('autorizaciones', 'residentes'));
	}
	function eventos()
	{
		$eventos = DB::select("SELECT cliente_evento.*, cliente_evento.tipo as tipo_id, cliente_evento_tipo.descripcion as tipo, cliente_evento_tipo.color as tipo_color from cliente_evento, cliente_evento_tipo where cliente_evento_tipo.id=cliente_evento.tipo and cliente_evento.cliente='" . $_SESSION['cliente'] . "'");
		foreach ($eventos as $key => $e) {
			$eventos[$key]->color_letra = Controller::getBestTextColor($e->tipo_color);
		}
		$tipos = DB::table('cliente_evento_tipo')->where('cliente', $_SESSION['cliente'])->orderBy('descripcion')->get();
		return view('cliente.eventos', compact('eventos', 'tipos'));
	}












	function eventos_crear_tipo()
	{
		extract($_POST);
		DB::table('cliente_evento_tipo')->insert([
			'cliente' => $_SESSION['cliente'],
			'descripcion' => $descripcion,
			'color' => $color,
		]);
		return back();
	}
	function eventos_crear()
	{
		extract($_POST);
		DB::table('cliente_evento')->insert([
			'cliente' => $_SESSION['cliente'],
			'descripcion' => $descripcion,
			'titulo' => $titulo,
			'tipo' => $tipo,
			'fecha' => str_replace('T', ' ', $fecha),
		]);
		return back();
	}
	function autorizacion_salida_post()
	{
		extract($_POST);
		$adjunto = "";
		if (!empty($_FILES['adjunto']['tmp_name'])) {
			$adjunto = "uplodas/cliente/adjunto_autorizacion_salida";
			Controller::folders($adjunto);
			$adjunto .= "/FILE-" . time() . "." . Controller::extencion($_FILES['adjunto']['name']);
			move_uploaded_file($_FILES['adjunto']['tmp_name'], $adjunto);
		}
		$id = Controller::AI('cliente_residente_autorizacion_salida');
		DB::table('cliente_residente_autorizacion_salida')->insert([
			'id' => $id,
			'residente' => $residente,
			'fecha' => $fecha,
			'hora_salida' => $hora_salida,
			'motivo' => $motivo,
			'adjunto' => $adjunto,
			'autoriza' => $_SESSION['id'],
		]);
		mensajes::autorizacion_salida($id);
		return back();
	}
	function actividades_extra()
	{
		if ($_SESSION['rol'] == "cliente") {
			$actividades = DB::select("SELECT cliente_residente.id as residente_id, cliente_residente.nombre as residente, cliente_area.descripcion as area, cliente_residente_actividad_extra.id, cliente_residente_actividad_extra.fecha, cliente_residente_actividad_extra.hora_salida, cliente_residente_actividad_extra.observaciones, usuario.nombre as autoriza from usuario, cliente_residente, cliente_area, cliente_residente_actividad_extra where cliente_residente_actividad_extra.residente=cliente_residente.id and cliente_residente.area=cliente_area.id and cliente_residente_actividad_extra.autoriza=usuario.id  order by cliente_residente_actividad_extra.id desc");
		} else {
			$actividades = DB::select("SELECT cliente_residente.id as residente_id, cliente_residente.nombre as residente, cliente_area.descripcion as area, cliente_residente_actividad_extra.id, cliente_residente_actividad_extra.fecha, cliente_residente_actividad_extra.hora_salida, cliente_residente_actividad_extra.observaciones, usuario.nombre as autoriza from usuario, cliente_residente, cliente_area, cliente_residente_actividad_extra where cliente_residente_actividad_extra.residente=cliente_residente.id and cliente_residente.area=cliente_area.id and cliente_residente_actividad_extra.autoriza=usuario.id and cliente_residente_actividad_extra.autoriza='" . $_SESSION['id'] . "' order by cliente_residente_actividad_extra.id desc");
		}
		if ($_SESSION['rol'] == 14) {
			$residentes = DB::select("SELECT cliente_residente.nombre, cliente_residente.id, cliente_area.descripcion as area from cliente_area, cliente_residente, cliente_rol_area where cliente_rol_area.rol='" . $_SESSION['cliente_rol'] . "' and cliente_rol_area.area=cliente_residente.area and cliente_residente.area=cliente_area.id and cliente_residente.cliente='" . $_SESSION['cliente'] . "' and cliente_area.maneja_horario=1 order by cliente_area.descripcion, cliente_residente.nombre");
		} else {
			$residentes = DB::select("SELECT cliente_residente.nombre, cliente_residente.id, cliente_area.descripcion as area from cliente_area, cliente_residente where cliente_residente.area=cliente_area.id and cliente_residente.cliente='" . $_SESSION['cliente'] . "' and cliente_area.maneja_horario=1 order by cliente_area.descripcion, cliente_residente.nombre");
		}
		return view('cliente.usuario.actividades_extra', compact('actividades', 'residentes'));
	}
	function actividades_extra_importar()
	{
		$problemas = [];
		$registros_cargados = [];
		if (!empty($_GET['data'])) {
			$data = json_decode(file_get_contents('https://software.ocrovi.com/data.json'));
			if (!empty($data[0][0]->cedula)) {
				$data2 = $data;
				$data = new stdClass(); // Crear un nuevo objeto vacío
				$data->cedula = [];
				$data->fecha = [];
				$data->salida = [];
				$data->observaciones = [];
				foreach ($data2 as $key => $val) {
					foreach ($val as $key2 => $row) {
						$data->cedula[] = $row->cedula;
						$data->fecha[] = $row->fecha;
						$data->salida[] = $row->salida;
						$data->observaciones[] = $row->observaciones;
					}
				}
			}
			if (!empty($data->cedula) && !empty($data->fecha) && !empty($data->salida) && !empty($data->observaciones)) {
				$residentes = DB::select("SELECT cliente_residente.cedula, cliente_residente.nombre, cliente_residente.id, cliente_area.descripcion as area from cliente_area, cliente_residente where cliente_residente.area=cliente_area.id and cliente_residente.cliente='53' order by cliente_area.descripcion, cliente_residente.nombre");
				$residentes_id = [];
				$nombre = [];
				foreach ($residentes as $r) {
					$residentes_id[$r->cedula] = $r->id;
					$nombre[$r->cedula] = $r->nombre;
				}
				foreach ($data->cedula as $key => $value) {
					if (!empty($residentes_id[$value])) {
						$partes_fecha = explode(' ', $data->fecha[$key]->date);
						$fecha = $partes_fecha[0];
						$hora = date('H:i', strtotime($data->salida[$key]));
						DB::table('cliente_residente_actividad_extra')->insert([
							'residente' => $residentes_id[$value],
							'fecha' => $fecha,
							'hora_salida' => $hora,
							'observaciones' => $data->observaciones[$key],
							'autoriza' => $_SESSION['id'],
						]);
						$registros_cargados[] = "Cédula: $value Nombre: " . $nombre[$value];
					} else {
						$problemas[] = "El usuario con cédula $value no existe";
					}
				}
			} else {
				$problemas[] = "No se ha podido cargar debido a que faltan campos por diligenciar en el excel";
			}
		}
		return view('cliente.usuario.actividades_extra_resultados', compact('problemas', 'registros_cargados'));
	}
	function actividades_extra_post()
	{
		extract($_POST);
		DB::table('cliente_residente_actividad_extra')->insert([
			'residente' => $residente,
			'fecha' => $fecha,
			'hora_salida' => $hora_salida,
			'observaciones' => $observaciones,
			'autoriza' => $_SESSION['id'],
		]);
		return back();
	}
	function registros()
	{
		if ($_SESSION['cliente'] == 53) {
			return redirect('/registros_aleman');
		}
		$campo = "fecha_ingreso";
		$residente = "";
		$fecha_inicio = date('Y-m-d') . " 00:00:00";
		$fecha_fin = date('Y-m-d') . " 23:59:00";
		extract($_GET);
		$fecha_inicio = str_replace('T', ' ', $fecha_inicio);
		$fecha_fin = str_replace('T', ' ', $fecha_fin);
		$and = "";
		if ($residente != "") {
			$and .= " and cr.id=$residente ";
		}
		$personas_actualmente = DB::select("SELECT cris.*, cr.cedula, cr.nombre as residente, ctr.descripcion as tipo_residente, ca.descripcion as area from cliente_residente as cr, cliente_residente_ingreso_salida as cris, cliente_tipo_residente as ctr, cliente_area as ca where ca.id=cr.area and ctr.id=cr.tipo and cr.id=cris.residente and cris.fecha_ingreso like '" . date('Y-m-d') . "%' and fecha_salida is null and cr.cliente='" . $_SESSION['cliente'] . "' order by cr.nombre desc");
		$registros = DB::select("SELECT cris.*, cr.nombre as residente, ctr.descripcion as tipo_residente, ca.descripcion as area from cliente_residente as cr, cliente_residente_ingreso_salida as cris, cliente_tipo_residente as ctr, cliente_area as ca where ca.id=cr.area and ctr.id=cr.tipo and cr.id=cris.residente $and and cris.$campo between '$fecha_inicio' and '$fecha_fin' and cr.cliente='" . $_SESSION['cliente'] . "'order by cris.id desc");
		$autorizaciones = DB::select("SELECT cras.*, u.nombre as autoriza from cliente_residente_ingreso_salida as cris, usuario as u, cliente_residente_autorizacion_salida as cras where cras.id=cris.autorizacion_salida and u.id=cras.autoriza and cris.$campo between '$fecha_inicio' and '$fecha_fin' and u.cliente='" . $_SESSION['cliente'] . "'");
		$autorizacion = [];
		foreach ($autorizaciones as $a) {
			$autorizacion[$a->id] = $a;
		}
		foreach ($registros as $key => $r) {
			$registros[$key]->tiempo = "";
			if ($r->fecha_salida != "") {
				$date1 = new DateTime($r->fecha_ingreso);
				$date2 = new DateTime($r->fecha_salida);
				$diff = $date1->diff($date2);
				if ($diff->d > 0) {
					$registros[$key]->tiempo .= " $diff->d Días ";
				}
				if ($diff->h > 0) {
					$registros[$key]->tiempo .= " $diff->h Horas ";
				}
				if ($diff->m > 0) {
					$registros[$key]->tiempo .= " $diff->m Minutos ";
				}
			}
		}
		unset($autorizaciones);
		$residentes = DB::select("SELECT cliente_residente.nombre, cliente_residente.id, cliente_area.descripcion as area from cliente_area, cliente_residente where cliente_residente.area=cliente_area.id and cliente_residente.cliente='" . $_SESSION['cliente'] . "' and cliente_area.maneja_horario=1 order by cliente_area.descripcion, cliente_residente.nombre");
		return view('cliente.usuario.registros', compact('campo', 'fecha_inicio', 'fecha_fin', 'autorizacion', 'residente', 'residentes', 'registros', 'personas_actualmente'));
	}
}
