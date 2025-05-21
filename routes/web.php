<?php
@session_start();
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\authController as auth;
use App\Http\Controllers\chatController as chat;
use App\Http\Controllers\androidController as android;
use App\Http\Controllers\clientesController as clientes;
use App\Http\Controllers\clienteController as cliente;
use App\Http\Controllers\usuariosController as usuarios;
use App\Http\Controllers\ajaxController as ajax;
use App\Http\Controllers\vigilanteController as vigilante;
use App\Http\Controllers\configController as config;
use App\Http\Controllers\reportesController as reportes;
use App\Http\Controllers\supervisorController as supervisor;
use App\Http\Controllers\tecnicoController as tecnico;
use App\Http\Controllers\softwareController as software;
use App\Http\Controllers\recurso_humanoController as recurso_humano;
use App\Http\Controllers\almacenController as almacen;
use App\Http\Controllers\programacionController as programacion;
use App\Http\Controllers\nominaController as nomina;
use App\Http\Controllers\sstController as sst;
use App\Http\Controllers\apiController as api;
use App\Http\Controllers\sgiController as sgi;
use App\Http\Controllers\clienteColegioAlemanController as colegioAleman;
use App\Http\Controllers\indicadorController as indicador;
use App\Http\Controllers\cronController as cron;
use App\Http\Controllers\encuestaController as encuesta;
use App\Http\Controllers\CampaniasController as campanias;
use App\Http\Controllers\WhatsAppApi;
use App\Http\Controllers\CpanelApi;
use App\Http\Controllers\mensajes;
Route::get('/clear-cache', function() {
    \Artisan::call('cache:clear');
    \Artisan::call('view:clear');
    \Artisan::call('config:clear');
    \Artisan::call('route:clear');
    return 'Cache cleared!';
});
Route::controller(mensajes::class)->group(function(){
    Route::get('/correos/enviar_autorizaciones', 'enviar_autorizaciones');
    Route::get('/correos/autorizacion_salida/{id}', 'autorizacion_salida');
    Route::get('/autorizaciones_contacto/correo/{accion}/{id}', 'autorizaciones_contacto');
}); 
Route::controller(api::class)->group(function(){
    Route::get('/api/panico2', 'panico2');
}); 
Route::controller(CpanelApi::class)->group(function(){
    Route::get('/CpanelApi/generar_correo', 'generar_correo');
});
Route::controller(WhatsAppApi::class)->group(function(){
    Route::get('/notifica_visitante/{id}', 'nuevo_visitante');
    Route::get('/notifica_correspondencia/{id}', 'entrega_correspondencia');
    Route::get('/wapi/enviar_autorizaciones', 'enviar_autorizaciones');
    Route::get('/autorizaciones_contacto/whatsapp/{accion}/{id}', 'autorizaciones_contacto');
    Route::get('/wapi/autorizaciones', 'autorizaciones');
    Route::any('/wapi/webhook', 'webhook');
});
// Route::controller(mensajes::class)->group(function(){
//     Route::get('/notifica_visitante/{id}', 'nuevo_visitante');
//     Route::get('/notifica_correspondencia/{id}', 'entrega_correspondencia');
//     Route::get('/wapi/enviar_autorizaciones', 'enviar_autorizaciones');
//     Route::get('/autorizaciones_contacto/whatsapp/{accion}/{id}', 'autorizaciones_contacto');
//     Route::get('/wapi/autorizaciones', 'autorizaciones');
//     Route::any('/wapi/webhook', 'webhook');
// });
if (!empty($_SERVER['REQUEST_URI'])) {



    if(empty($_SESSION['id']) && !empty($_COOKIE['session_id']) && $_SERVER['REQUEST_URI']!="/cookie_session")

    {

        echo "<script>location.href='/cookie_session';</script>";

        exit();

    }

    Route::get('/no_autorizado', function(){

        return view('errors.no_autorizado');

    });

    Route::controller(clientes::class)->group(function(){

        Route::get('/clientes', 'clientes');

        Route::get('/clientes/pqrs/editar/{id}', 'clientes_pqrs_editar');

        Route::post('/clientes/pqrs/editar/{id}', 'clientes_pqrs_editar_post');

    });


    Route::controller(programacion::class)->group(function(){
        Route::get('/programacion/actualizacion', 'actualizar_programacion');

        Route::get('/solicitudes_permisos/ver/{base}', 'solicitudes_permisos_base');

        Route::get('/solicitudes_permisos/{id}/{autorizar}/{area}', 'solicitudes_base_autorizar');

        Route::post('/solicitudes_permisos/{id}/{autorizar}/{area}', 'solicitudes_base_autorizar_post');

    });



    Route::controller(android::class)->group(function(){

        Route::get('/ref', 'ref');

        Route::post('/android_services/login', 'login2');

        Route::post('/android_services/sync', 'sync');

        Route::get('/android2/login', 'login');

        Route::post('/api/android/panico_cambia_puesto', 'cambio_puesto_android');

        // Route::get('/android/login', 'login');



    });

    Route::controller(chat::class)->group(function(){

        Route::get('/chat', 'inicio');

        Route::post('/chat/iniciar_conversacion', 'iniciar_conversacion');

        Route::get('/chat/chat', 'chat');

        Route::post('/chat/enviar_mensaje', 'enviar_mensaje');

        Route::get('/chat/salir/{chat}/{usuario}', 'salir');

        Route::get('/chat/mensajes_nuevos', 'mensajes_nuevos');

    });

    //   Route::controller(auth::class)->group(function(){

    //     Route::get('/cookie_session', 'cookie_session');

    //     Route::get('/logout', 'logout');

    //     Route::get('/', 'inicio');

    //     Route::post('/', 'login_post');

    //     Route::post('/android/login', 'login_android');

    //     Route::get('/cript/{bla}', 'encript');

    //     Route::get('/cron/copy', 'cron_copy');



    // });

    Route::controller(api::class)->group(function(){

        Route::get('/api/panico2', 'panico2');
        Route::get('/api/panico', 'panico');

        Route::get('/api/panico_omt', 'panico_omt');

        Route::get('/api/panico_registros', 'panico_registros');

    });



    Route::controller(vigilante::class)->group(function(){



        Route::get('/vigilantes_clientes_auto', 'vigilantes_clientes_auto');

        Route::post('/android/inicia_ronda', 'android_inicia_ronda');



        Route::post('/android/finaliza_ronda', 'android_finaliza_ronda');



        Route::post('/android/sincronizar_vigilante', 'android_sincronizar_vigilante');



    });



    Route::controller(recurso_humano::class)->group(function(){



        Route::get('/importar_fotos', 'importar_fotos');



    });

    $_SESSION['es_empleado'] = false;

    if(!empty($_SESSION['rol']))

    {

        if($_SESSION['rol']!="cliente" && $_SESSION['rol']!=11 && $_SESSION['rol']!=12  &&  $_SESSION['rol']!=14  &&  $_SESSION['rol']!=8)

        {

            $_SESSION['es_empleado'] = true;

        }

        if($_SESSION['rol']==11)

        {

             $_SESSION['rol'] = "cliente";

        }

      

        if($_SESSION['cliente'] == 53)

        {

            /// Supervisores de ruta colegio alemán 

            if($_SESSION['cliente_rol'] == 16)

            {

                Route::controller(colegioAleman::class)->group(function(){

                    Route::get('/cliente/aleman/rutas/rutas', 'rutas_rutas');

                    Route::get('/cliente/aleman/rutas/auxiliares', 'rutas_auxiliares');

                    Route::post('/cliente/aleman/rutas/crear', 'rutas_crear');

                    Route::post('/cliente/aleman/rutas/agregar_estudiante_a_ruta', 'rutas_agregar_estudiante_a_ruta');

                    Route::post('/cliente/aleman/rutas/eliminar_estudiante_ruta', 'rutas_eliminar_estudiante_ruta');

                });

                Route::controller(cliente::class)->group(function(){

                    Route::post('/configuracion/asignar_pass_usuario', 'configuracion_asignar_pass_usuario');

                    Route::post('/configuracion/crear_usuario', 'configuracion_crear_usuario');

                });



            }
            if($_SESSION['cliente_rol'] == 15 || $_SESSION['cliente_rol'] == 17)
            {
                Route::controller(colegioAleman::class)->group(function(){
                    Route::get('/aleman/rutas/inicio', 'rutas_inicio');
                    Route::post('/aleman/rutas/inicio', 'rutas_inicio_post');
                    Route::get('aleman/rutas/seccion/embarque_ruta', 'rutas_inicio_embarque');
                    Route::get('aleman/rutas/seccion/embarque_ruta_finalizacion', 'embarque_ruta_finalizacion');
                    Route::post('/aleman/rutas/embarcar_ruta', 'embarcar_estudiante_inicio');
                    Route::post('/aleman/rutas/embarcar_ruta_finalizacion', 'embarcar_estudiante_inicio_finalizacion');
                    Route::post('/aleman/rutas/inicio/confirmar_llegada', 'inicio_confirmar_llegada');
                    Route::post('/aleman/rutas/finalizacion/finalizar_ruta', 'finalizar_ruta');

                    Route::get('/aleman/rutas/finalizacion', 'rutas_finalizacion');
                    Route::post('/aleman/rutas/finalizacion', 'rutas_finalizacion_post');
                });
            }

            



            Route::controller(colegioAleman::class)->group(function(){

                Route::get('/registros_aleman', 'registros');

                Route::get('/cliente/aleman/despacho_rutas', 'despacho_rutas');

                Route::post('/clientes/aleman/item_crear', 'item_crear');

                Route::get('/aleman/contratistas', 'contratistas');

                Route::get('/aleman/usuarios', 'usuarios');

                Route::post('/ajax/aleman/contratistas/marcar_validacion', 'ajax_contratistas_marcar_validacion');

            });

        }

             Route::controller(clientes::class)->group(function(){



                    Route::get('/clientes/minuta/{id}', 'minuta_cliente');



                    Route::post('/clientes/vigilantes/{id}', 'vigilantes_cliente');



                });



            if($_SESSION['es_empleado'] == true)

            {

                Route::controller(sgi::class)->group(function(){

                    Route::get('/sgi/radicacion_documentos', 'radicacion_documentos');

                    Route::post('/sgi/radicacion_documentos', 'radicacion_documentos_post');

                    Route::get('/sgi/radicacion_documentos/aceptar/{id}', 'radicacion_documentos_aceptar');

                });
                Route::controller(ajax::class)->group(function(){
                    Route::post('/ajax/ficha_persona', 'ficha_persona');
                });
                Route::controller(campanias::class)->group(function(){
                    Route::post('/ajax/campanias/filtrado', 'filtrado');
                    Route::post('/ajax/campanias/subir_imagen', 'subir_imagen');
                    Route::post('/ajax/campanias/enviar', 'enviar');
                });
            }

            if($_SESSION['rol']!=13 && $_SESSION['rol']!=12  &&  $_SESSION['rol']!=14  &&  $_SESSION['rol']!=8 || $_SESSION['es_empleado'])

            {

                Route::controller(usuarios::class)->group(function(){

                    Route::post('/solicitudes_usuarios/respuesta/{id}', 'respuesta_solicitud');

                    Route::get('/autogestion/otras_solicitudes', 'otras_solicitudes');

                    Route::get('/autogestion/mi_perfil', 'mi_perfil');

                    Route::get('/autogestion/seguridad', 'seguridad');

                    Route::post('/autogestion/seguridad', 'seguridad_post');

                    Route::get('/autogestion/solicitudes_tramites', 'solicitudes_tramites');

                    Route::get('/autogestion/carta_laboral', 'carta_laboral');

                    Route::get('/autogestion/solicitud_permiso', 'solicitud_permiso');

                    Route::get('/autogestion/solicitud_permiso/{id}', 'solicitud_permiso_ver');

                });

                Route::controller(sst::class)->group(function(){

                    Route::post('/sst/encuesta_sociodemografica/encuesta_personal', 'encuesta_sociodemografica_personal');

                    Route::get('/sst/encuesta_sociodemografica', 'encuesta_sociodemografica');

                });

            }

            switch ($_SESSION['rol']) {



                ////////////////ADMINISTRADOR ////////////////////////

                case '1':

                    Route::controller(campanias::class)->group(function(){
                        Route::get('/campanias/nueva', 'campanias_nueva');
                        Route::get('/campanias/ver', 'campanias_ver');

                    }); 
                    Route::controller(encuesta::class)->group(function(){

                        Route::get('/encuestas', 'encuestas');

                        Route::get('/encuestas/crear', 'encuestas_crear');

                        Route::post('/encuestas/cerrar/{id}', 'encuestas_cerrar');

                        Route::get('/encuestas/editar/{id}', 'encuestas_editar')->name('encuestas.editar');

                        Route::get('/encuestas/administrar/{id}', 'encuestas_administrar')->name('encuestas.administrar');

                        Route::get('/encuestas/administrar/versiones/{id}', 'encuestas_administrar_versiones');

                        Route::post('/encuestas/administrar/versiones/{id}', 'encuestas_administrar_versiones_crear');
                        Route::post('/ajax/encuestas/editar_respuesta', 'encuestas_editar_respuesta');
                        Route::post('/encuestas/administrar/versiones/{id}/editar', 'encuestas_administrar_versiones_editar');

                        Route::get('/encuestas/administrar/respuestas/{id}', 'encuestas_administrar_respuestas');

                        Route::any('/encuestas/administrar/estadisticas/{id}', 'encuestas_administrar_estadisticas');



                    }); 

                    Route::controller(programacion::class)->group(function(){

                        Route::get('/programacion', 'inicio');

                        Route::post('/programacion/novedad_turno', 'novedad_turno');

                        Route::post('/programacion/descargar', 'descargar');

                        Route::get('/programacion/puesto', 'puesto');

                        Route::get('/programacion/puesto/{id}', 'puesto');

                        Route::get('/ajax/componentes/contenedor_programacion', 'componente_contenedor_programacion');

                    });

                    Route::controller(programacion::class)->group(function(){

                        Route::get('/solicitudes_permisos', 'solicitudes_permisos');

                        Route::post('/solicitudes_permisos/autorizacion_programacion', 'autorizacion_programacion_permiso');

                        Route::post('/solicitudes_permisos/rechazar_solicitud', 'autorizacion_programacion_permiso_rechazar');

                        Route::get('/programacion', 'inicio');

                        Route::get('/programacion/bitacora', 'bitacora');

                        Route::get('/programacion_turnos', 'programacion_turnos');
                        Route::post('/ajax/programacion_turnos/puesto', 'programacion_turnos_puesto');
                        Route::post('/ajax/programacion_turnos/servicio_ocasional', 'programacion_turnos_servicio_ocasional');
                        Route::post('/ajax/programacion_turnos/asignar_turno', 'programacion_turnos_asignar_turno');
                        Route::post('/ajax/programacion_turnos/programacion_empleado', 'programacion_turnos_empleado');
                        Route::post('/ajax/programacion_turnos/opciones/listado_servicios_ocasionales', 'programacion_turnos_opcion_listado_servicios_ocasionales');
                        Route::post('/ajax/programacion_turnos/opciones/ausencias', 'programacion_turnos_opcion_ausencias');
                        Route::post('/ajax/programacion_turnos/opciones/novedades_programacion', 'programacion_turnos_opcion_novedades_programacion');
                        Route::post('/ajax/programacion_turnos/opciones/turnos_vig', 'programacion_turnos_opcion_turnos_vig');
                        Route::post('/ajax/programacion_turnos/opciones/llenado_automatico', 'programacion_turnos_opcion_llenado_automatico');

                        Route::post('/programacion/descargar', 'descargar');

                        Route::get('/programacion/puesto', 'puesto');

                        Route::get('/programacion/puesto/{id}', 'puesto');

                        Route::post('/ajax/programacion/seleccionar', 'seleccionar');

                        Route::post('/ajax/programacion/eliminar', 'eliminar');

                        Route::post('/ajax/programacion/autocompletar', 'autocompletar');

                        Route::get('/ajax/componentes/contenedor_programacion', 'componente_contenedor_programacion');

                        Route::post('/ajax/programacion/agregar_persona', 'programacion_agregar_persona');

                        Route::post('/programacion/novedad/anular', 'novedad_solicitar_anulacion');

                        Route::post('/ajax/programacion/asignar_otro_puesto', 'programacion_asignar_otro_puesto');

                        Route::get('/programacion/novedades', 'novedades');

                        Route::get('/programacion/novedades/crear', 'novedades_crear');

                        Route::post('/programacion/novedades/crear', 'novedades_crear_post');

                        Route::get('/ajax/programacion/novedades/cubrir_puesto/{id}', 'ajax_novedades_cubrir_puesto');

                        Route::post('/ajax/programacion/novedades/cubrir_puesto_consulta_actual_turno', 'ajax_cubrir_puesto_consulta_actual_turno');

                    });

                    Route::controller(recurso_humano::class)->group(function(){

                        Route::get('recurso_humano/config/mi_firma', 'config_mi_firma');

                        Route::post('recurso_humano/config/mi_firma', 'config_mi_firma_post');

                        Route::get('recurso_humano/editar_visita_domiciliaria/{usuario}', 'contratados_editar_visita_domiciliaria'); 

                        Route::get('/recurso_humano/faltas_disciplinarias', 'faltas_disciplinarias');

                        Route::get('/recurso_humano/faltas_disciplinarias/{id}/pdf', 'faltas_disciplinarias_pdf');

                        Route::get('/recurso_humano/faltas_disciplinarias/crear', 'faltas_disciplinarias_crear');

                        Route::post('/recurso_humano/faltas_disciplinarias/crear', 'faltas_disciplinarias_crear_post');

                        Route::post('/ajax/recurso_humano/faltas_disciplinarias/buscar_persona', 'ajax_faltas_disciplinarias_buscar_persona');

                        

                        Route::get('/recurso_humano/proceso_disciplinario', 'proceso_disciplinario');

                        Route::get('/recurso_humano/proceso_disciplinario/{id}/pdf', 'proceso_disciplinario_pdf');



                        Route::get('/recurso_humano/proceso_disciplinario/{id}', 'proceso_disciplinario_crear');



                        Route::post('/recurso_humano/proceso_disciplinario/{id}', 'proceso_disciplinario_crear_post');



                        Route::get('/recurso_humano/proceso_disciplinario/crear', 'proceso_disciplinario_crear');



                        Route::post('/recurso_humano/proceso_disciplinario/crear', 'proceso_disciplinario_crear_post');

                    });

                    Route::controller(indicador::class)->group(function(){

                        Route::get('/indicadores/zonas', 'zonas');

                        Route::get('/indicadores/gestion_operativa', 'gestion_operativa');

                    });

                    Route::controller(sst::class)->group(function(){

                        Route::get('/sst/chequeo_vehiculos', 'chequeo_vehiculos');

                        Route::get('/sst/chequeo_vehiculos/ver/{id}', 'chequeo_vehiculos_ver');

                        Route::get('/sst/chequeo_vehiculos/crear', 'chequeo_vehiculos_crear');

                        Route::get('/sst/inspeccion_seguridad', 'inspeccion_seguridad');

                        Route::get('/sst/inspeccion_seguridad/crear', 'inspeccion_seguridad_crear');

                        Route::get('/sst/inspeccion_seguridad/ver/{id}', 'inspeccion_seguridad_ver');

                    });

                    Route::controller(supervisor::class)->group(function(){

                        Route::get('/supervisor/supervision', 'supervision');

                        Route::get('/supervisor/supervision/{id}', 'supervision_cliente');

                        Route::get('/supervisor/supervision1/{id}', 'supervision_cliente1');

                    });



                    Route::controller(software::class)->group(function(){



                        Route::get('/software/requerimientos', 'requerimientos');



                        Route::get('/software/requerimientos/nuevo', 'requerimientos_nuevo');



                        Route::get('/software/requerimientos/ver/{id}', 'requerimientos_ver');



                        Route::get('/software/requerimientos/tiempos', 'requerimientos_tiempos');



                        Route::post('/software/requerimientos/ver/{id}', 'requerimientos_nuevo_post');



                        Route::post('/software/requerimientos/nuevo', 'requerimientos_nuevo_post');



                    });



                    Route::controller(tecnico::class)->group(function(){



                        Route::get('/tecnico/orden_servicio_tecnico', 'orden_servicio_tecnico');

                        Route::get('/tecnico/orden_servicio_tecnico/nueva', 'orden_servicio_tecnico_nueva');

                        Route::get('/tecnico/orden_servicio_tecnico/ver/{id}', 'orden_servicio_tecnico_ver');

                        Route::get('/tecnico/reporte_tecnico', 'reporte_tecnico');



                        Route::get('/tecnico/reporte_tecnico/nuevo', 'reporte_tecnico_nuevo');







                        Route::get('/tecnico/reporte_tecnico_se', 'reporte_tecnico_se');



                        Route::get('/tecnico/reporte_tecnico_se/crear', 'reporte_tecnico_se_crear');



                    });

                    Route::controller(usuarios::class)->group(function(){

                        Route::get('/usuarios/capacitaciones', 'usuarios_capacitaciones');

                        Route::get('/usuarios/capacitaciones/nueva', 'usuarios_capacitaciones_nueva');
                        Route::get('/usuarios/capacitaciones/editar/{id}', 'usuarios_capacitaciones_nueva');

                        Route::get('/ajax/usuarios/capacitaciones/asistentes/{id}', 'usuarios_capacitaciones_asistentes');

                        Route::get('/usuarios/capacitaciones/ver/{id}', 'capacitaciones_ver');



                    });



                    Route::controller(recurso_humano::class)->group(function(){



                        Route::get('recurso_humano/crear_persona', 'crear_persona');



                        Route::post('recurso_humano/crear_persona', 'crear_persona_post');



                        Route::get('recurso_humano/contratacion/{id}', 'contratacion');



                        Route::post('recurso_humano/contratacion/{id}/contratar', 'contratacion_contratar');



                        Route::get('recurso_humano/editar_usuario/{id}', 'editar_usuario');



                        Route::get('recurso_humano/en_contratacion', 'en_contratacion');



                        Route::get('recurso_humano/contratados', 'contratados');



                        Route::get('recurso_humano/contratados/{id}', 'contratados_ver');



                        Route::post('recurso_humano/contratados/{id}/agregar_documento', 'contratados_agregar_documento');



                        Route::get('recurso_humano/descartados', 'descartados');



                        Route::get('recurso_humano/retirados', 'retirados');

                        Route::get('recurso_humano/retirados/encuestas', 'retirados_encuestas');

                        Route::get('recurso_humano/retirados/encuestas/{id}', 'retirados_encuestas_ver');

                        Route::get('recurso_humano/retirados/encuestas/editar/{id}', 'retirados_encuestas_editar');

                        Route::get('recurso_humano/retirados/nuevo', 'retirados_nuevo');



                        Route::post('recurso_humano/retirados/nuevo', 'retirados_nuevo_post');



                        Route::get('recurso_humano/reportes/poblacion', 'reporte_poblacion');



                        Route::post('recurso_humano/reportes/poblacion', 'reporte_poblacion_post_filtros');



                        Route::get('recurso_humano/reportes/vacaciones', 'reporte_vacaciones');



                        Route::get('recurso_humano/reportes/guardas_clientes', 'reporte_guardas_clientes');



                        Route::get('recurso_humano/config/general', 'config_general');



                        Route::get('recurso_humano/config/cargos', 'config_cargos');



                        Route::get('recurso_humano/config/notificaciones', 'config_notificaciones');



                        Route::get('recurso_humano/config/mi_firma', 'config_mi_firma');



                        Route::post('recurso_humano/config/mi_firma', 'config_mi_firma_post');







                        Route::get('recurso_humano/ver_entrevista/{usuario}', 'contratados_ver_entrevista'); 



                        Route::get('recurso_humano/editar_entrevista/{usuario}', 'contratados_editar_entrevista'); 



                        Route::get('recurso_humano/ver_verificacion_referencias/{usuario}', 'contratados_ver_verificacion_referencias'); 



                        Route::get('recurso_humano/editar_verificacion_referencias/{usuario}', 'contratados_editar_verificacion_referencias'); 



                        Route::get('recurso_humano/editar_visita_domiciliaria/{usuario}', 'contratados_editar_visita_domiciliaria'); 



                        Route::get('recurso_humano/ver_visita_domiciliaria/{usuario}', 'contratados_ver_visita_domiciliaria'); 







                        Route::get('/recurso_humano/proceso_disciplinario', 'proceso_disciplinario');

                        Route::get('/recurso_humano/faltas_disciplinarias', 'faltas_disciplinarias');

                        Route::get('/recurso_humano/faltas_disciplinarias/{id}/pdf', 'faltas_disciplinarias_pdf');

                        Route::get('/recurso_humano/faltas_disciplinarias/crear', 'faltas_disciplinarias_crear');

                        Route::post('/recurso_humano/faltas_disciplinarias/crear', 'faltas_disciplinarias_crear_post');

                        Route::post('/ajax/recurso_humano/faltas_disciplinarias/buscar_persona', 'ajax_faltas_disciplinarias_buscar_persona');



                        Route::get('/recurso_humano/proceso_disciplinario/{id}/pdf', 'proceso_disciplinario_pdf');



                        Route::get('/recurso_humano/proceso_disciplinario/{id}', 'proceso_disciplinario_crear');



                        Route::post('/recurso_humano/proceso_disciplinario/{id}', 'proceso_disciplinario_crear_post');



                        Route::get('/recurso_humano/proceso_disciplinario/crear', 'proceso_disciplinario_crear');



                        Route::post('/recurso_humano/proceso_disciplinario/crear', 'proceso_disciplinario_crear_post');







                        Route::get('/recurso_humano/contrato/{id}', 'genera_contrato');











                        Route::get('/cartas', 'cartas');



                        Route::get('/cartas/{carta}/{usuario}', 'carta_usuario');



                    });

                    Route::controller(clientes::class)->group(function(){



                        Route::get('/clientes', 'clientes');







                        Route::get('/clientes/visitas_reuniones', 'visitas_reuniones');



                        Route::get('/clientes/visitas_reuniones/nuevo', 'visitas_reuniones_nuevo');



                        Route::get('/clientes/visitas_reuniones/ver/{id}', 'visitas_reuniones_ver');







                        Route::get('/clientes/pqrs', 'clientes_pqrs');



                        Route::get('/clientes/pqrs/nuevo', 'clientes_pqrs_nuevo');



                        Route::post('/clientes/pqrs/nuevo', 'clientes_pqrs_nuevo_post');



                        Route::get('/clientes/pqrs/ver/{id}', 'clientes_pqrs_ver');



                        Route::get('/clientes/pqrs/editar/{id}', 'clientes_pqrs_editar');



                        Route::post('/clientes/pqrs/editar/{id}', 'clientes_pqrs_editar_post');







                        Route::get('/clientes/nuevo', 'clientes_nuevo');



                        Route::post('/clientes/nuevo', 'clientes_nuevo_post');



                        Route::get('/clientes/editar/{id}', 'clientes_editar');



                        Route::post('/clientes/editar/{id}', 'clientes_editar_post');



                        Route::get('/clientes/administrar/{id}', 'clientes_administrar');
                        Route::post('/ajax/clientes/administrar/info/{id}', 'clientes_administrar_info');
                        Route::post('/ajax/clientes/administrar/contactos/{id}', 'clientes_administrar_contactos');
                        Route::post('/ajax/clientes/administrar/puntos/{id}', 'clientes_administrar_puntos');
                        Route::post('/ajax/clientes/administrar/minuta/{id}', 'clientes_administrar_minuta');

                        Route::post('/ajax/clientes/administrar/copropiedades/{id}', 'clientes_administrar_copropiedades');
                        Route::post('/ajax/clientes/administrar/copropiedades', 'clientes_administrar_copropiedades_data');
                        Route::post('/ajax/clientes/administrar/copropiedades_grupo', 'clientes_administrar_copropiedad_grupo');
                        Route::post('/ajax/clientes/administrar/parqueadero/{id}', 'clientes_administrar_parqueadero');

                         Route::post('/clientes/administrar/regla_parqueadero/{id}', 'clientes_administrar_regla_parqueadero');

                        Route::post('/clientes/administrar/permisos_contacto', 'clientes_administrar_clientes_contacto');



                        Route::post('/clientes/administrar/asignar_contrasena', 'clientes_administrar_asignar_contrasena');



                        Route::get('/clientes/detalles/{id}', 'clientes_detalles');



                        Route::post('/clientes/adminstrar/{id}/puntos', 'clientes_administrar_puntos_crear_post');



                        Route::post('/clientes/adminstrar/{id}/puntos/editar', 'clientes_administrar_puntos_editar_post');



                        Route::post('/clientes/adminstrar/{id}/residentes', 'clientes_administrar_residentes_crear_post');



                        Route::post('/clientes/adminstrar/{id}/residentes/editar', 'clientes_administrar_residentes_editar_post');



                        Route::post('/clientes/adminstrar/{id}/tareas_punto/editar', 'clientes_administrar_tareas_punto_editar_post');



                        Route::post('/clientes/adminstrar/{id}/residentes/vehiculo_crear', 'clientes_administrar_residentes_vehiculo_crear_post');



                        Route::get('/clientes/visitas', 'visitas');



                        Route::get('/clientes/visitas/crear', 'visitas_crear');



                        Route::post('/clientes/visitas/crear/subefoto/{id}', 'visitas_crear_sube_foto');



                        Route::post('/clientes/visitas/sube_foto/{id}', 'visitas_crear_sube_foto_archivo');



                        Route::get('/clientes/visitas/finalizar/{id}', 'visitas_finalizar');



                        Route::get('/clientes/visitas/ver/{id}', 'ver');



                        Route::post('/clientes/adminstrar/{id}/residentes/cambio_cliente', 'administrar_residentes_cambio_cliente');



                        Route::get('/clientes/importar', 'clientes_importar');



                        Route::get('/clientes/residentes/importar', 'clientes_residentes_importar');

                        Route::get('/clientes/residentes_vehiculos/importar', 'clientes_residentes_y_vehiculos_importar');

                        Route::get('/clientes/residentes_vehiculos/importar/{cliente}', 'clientes_residentes_y_vehiculos_importar');



                        Route::get('/clientes/residentes/vehiculos/importar', 'clientes_residentes_vehiculos_importar');







                        Route::get('/clientes/residentes/importar/{cliente}', 'clientes_residentes_importar');



                        Route::get('/clientes/residentes/vehiculos/importar/{cliente}', 'clientes_residentes_vehiculos_importar');







                        Route::get('/clientes/analisis_riesgos', 'analisis_riesgos');



                        Route::get('/clientes/analisis_riesgos/crear', 'analisis_riesgos_crear');



                        Route::get('/clientes/analisis_riesgos/ver/{id}', 'analisis_riesgos_ver');



                        Route::any('/clientes/analisis_riesgos/editar/{id}', 'analisis_riesgos_editar');



                        Route::post('/clientes/analisis_riesgos/crear', 'analisis_riesgos_crear_post');







                        Route::get('/clientes/consignas_particulares', 'consignas_particulares');



                        Route::get('/clientes/consignas_particulares/crear', 'consignas_particulares_crear');



                        Route::post('/clientes/consignas_particulares/crear', 'consignas_particulares_crear_post');



                        Route::get('/clientes/consignas_particulares/ver/{id}', 'consignas_particulares_ver_pdf');







                        Route::get('/clientes/incidentes_seguridad', 'incidentes_seguridad');



                        Route::get('/clientes/incidentes_seguridad/nuevo', 'incidentes_seguridad_nuevo');







                    });



                    Route::controller(usuarios::class)->group(function(){



                        Route::get('/usuarios', 'usuarios');

                        

                        Route::get('/usuarios/capacitaciones', 'usuarios_capacitaciones');

                        Route::get('/usuarios/capacitaciones/nueva', 'usuarios_capacitaciones_nueva');
                        Route::get('/usuarios/capacitaciones/editar/{id}', 'usuarios_capacitaciones_nueva');

                        Route::get('/ajax/usuarios/capacitaciones/asistentes/{id}', 'usuarios_capacitaciones_asistentes');

                        Route::get('/usuarios/capacitaciones/ver/{id}', 'capacitaciones_ver');



                        Route::get('/usuarios/nuevo', 'usuarios_crear');



                        Route::get('/usuarios/editar/{id}', 'usuarios_editar');



                        Route::post('/usuarios/nuevo', 'usuarios_crear_post');



                        Route::post('/usuarios/editar/{id}', 'usuarios_editar_post');



                        Route::get('/usuarios/administrar/{id}', 'usuarios_administrar');



                        Route::get('/usuarios/rci', 'usuarios_rci');



                        Route::get('/usuarios/rci/ver/{id}', 'usuarios_rci_ver');



                        Route::get('/usuarios/evaluacion', 'evaluacion');



                        Route::post('/usuarios/evaluacion', 'evaluacion_crear');



                        Route::get('/usuarios/evaluaciones/editar/{id}', 'evaluacion_editar');



                        Route::get('/usuarios/evaluaciones/descargar/{id}', 'evaluacion_descargar');



                    });



                    Route::controller(config::class)->group(function(){



                        Route::get('/config/general', 'general');
                        Route::get('/config/tipo_novedad_cliente', 'tipo_novedad_cliente');
                        Route::post('/config/tipo_novedad_cliente/crear', 'tipo_novedad_cliente_crear');
                        Route::post('/config/tipo_novedad_cliente/editar', 'tipo_novedad_cliente_editar');
                        
                        

                        Route::get('/config/festivos', 'festivos');

                        Route::post('/config/festivos/crear', 'festivos_crear');



                        Route::post('/config/general', 'general_post');







                        Route::get('/config/tipos_cliente', 'tipos_cliente');

                        Route::post('/config/tipos_cliente/crear', 'tipos_cliente_crear');

                        Route::get('/config/tipos_cliente/editar/{id}', 'tipos_cliente_edit');

                        Route::post('/config/tipos_cliente/editar/{id}/crear_motivo', 'tipos_cliente_edit_crear_motivo');



                        Route::get('/config/tipos_vehiculo', 'tipos_vehiculo');



                        Route::post('/config/tipos_vehiculo/crear', 'tipos_vehiculo_crear');



                        Route::post('/config/tipos_vehiculo/editar', 'tipos_vehiculo_editar');







                        Route::get('/config/vehiculos_marca', 'vehiculos_marca');



                        Route::post('/config/vehiculos_marca/crear', 'vehiculos_marca_crear');



                        Route::post('/config/vehiculos_marca/editar', 'vehiculos_marca_editar');







                        Route::get('/config/tipos_visita', 'tipos_visita');



                        Route::post('/config/tipos_visita/crear', 'tipos_visita_crear');



                        Route::post('/config/tipos_visita/editar', 'tipos_visita_editar');







                        Route::get('/config/zonas', 'zonas');



                        Route::post('/config/zonas/crear', 'zonas_crear');



                        Route::post('/config/zonas/editar', 'zonas_editar');







                        Route::get('/config/evaluacion', 'evaluacion');



                        Route::post('/config/evaluacion/crear', 'evaluacion_crear');



                        Route::post('/config/evaluacion/editar', 'evaluacion_editar');







                        Route::get('/config/analisis_riesgos', 'analisis_riesgos');







                        Route::get('/config/items_dotacion', 'items_dotacion');



                        Route::post('/config/items_dotacion/crear', 'items_dotacion_crear');



                        Route::post('/config/items_dotacion/editar', 'items_dotacion_editar');



                        



                        Route::get('/config/tallas', 'tallas');



                        Route::post('/config/tallas/crear', 'tallas_crear');



                        Route::post('/config/tallas/editar', 'tallas_editar');







                        Route::get('/config/tallas_categorias', 'tallas_categorias');



                        Route::post('/config/tallas_categorias/crear', 'tallas_categorias_crear');



                        Route::post('/config/tallas_categorias/editar', 'tallas_categorias_editar');







                        Route::get('/config/cargos', 'cargos');



                        Route::post('/config/cargos/crear', 'cargos_crear');



                        Route::post('/config/cargos/editar', 'cargos_editar');







                        



                        Route::get('/config/documentos_gestion_humana', 'documentos_gestion_humana');



                        Route::post('/config/documentos_gestion_humana/crear', 'documentos_gestion_humana_crear');



                        Route::post('/config/documentos_gestion_humana/editar', 'documentos_gestion_humana_editar');











                        Route::get('/config/entrevista_aspecto', 'entrevista_aspecto');



                        Route::post('/config/entrevista_aspecto/crear', 'entrevista_aspecto_crear');



                        Route::post('/config/entrevista_aspecto/editar', 'entrevista_aspecto_editar');







                        Route::get('/config/entrevista_factor', 'entrevista_factor');



                        Route::post('/config/entrevista_factor/crear', 'entrevista_factor_crear');



                        Route::post('/config/entrevista_factor/editar', 'entrevista_factor_editar');







                         Route::get('/config/zonas', 'zonas');



                        Route::post('/config/zonas/crear', 'zonas_crear');



                        Route::post('/config/zonas/editar', 'zonas_editar');



                        



                    });



                    Route::controller(reportes::class)->group(function(){



                        Route::get('/reportes/login', 'login');

                        Route::get('/reportes/problemas_marcacion', 'problemas_marcacion');



                        Route::any('/reportes/reporte_tecnico', 'reporte_tecnico');



                        Route::any('/reportes/reporte_tecnico/costos', 'reporte_tecnico_costos');



                        Route::any('/reportes/seguimiento_usuarios', 'seguimiento_usuarios');



                        Route::any('/reportes/record_patrullaje', 'record_patrullaje');
                        Route::post('/ajax/reportes/record_patrullaje_resultados', 'record_patrullaje_resultados');



                        Route::any('/reportes/record_patrullaje_global', 'record_patrullaje_global');



                        Route::any('/reportes/cumplimiento', 'cumplimiento');



                        Route::any('/reportes/cumplimiento/informe_pdf', 'cumplimiento_informe_pdf');



                        Route::any('/reportes/correspondencia', 'correspondencia');

                        Route::any('/reportes/capacitaciones', 'capacitaciones');



                        Route::get('/reportes/correspondencia/pdf', 'correspondencia_pdf');



                        Route::any('/reportes/vehiculos', 'vehiculos');



                        Route::any('/reportes/vehiculos_base_datos', 'vehiculos_base_datos');



                        Route::any('/reportes/control_vehiculos', 'control_vehiculos');



                        Route::get('/reportes/minuta', 'minuta');



                        Route::post('/reportes/minuta', 'minuta_post');



                        Route::any('/reportes/visitantes', 'visitantes');



                        Route::any('/reportes/visitantes/pdf', 'visitantes_pdf');



                        Route::any('/reportes/control_equipo_comunicacion', 'control_equipo_comunicacion');



                        Route::any('/reportes/visitas_analista', 'visitas_analista');



                        Route::any('/reportes/control_ingreso_salida', 'control_ingreso_salida');



                        Route::any('/reportes/control_ingreso_salida/descargar/{id}', 'control_ingreso_salida_descargar');



                        Route::any('/reportes/kilometraje_supervisores', 'kilometraje_supervisores');



                        Route::any('/reportes/novedad_cliente', 'novedad_cliente');



                        Route::any('/reportes/novedad_vigsecol', 'novedad_vigsecol');



                        Route::any('/reportes/novedad_cliente/{id}', 'novedad_cliente_ver_pdf');



                        Route::any('/reportes/novedad_vigsecol/{id}', 'novedad_vigsecol_ver_pdf');



                        Route::any('/reportes/evaluaciones', 'evaluaciones');



                    });



                    break;



                ///////////////////////////////////VIGILANTE  ///////////////////////////////////



                case '2':



                    Route::controller(ajax::class)->group(function(){

                        Route::get('/concepto/{concepto}', 'concepto');

                    });

                    Route::controller(clientes::class)->group(function(){



                        Route::get('/novedad_cliente/{cliente}', 'novedad_cliente');



                        Route::post('/novedad_cliente/{cliente}', 'novedad_cliente_post');



                        Route::get('/novedad_vigsecol/{cliente}', 'novedad_vigsecol');



                        Route::post('/novedad_vigsecol/{cliente}', 'novedad_vigsecol_post');



                    });

                  



                    Route::controller(vigilante::class)->group(function(){



                        Route::get('/manuales_instructivos', 'manuales_instructivos');

                        Route::get('/ingresos_salidas', 'ingresos_salidas');

                        Route::get('/eventos', 'eventos');



                        Route::get('/ingresos_salidas_ajax', 'ingresos_salidas_ajax');





                        Route::get('/ingresos_salidas2', 'ingresos_salidas2');



                        Route::get('/ingresos_salidas_ajax2', 'ingresos_salidas_ajax2');



                        Route::get('/vehiculos', 'vehiculos');

                        Route::get('/vehiculos2', 'vehiculos');



                        Route::get('/vehiculos/historial', 'vehiculos_historial');



                        Route::post('/vehiculos', 'vehiculos_post');



                        Route::get('/minuta', 'minuta');



                        Route::post('/minuta', 'minuta_post');



                        Route::get('/marcacion', 'marcacion');



                        Route::get('/marcacion/apertura_ronda', 'marcacion_apertura_ronda');



                        Route::post('/marcacion/apertura_ronda', 'marcacion_apertura_ronda_post');



                        Route::post('/marcacion/finalizar_ronda', 'marcacion_finalizar_ronda_post');



                        Route::get('/marcacion/confirmar/{id}', 'marcacion_confirmar');



                        Route::post('/marcacion/confirmar/subefoto', 'marcacion_confirmar_sube_foto');



                        Route::post('/marcacion/confirmar/{id}', 'marcacion_confirmar_post');



                        Route::get('/visitantes', 'visitantes');



                        Route::get('/visitantes_vs2', 'visitantes_vs2');



                        Route::post('/visitantes/subefoto', 'visitantes_sube_foto');



                        Route::post('/correspondencia/subefoto', 'correspondencia_sube_foto');



                        Route::post('/visitantes', 'visitantes_post');

                        Route::post('/visitantes_vs2', 'visitantes_vs2_post'); 



                        Route::get('/correspondencia', 'correspondencia');



                        Route::get('/clientes/adminstrar/{id}/residentes', 'clientes_administrar_residentes');



                        Route::post('/clientes/adminstrar/{id}/residentes', 'clientes_administrar_residentes_crear_post');



                        Route::post('/clientes/adminstrar/{id}/residentes/editar', 'clientes_administrar_residentes_editar_post');



                        Route::post('/clientes/adminstrar/{id}/residentes/vehiculo_crear', 'clientes_administrar_residentes_vehiculo_crear_post');







                        Route::get('/reporte_condiciones_inseguras', 'reporte_condiciones_inseguras');



                        Route::post('/reporte_condiciones_inseguras/subefoto', 'reporte_condiciones_inseguras_subefoto');



                    });



                    break;



                ///////////////////////////////////SUPERVISOR /////////////////////////////////// 



                case '3':



                    if($_SESSION['ingreso_salida']=="" && !strpos($_SERVER['REQUEST_URI'], 'ingreso_salida'))



                    {



                        header('Location: /ingreso_salida');



                        exit();



                    }



                    else



                    {



                    }

                    Route::controller(sst::class)->group(function(){

                        Route::get('/sst/chequeo_vehiculos', 'chequeo_vehiculos');

                        Route::get('/sst/chequeo_vehiculos/ver/{id}', 'chequeo_vehiculos_ver');

                        Route::get('/sst/chequeo_vehiculos/crear', 'chequeo_vehiculos_crear');

                        // Route::get('/sst/inspeccion_seguridad', 'inspeccion_seguridad');

                        // Route::get('/sst/inspeccion_seguridad/crear', 'inspeccion_seguridad_crear');

                        // Route::get('/sst/inspeccion_seguridad/ver/{id}', 'inspeccion_seguridad_ver');

                    });

                    Route::controller(usuarios::class)->group(function(){

                        Route::get('/usuarios/capacitaciones', 'usuarios_capacitaciones');

                        Route::get('/usuarios/capacitaciones/nueva', 'usuarios_capacitaciones_nueva');
                        Route::get('/usuarios/capacitaciones/editar/{id}', 'usuarios_capacitaciones_nueva');

                        Route::get('/ajax/usuarios/capacitaciones/asistentes/{id}', 'usuarios_capacitaciones_asistentes');

                        Route::get('/usuarios/capacitaciones/ver/{id}', 'capacitaciones_ver');



                    });

                    Route::controller(recurso_humano::class)->group(function(){

                        Route::get('recurso_humano/config/mi_firma', 'config_mi_firma');

                        Route::post('recurso_humano/config/mi_firma', 'config_mi_firma_post');

                        Route::get('recurso_humano/editar_visita_domiciliaria/{usuario}', 'contratados_editar_visita_domiciliaria'); 

                        Route::get('/recurso_humano/proceso_disciplinario', 'proceso_disciplinario');

                        Route::get('/recurso_humano/faltas_disciplinarias', 'faltas_disciplinarias');

                        Route::get('/recurso_humano/faltas_disciplinarias/{id}/pdf', 'faltas_disciplinarias_pdf');

                        Route::get('/recurso_humano/faltas_disciplinarias/crear', 'faltas_disciplinarias_crear');

                        Route::post('/recurso_humano/faltas_disciplinarias/crear', 'faltas_disciplinarias_crear_post');

                        Route::post('/ajax/recurso_humano/faltas_disciplinarias/buscar_persona', 'ajax_faltas_disciplinarias_buscar_persona');

                        Route::get('/recurso_humano/proceso_disciplinario/{id}/pdf', 'proceso_disciplinario_pdf');

                        Route::get('/recurso_humano/proceso_disciplinario/{id}', 'proceso_disciplinario_crear');

                        Route::post('/recurso_humano/proceso_disciplinario/{id}', 'proceso_disciplinario_crear_post');

                        Route::get('/recurso_humano/proceso_disciplinario/crear', 'proceso_disciplinario_crear');

                        Route::post('/recurso_humano/proceso_disciplinario/crear', 'proceso_disciplinario_crear_post');

                    });

                    Route::controller(clientes::class)->group(function(){



                        Route::get('/novedad_cliente', 'novedad_cliente_inicio');



                        Route::get('/novedad_vigsecol', 'novedad_vigsecol_inicio');



                        Route::get('/clientes/pqrs/nuevo', 'clientes_pqrs_nuevo');

                        Route::post('/clientes/pqrs/nuevo', 'clientes_pqrs_nuevo_post');



                        Route::get('/novedad_cliente/{cliente}', 'novedad_cliente');



                        Route::post('/novedad_cliente/{cliente}', 'novedad_cliente_post');



                        Route::get('/novedad_vigsecol/{cliente}', 'novedad_vigsecol');



                        Route::post('/novedad_vigsecol/{cliente}', 'novedad_vigsecol_post');







                        



                        Route::get('/clientes/pqrs', 'clientes_pqrs');



                        Route::get('/clientes/pqrs/ver/{id}', 'clientes_pqrs_ver');



                        Route::get('/clientes/pqrs/editar/{id}', 'clientes_pqrs_editar');



                        Route::post('/clientes/pqrs/editar/{id}', 'clientes_pqrs_editar_post');



                    });



                    Route::controller(supervisor::class)->group(function(){



                        Route::get('/ingreso_salida', 'ingreso_salida');



                        Route::post('/ingreso_salida', 'ingreso_salida_post');



                        Route::get('/supervisor/control_equipo_comunicacion', 'control_equipo_comunicacion');



                        Route::post('/supervisor/control_equipo_comunicacion', 'control_equipo_comunicacion_post');



                        Route::get('/supervisor/supervision', 'supervision');

                        Route::get('/supervisor/supervision/{id}', 'supervision_cliente');



                        Route::get('/supervisor/marcacion', 'marcacion');



                        Route::post('/supervisor/marcacion', 'marcacion_by_id');



                        Route::get('/supervisor/minuta', 'minuta');



                        Route::post('/supervisor/minuta', 'minuta_post');



                        Route::get('/marcacion/confirmar/{id}', 'marcacion_confirmar');



                        Route::post('/marcacion/confirmar/{id}', 'marcacion_confirmar_post');



                        Route::post('/supervisor/marcacion/subefoto/{id}', 'marcacion_sube_fotos');



                        Route::post('/supervisor/ingreso_salida/subefoto/{id}', 'ingreso_salida_sube_fotos');



                        Route::post('/supervisor/captura-firma/{id}', 'captura_firma_marcacion');



                    });



                     Route::controller(usuarios::class)->group(function(){



                        Route::get('/usuarios', 'usuarios');



                        Route::get('/usuarios/nuevo', function(){

                            return redirect('/no_autorizado');

                        });



                        Route::get('/usuarios/editar/{id}', 'usuarios_editar');



                        Route::post('/usuarios/nuevo', 'usuarios_crear_post');



                        Route::post('/usuarios/editar/{id}', 'usuarios_editar_post');



                        Route::get('/usuarios/administrar/{id}', 'usuarios_administrar');



                        Route::get('/usuarios/evaluacion', 'evaluacion');



                        Route::post('/usuarios/evaluacion', 'evaluacion_crear');



                        Route::get('/usuarios/evaluaciones/editar/{id}', 'evaluacion_editar');



                        Route::get('/usuarios/evaluaciones/descargar/{id}', 'evaluacion_descargar');



                    });



                    Route::controller(reportes::class)->group(function(){



                        Route::any('/reportes/correspondencia', 'correspondencia');



                        Route::get('/reportes/correspondencia/pdf', 'correspondencia_pdf');



                        Route::any('/reportes/vehiculos', 'vehiculos');



                        Route::any('/reportes/vehiculos_base_datos', 'vehiculos_base_datos');



                        Route::any('/reportes/control_vehiculos', 'control_vehiculos');



                        Route::any('/reportes/control_equipo_comunicacion', 'control_equipo_comunicacion');



                        Route::any('/reportes/novedad_cliente', 'novedad_cliente');



                        Route::any('/reportes/novedad_vigsecol', 'novedad_vigsecol');



                        Route::any('/reportes/novedad_cliente/{id}', 'novedad_cliente_ver_pdf');



                        Route::any('/reportes/novedad_vigsecol/{id}', 'novedad_vigsecol_ver_pdf');



                    });



                    break;  



                case '4':

                    Route::controller(programacion::class)->group(function(){

                        Route::get('/programacion', 'inicio');

                        Route::post('/programacion/novedad_turno', 'novedad_turno');

                        Route::post('/programacion/descargar', 'descargar');

                        Route::get('/programacion/puesto', 'puesto');

                        Route::get('/programacion/puesto/{id}', 'puesto');

                        Route::get('/ajax/componentes/contenedor_programacion', 'componente_contenedor_programacion');

                    });

                    Route::controller(supervisor::class)->group(function(){

                        Route::get('/supervisor/supervision', 'supervision');

                        Route::get('/supervisor/supervision/{id}', 'supervision_cliente');

                    });

                        Route::controller(recurso_humano::class)->group(function(){

                            Route::get('recurso_humano/descargos', 'descargos');

                            Route::get('recurso_humano/descargos/crear', 'descargos_crear');

                            Route::get('recurso_humano/descargos/ver/{id}', 'descargos_ver');

                            Route::any('/cartas/carta_recomendacion', 'carta_recomendacion_cliente');

                            Route::get('recurso_humano/config/mi_firma', 'config_mi_firma');

                            Route::post('recurso_humano/config/mi_firma', 'config_mi_firma_post');

                            Route::get('recurso_humano/editar_visita_domiciliaria/{usuario}', 'contratados_editar_visita_domiciliaria');

                            Route::get('/recurso_humano/proceso_disciplinario', 'proceso_disciplinario');

                            Route::get('/recurso_humano/faltas_disciplinarias', 'faltas_disciplinarias');

                            Route::get('/recurso_humano/faltas_disciplinarias/{id}/pdf', 'faltas_disciplinarias_pdf');

                            Route::get('/recurso_humano/faltas_disciplinarias/crear', 'faltas_disciplinarias_crear');

                            Route::post('/recurso_humano/faltas_disciplinarias/crear', 'faltas_disciplinarias_crear_post');

                            Route::post('/ajax/recurso_humano/faltas_disciplinarias/buscar_persona', 'ajax_faltas_disciplinarias_buscar_persona');

                            Route::get('/recurso_humano/proceso_disciplinario/{id}/pdf', 'proceso_disciplinario_pdf');

                            Route::get('/recurso_humano/proceso_disciplinario/{id}', 'proceso_disciplinario_crear');

                            Route::post('/recurso_humano/proceso_disciplinario/{id}', 'proceso_disciplinario_crear_post');

                            Route::get('/recurso_humano/proceso_disciplinario/crear', 'proceso_disciplinario_crear');

                            Route::post('/recurso_humano/proceso_disciplinario/crear', 'proceso_disciplinario_crear_post'); 

                            Route::get('recurso_humano/contratados', 'contratados');

                            Route::get('recurso_humano/ver_visita_domiciliaria/{usuario}', 'contratados_ver_visita_domiciliaria'); 

                            Route::get('recurso_humano/contratados/{id}', 'contratados_ver');

                        });

                        Route::controller(sst::class)->group(function(){

                            Route::get('/sst/chequeo_vehiculos', 'chequeo_vehiculos');

                            Route::get('/sst/chequeo_vehiculos/ver/{id}', 'chequeo_vehiculos_ver');

                            Route::get('/sst/chequeo_vehiculos/crear', 'chequeo_vehiculos_crear');

                            Route::get('/sst/inspeccion_seguridad', 'inspeccion_seguridad');

                            Route::get('/sst/inspeccion_seguridad/crear', 'inspeccion_seguridad_crear');

                            Route::get('/sst/inspeccion_seguridad/ver/{id}', 'inspeccion_seguridad_ver');

                        });

                        Route::controller(clientes::class)->group(function(){



                        

                        Route::get('/clientes/importar', 'clientes_importar');



                        Route::get('/clientes/residentes/importar', 'clientes_residentes_importar');

                        Route::get('/clientes/residentes_vehiculos/importar', 'clientes_residentes_y_vehiculos_importar');

                        Route::get('/clientes/residentes_vehiculos/importar/{cliente}', 'clientes_residentes_y_vehiculos_importar');



                        Route::get('/clientes/residentes/vehiculos/importar', 'clientes_residentes_vehiculos_importar');







                        Route::get('/clientes/residentes/importar/{cliente}', 'clientes_residentes_importar');



                        Route::get('/clientes/residentes/vehiculos/importar/{cliente}', 'clientes_residentes_vehiculos_importar');



                        Route::get('/clientes/pqrs', 'clientes_pqrs');

                        Route::get('/clientes/vigilantes', 'clientes_vigilantes');



                        Route::post('/clientes/vigilantes', 'clientes_vigilantes_post');



                        Route::get('/clientes/pqrs/ver/{id}', 'clientes_pqrs_ver');



                        Route::get('/clientes/pqrs/editar/{id}', 'clientes_pqrs_editar');



                        Route::post('/clientes/pqrs/editar/{id}', 'clientes_pqrs_editar_post');







                        Route::get('/clientes', 'clientes');



                        Route::get('/clientes/nuevo', 'clientes_nuevo');



                        Route::post('/clientes/nuevo', 'clientes_nuevo_post');



                        Route::get('/clientes/editar/{id}', 'clientes_editar');



                        Route::post('/clientes/editar/{id}', 'clientes_editar_post');



                        Route::get('/clientes/administrar/{id}', 'clientes_administrar');
                        Route::post('/ajax/clientes/administrar/info/{id}', 'clientes_administrar_info');
                        Route::post('/ajax/clientes/administrar/contactos/{id}', 'clientes_administrar_contactos');
                        Route::post('/ajax/clientes/administrar/puntos/{id}', 'clientes_administrar_puntos');
                        Route::post('/ajax/clientes/administrar/minuta/{id}', 'clientes_administrar_minuta');

                        Route::post('/ajax/clientes/administrar/copropiedades/{id}', 'clientes_administrar_copropiedades');
                        Route::post('/ajax/clientes/administrar/copropiedades', 'clientes_administrar_copropiedades_data');
                        Route::post('/ajax/clientes/administrar/copropiedades_grupo', 'clientes_administrar_copropiedad_grupo');
                        Route::post('/ajax/clientes/administrar/parqueadero/{id}', 'clientes_administrar_parqueadero');

                         Route::post('/clientes/administrar/regla_parqueadero/{id}', 'clientes_administrar_regla_parqueadero');

                        Route::post('/clientes/administrar/permisos_contacto', 'clientes_administrar_clientes_contacto');



                        Route::get('/clientes/detalles/{id}', 'clientes_detalles');



                        Route::post('/clientes/adminstrar/{id}/puntos', 'clientes_administrar_puntos_crear_post');



                        Route::post('/clientes/adminstrar/{id}/tareas_punto/editar', 'clientes_administrar_tareas_punto_editar_post');



                        Route::post('/clientes/adminstrar/{id}/puntos/editar', 'clientes_administrar_puntos_editar_post');



                        Route::post('/clientes/adminstrar/{id}/residentes', 'clientes_administrar_residentes_crear_post');



                        Route::post('/clientes/adminstrar/{id}/residentes/editar', 'clientes_administrar_residentes_editar_post');



                        Route::post('/clientes/adminstrar/{id}/residentes/vehiculo_crear', 'clientes_administrar_residentes_vehiculo_crear_post');



                        Route::get('/clientes/visitas', 'visitas');



                        Route::get('/clientes/visitas/crear', 'visitas_crear');



                        Route::post('/clientes/visitas/crear/subefoto/{id}', 'visitas_crear_sube_foto');



                        Route::post('/clientes/visitas/sube_foto/{id}', 'visitas_crear_sube_foto_archivo');



                        Route::get('/clientes/visitas/finalizar/{id}', 'visitas_finalizar');



                        Route::get('/clientes/visitas/ver/{id}', 'ver');



                        Route::get('/clientes/analisis_riesgos', 'analisis_riesgos');



                        Route::get('/clientes/analisis_riesgos/crear', 'analisis_riesgos_crear');



                        Route::get('/clientes/analisis_riesgos/ver/{id}', 'analisis_riesgos_ver');



                        Route::post('/clientes/analisis_riesgos/crear', 'analisis_riesgos_crear_post');



                        Route::get('/clientes/analisis_riesgos/ver/{id}', 'analisis_riesgos_ver');



                        Route::any('/clientes/analisis_riesgos/editar/{id}', 'analisis_riesgos_editar');







                        Route::get('/clientes/consignas_particulares', 'consignas_particulares');



                        Route::get('/clientes/consignas_particulares/crear', 'consignas_particulares_crear');



                        Route::post('/clientes/consignas_particulares/crear', 'consignas_particulares_crear_post');



                        Route::get('/clientes/consignas_particulares/ver/{id}', 'consignas_particulares_ver_pdf');







                        Route::post('/clientes/adminstrar/{id}/residentes/cambio_cliente', 'administrar_residentes_cambio_cliente');







                        Route::get('/clientes/incidentes_seguridad', 'incidentes_seguridad');



                        Route::get('/clientes/incidentes_seguridad/nuevo', 'incidentes_seguridad_nuevo');

                        Route::post('/clientes/administrar/asignar_contrasena', 'clientes_administrar_asignar_contrasena');





                    });



                    Route::controller(reportes::class)->group(function(){



                        Route::any('/reportes/novedad_cliente', 'novedad_cliente');



                        Route::any('/reportes/novedad_vigsecol', 'novedad_vigsecol');



                        Route::any('/reportes/novedad_cliente/{id}', 'novedad_cliente_ver_pdf');



                        Route::any('/reportes/novedad_vigsecol/{id}', 'novedad_vigsecol_ver_pdf');



                        

                        Route::get('/reportes/problemas_marcacion', 'problemas_marcacion');



                        Route::any('/reportes/reporte_tecnico', 'reporte_tecnico');



                        Route::any('/reportes/seguimiento_usuarios', 'seguimiento_usuarios');



                        Route::any('/reportes/record_patrullaje', 'record_patrullaje');
                        Route::post('/ajax/reportes/record_patrullaje_resultados', 'record_patrullaje_resultados');



                        Route::any('/reportes/record_patrullaje_global', 'record_patrullaje_global');







                        Route::any('/reportes/cumplimiento', 'cumplimiento');



                        Route::any('/reportes/cumplimiento/informe_pdf', 'cumplimiento_informe_pdf');



                        Route::any('/reportes/correspondencia', 'correspondencia');



                        Route::get('/reportes/correspondencia/pdf', 'correspondencia_pdf');



                        Route::any('/reportes/vehiculos', 'vehiculos');



                        Route::any('/reportes/vehiculos_base_datos', 'vehiculos_base_datos');



                        Route::any('/reportes/control_vehiculos', 'control_vehiculos');



                        Route::any('/reportes/visitantes', 'visitantes');



                        Route::any('/reportes/control_equipo_comunicacion', 'control_equipo_comunicacion');







                        Route::any('/reportes/visitas_analista', 'visitas_analista');



                        Route::any('/reportes/control_ingreso_salida', 'control_ingreso_salida');



                        Route::any('/reportes/control_ingreso_salida/descargar/{id}', 'control_ingreso_salida_descargar');



                        Route::any('/reportes/kilometraje_supervisores', 'kilometraje_supervisores');







                    });



                    Route::controller(usuarios::class)->group(function(){

                        Route::get('/usuarios/visitas_domiciliarias', 'usuarios_visitas_domiciliarias');

                        Route::post('/usuarios/visitas_domiciliarias', 'usuarios_visitas_domiciliarias_post');

                        Route::get('/usuarios', 'usuarios');



                        Route::get('/usuarios/capacitaciones', 'usuarios_capacitaciones');

                        Route::get('/usuarios/capacitaciones/nueva', 'usuarios_capacitaciones_nueva');
                        Route::get('/usuarios/capacitaciones/editar/{id}', 'usuarios_capacitaciones_nueva');

                        Route::get('/ajax/usuarios/capacitaciones/asistentes/{id}', 'usuarios_capacitaciones_asistentes');

                        Route::get('/usuarios/capacitaciones/ver/{id}', 'capacitaciones_ver');



                        Route::get('/usuarios/nuevo', function(){

                            return redirect('/no_autorizado');

                        });



                        Route::get('/usuarios/editar/{id}', 'usuarios_editar');



                        Route::post('/usuarios/nuevo', 'usuarios_crear_post');



                        Route::post('/usuarios/editar/{id}', 'usuarios_editar_post');



                        Route::get('/usuarios/administrar/{id}', 'usuarios_administrar');



                        Route::get('/usuarios/evaluacion', 'evaluacion');



                        Route::post('/usuarios/evaluacion', 'evaluacion_crear');



                        Route::get('/usuarios/evaluaciones/editar/{id}', 'evaluacion_editar');



                        Route::get('/usuarios/evaluaciones/descargar/{id}', 'evaluacion_descargar');



                    });



                    break;



                case '5':
                    Route::controller(campanias::class)->group(function(){
                        Route::get('/campanias/nueva', 'campanias_nueva');
                        Route::get('/campanias/ver', 'campanias_ver');

                    }); 
                    Route::controller(indicador::class)->group(function(){

                        Route::get('/indicadores/zonas', 'zonas');

                    });

                    Route::controller(sst::class)->group(function(){

                        Route::get('/sst/chequeo_vehiculos', 'chequeo_vehiculos');

                        Route::get('/sst/chequeo_vehiculos/ver/{id}', 'chequeo_vehiculos_ver');

                        Route::get('/sst/chequeo_vehiculos/crear', 'chequeo_vehiculos_crear');

                        Route::get('/sst/inspeccion_seguridad', 'inspeccion_seguridad');

                        Route::get('/sst/inspeccion_seguridad/crear', 'inspeccion_seguridad_crear');

                        Route::get('/sst/inspeccion_seguridad/ver/{id}', 'inspeccion_seguridad_ver');

                    });

                    Route::controller(supervisor::class)->group(function(){

                        Route::get('/supervisor/supervision', 'supervision');

                        Route::get('/supervisor/supervision/{id}', 'supervision_cliente');

                    });

                    Route::controller(recurso_humano::class)->group(function(){

                        Route::any('/cartas/carta_recomendacion', 'carta_recomendacion_cliente');



                        Route::get('recurso_humano/descargos', 'descargos');

                        Route::get('recurso_humano/descargos/crear', 'descargos_crear');

                        Route::get('recurso_humano/descargos/ver/{id}', 'descargos_ver');



                        Route::get('recurso_humano/config/mi_firma', 'config_mi_firma');

                        Route::post('recurso_humano/config/mi_firma', 'config_mi_firma_post');

                        Route::get('recurso_humano/editar_visita_domiciliaria/{usuario}', 'contratados_editar_visita_domiciliaria'); 

                        Route::get('/recurso_humano/proceso_disciplinario', 'proceso_disciplinario');

                        Route::get('/recurso_humano/faltas_disciplinarias', 'faltas_disciplinarias');

                        Route::get('/recurso_humano/faltas_disciplinarias/{id}/pdf', 'faltas_disciplinarias_pdf');

                        Route::get('/recurso_humano/faltas_disciplinarias/crear', 'faltas_disciplinarias_crear');

                        Route::post('/recurso_humano/faltas_disciplinarias/crear', 'faltas_disciplinarias_crear_post');

                        Route::post('/ajax/recurso_humano/faltas_disciplinarias/buscar_persona', 'ajax_faltas_disciplinarias_buscar_persona');



                        Route::get('/recurso_humano/proceso_disciplinario/{id}/pdf', 'proceso_disciplinario_pdf');



                        Route::get('/recurso_humano/proceso_disciplinario/{id}', 'proceso_disciplinario_crear');



                        Route::post('/recurso_humano/proceso_disciplinario/{id}', 'proceso_disciplinario_crear_post');



                        Route::get('/recurso_humano/proceso_disciplinario/crear', 'proceso_disciplinario_crear');



                        Route::post('/recurso_humano/proceso_disciplinario/crear', 'proceso_disciplinario_crear_post');

                        Route::get('recurso_humano/contratados', 'contratados');

                        Route::get('recurso_humano/ver_visita_domiciliaria/{usuario}', 'contratados_ver_visita_domiciliaria'); 



                        Route::get('recurso_humano/contratados/{id}', 'contratados_ver');

                    });

                      Route::controller(clientes::class)->group(function(){



                        Route::get('/clientes/nuevo', 'clientes_nuevo');



                        Route::post('/clientes/nuevo', 'clientes_nuevo_post');



                        Route::get('/clientes/editar/{id}', 'clientes_editar');



                        Route::post('/clientes/editar/{id}', 'clientes_editar_post');

                        

                        Route::get('/clientes/visitas_reuniones', 'visitas_reuniones');



                        Route::get('/clientes/visitas_reuniones/nuevo', 'visitas_reuniones_nuevo');



                        Route::get('/clientes/visitas_reuniones/ver/{id}', 'visitas_reuniones_ver');



                        Route::get('/clientes/pqrs', 'clientes_pqrs');



                       



                        Route::get('/clientes/pqrs/ver/{id}', 'clientes_pqrs_ver');



                        Route::get('/clientes/pqrs/editar/{id}', 'clientes_pqrs_editar');



                        Route::post('/clientes/pqrs/editar/{id}', 'clientes_pqrs_editar_post');







                        Route::get('/clientes', 'clientes');



                        Route::get('/clientes/editar/{id}', 'clientes_editar');



                        Route::post('/clientes/editar/{id}', 'clientes_editar_post');



                        Route::get('/clientes/administrar/{id}', 'clientes_administrar');
                        Route::post('/ajax/clientes/administrar/info/{id}', 'clientes_administrar_info');
                        Route::post('/ajax/clientes/administrar/contactos/{id}', 'clientes_administrar_contactos');
                        Route::post('/ajax/clientes/administrar/puntos/{id}', 'clientes_administrar_puntos');
                        Route::post('/ajax/clientes/administrar/minuta/{id}', 'clientes_administrar_minuta');

                        Route::post('/ajax/clientes/administrar/copropiedades/{id}', 'clientes_administrar_copropiedades');
                        Route::post('/ajax/clientes/administrar/copropiedades', 'clientes_administrar_copropiedades_data');
                        Route::post('/ajax/clientes/administrar/copropiedades_grupo', 'clientes_administrar_copropiedad_grupo');
                        Route::post('/ajax/clientes/administrar/parqueadero/{id}', 'clientes_administrar_parqueadero');

                         Route::post('/clientes/administrar/regla_parqueadero/{id}', 'clientes_administrar_regla_parqueadero');

                        Route::post('/clientes/administrar/permisos_contacto', 'clientes_administrar_clientes_contacto');



                        Route::get('/clientes/detalles/{id}', 'clientes_detalles');



                        Route::post('/clientes/adminstrar/{id}/puntos', 'clientes_administrar_puntos_crear_post');



                        Route::post('/clientes/adminstrar/{id}/puntos/editar', 'clientes_administrar_puntos_editar_post');



                        Route::post('/clientes/adminstrar/{id}/residentes', 'clientes_administrar_residentes_crear_post');



                        Route::post('/clientes/adminstrar/{id}/residentes/editar', 'clientes_administrar_residentes_editar_post');



                        Route::post('/clientes/adminstrar/{id}/tareas_punto/editar', 'clientes_administrar_tareas_punto_editar_post');



                        Route::post('/clientes/adminstrar/{id}/residentes/vehiculo_crear', 'clientes_administrar_residentes_vehiculo_crear_post');



                        Route::get('/clientes/visitas', 'visitas');



                        Route::get('/clientes/visitas/crear', 'visitas_crear');



                        Route::post('/clientes/visitas/crear/subefoto/{id}', 'visitas_crear_sube_foto');



                        Route::post('/clientes/visitas/sube_foto/{id}', 'visitas_crear_sube_foto_archivo');



                        Route::get('/clientes/visitas/finalizar/{id}', 'visitas_finalizar');



                        Route::get('/clientes/visitas/ver/{id}', 'ver');



                        Route::post('/clientes/adminstrar/{id}/residentes/cambio_cliente', 'administrar_residentes_cambio_cliente');



                        Route::get('/clientes/analisis_riesgos', 'analisis_riesgos');



                        Route::get('/clientes/analisis_riesgos/crear', 'analisis_riesgos_crear');



                        Route::get('/clientes/analisis_riesgos/ver/{id}', 'analisis_riesgos_ver');



                        Route::post('/clientes/analisis_riesgos/crear', 'analisis_riesgos_crear_post');



                        Route::get('/clientes/analisis_riesgos/ver/{id}', 'analisis_riesgos_ver');



                        Route::any('/clientes/analisis_riesgos/editar/{id}', 'analisis_riesgos_editar');



                        



                        Route::get('/clientes/incidentes_seguridad', 'incidentes_seguridad');

                        Route::post('/clientes/administrar/asignar_contrasena', 'clientes_administrar_asignar_contrasena');



                        Route::get('/clientes/incidentes_seguridad/nuevo', 'incidentes_seguridad_nuevo');



                        Route::get('/clientes/importar', 'clientes_importar');



                        Route::get('/clientes/residentes/importar', 'clientes_residentes_importar');

                        Route::get('/clientes/residentes_vehiculos/importar', 'clientes_residentes_y_vehiculos_importar');

                        Route::get('/clientes/residentes_vehiculos/importar/{cliente}', 'clientes_residentes_y_vehiculos_importar');



                        Route::get('/clientes/residentes/vehiculos/importar', 'clientes_residentes_vehiculos_importar');







                        Route::get('/clientes/residentes/importar/{cliente}', 'clientes_residentes_importar');



                        Route::get('/clientes/residentes/vehiculos/importar/{cliente}', 'clientes_residentes_vehiculos_importar');



                    });



                    Route::controller(usuarios::class)->group(function(){

                        Route::get('/usuarios/visitas_domiciliarias', 'usuarios_visitas_domiciliarias');

                        Route::post('/usuarios/visitas_domiciliarias', 'usuarios_visitas_domiciliarias_post');

                        Route::get('/usuarios', 'usuarios');



                        Route::get('/usuarios/nuevo', function(){

                            return redirect('/no_autorizado');

                        });

                        Route::get('/usuarios/capacitaciones', 'usuarios_capacitaciones');

                        Route::get('/usuarios/capacitaciones/nueva', 'usuarios_capacitaciones_nueva');
                        Route::get('/usuarios/capacitaciones/editar/{id}', 'usuarios_capacitaciones_nueva');

                        Route::get('/ajax/usuarios/capacitaciones/asistentes/{id}', 'usuarios_capacitaciones_asistentes');

                        Route::get('/usuarios/capacitaciones/ver/{id}', 'capacitaciones_ver');

                        Route::get('/usuarios/editar/{id}', 'usuarios_editar');



                        Route::post('/usuarios/nuevo', 'usuarios_crear_post');



                        Route::post('/usuarios/editar/{id}', 'usuarios_editar_post');



                        Route::get('/usuarios/administrar/{id}', 'usuarios_administrar');



                        Route::get('/usuarios/evaluacion', 'evaluacion');



                        Route::post('/usuarios/evaluacion', 'evaluacion_crear');



                        Route::get('/usuarios/evaluaciones/editar/{id}', 'evaluacion_editar');



                        Route::get('/usuarios/evaluaciones/descargar/{id}', 'evaluacion_descargar');



                    });



                    Route::controller(reportes::class)->group(function(){

                    

                        Route::any('/reportes/novedad_cliente', 'novedad_cliente');



                        Route::any('/reportes/novedad_vigsecol', 'novedad_vigsecol');



                        Route::any('/reportes/novedad_cliente/{id}', 'novedad_cliente_ver_pdf');



                        Route::any('/reportes/novedad_vigsecol/{id}', 'novedad_vigsecol_ver_pdf');



                        Route::get('/reportes/problemas_marcacion', 'problemas_marcacion');



                        Route::any('/reportes/reporte_tecnico', 'reporte_tecnico');



                        Route::any('/reportes/seguimiento_usuarios', 'seguimiento_usuarios');



                        Route::any('/reportes/record_patrullaje', 'record_patrullaje');
                        Route::post('/ajax/reportes/record_patrullaje_resultados', 'record_patrullaje_resultados');



                        Route::any('/reportes/record_patrullaje_global', 'record_patrullaje_global');







                        Route::any('/reportes/cumplimiento', 'cumplimiento');



                        Route::any('/reportes/cumplimiento/informe_pdf', 'cumplimiento_informe_pdf');



                        Route::any('/reportes/correspondencia', 'correspondencia');



                        Route::get('/reportes/correspondencia/pdf', 'correspondencia_pdf');



                        Route::any('/reportes/vehiculos', 'vehiculos');



                        Route::any('/reportes/vehiculos_base_datos', 'vehiculos_base_datos');



                        Route::any('/reportes/control_vehiculos', 'control_vehiculos');



                        Route::any('/reportes/visitantes', 'visitantes');



                        Route::any('/reportes/control_equipo_comunicacion', 'control_equipo_comunicacion');



                        Route::any('/reportes/visitas_analista', 'visitas_analista');











                    });



                    break;



                 case '6':



                    Route::controller(tecnico::class)->group(function(){



                        Route::get('/tecnico/reporte_tecnico', 'reporte_tecnico');



                        Route::get('/tecnico/reporte_tecnico/nuevo', 'reporte_tecnico_nuevo');



                        Route::get('/tecnico/orden_servicio_tecnico', 'orden_servicio_tecnico');

                        Route::get('/tecnico/orden_servicio_tecnico/nueva', 'orden_servicio_tecnico_nueva');

                        Route::get('/tecnico/orden_servicio_tecnico/ver/{id}', 'orden_servicio_tecnico_ver');

                        Route::get('/tecnico/reporte_tecnico', 'reporte_tecnico');



                    });



                    Route::controller(usuarios::class)->group(function(){



                        Route::get('/usuarios', 'usuarios');



                        Route::get('/usuarios/nuevo', function(){

                            return redirect('/no_autorizado');

                        });



                        Route::get('/usuarios/editar/{id}', 'usuarios_editar');



                        Route::post('/usuarios/nuevo', 'usuarios_crear_post');



                        Route::post('/usuarios/editar/{id}', 'usuarios_editar_post');



                        Route::get('/usuarios/administrar/{id}', 'usuarios_administrar');



                        Route::get('/usuarios/rci', 'usuarios_rci');



                        Route::get('/usuarios/rci/ver/{id}', 'usuarios_rci_ver');



                    });



                break;



                 case '7':
                    Route::controller(campanias::class)->group(function(){
                        Route::get('/campanias/nueva', 'campanias_nueva');
                        Route::get('/campanias/ver', 'campanias_ver');

                    }); 
                    Route::controller(encuesta::class)->group(function(){

                        Route::get('/encuestas', 'encuestas');

                        Route::get('/encuestas/crear', 'encuestas_crear');

                        Route::post('/encuestas/cerrar/{id}', 'encuestas_cerrar');

                        Route::get('/encuestas/editar/{id}', 'encuestas_editar')->name('encuestas.editar');

                        Route::get('/encuestas/administrar/{id}', 'encuestas_administrar')->name('encuestas.administrar');

                        Route::get('/encuestas/administrar/versiones/{id}', 'encuestas_administrar_versiones');

                        Route::post('/encuestas/administrar/versiones/{id}', 'encuestas_administrar_versiones_crear');
                        Route::post('/ajax/encuestas/editar_respuesta', 'encuestas_editar_respuesta');
                        Route::post('/encuestas/administrar/versiones/{id}/editar', 'encuestas_administrar_versiones_editar');


                        Route::get('/encuestas/administrar/respuestas/{id}', 'encuestas_administrar_respuestas');

                        Route::any('/encuestas/administrar/estadisticas/{id}', 'encuestas_administrar_estadisticas');



                    }); 

                    Route::controller(programacion::class)->group(function(){

                        Route::get('/solicitudes_permisos', 'solicitudes_permisos');

                        Route::post('/solicitudes_permisos/autorizacion_programacion', 'autorizacion_programacion_permiso');

                        Route::post('/solicitudes_permisos/rechazar_solicitud', 'autorizacion_programacion_permiso_rechazar');

                    });

                     Route::controller(usuarios::class)->group(function(){



                        Route::get('/usuarios/visitas_domiciliarias', 'usuarios_visitas_domiciliarias');

                        Route::post('/usuarios/visitas_domiciliarias', 'usuarios_visitas_domiciliarias_post');

                        Route::get('/usuarios/capacitaciones', 'usuarios_capacitaciones');

                        Route::get('/usuarios/capacitaciones/nueva', 'usuarios_capacitaciones_nueva');
                        Route::get('/usuarios/capacitaciones/editar/{id}', 'usuarios_capacitaciones_nueva');

                        Route::get('/ajax/usuarios/capacitaciones/asistentes/{id}', 'usuarios_capacitaciones_asistentes');

                        Route::get('/usuarios/capacitaciones/ver/{id}', 'capacitaciones_ver');



                    });



                    Route::controller(recurso_humano::class)->group(function(){



                        Route::get('recurso_humano/cuadro_honor', 'cuadro_honor');
                        Route::post('recurso_humano/cuadro_honor', 'cuadro_honor_post');
                        Route::post('recurso_humano/cuadro_honor/categorias', 'cuadro_honor_categorias');
                        Route::get('recurso_humano/bd', 'base_datos');

                        

                        Route::get('recurso_humano/crear_persona', 'crear_persona');



                        Route::post('recurso_humano/crear_persona', 'crear_persona_post');



                        Route::get('recurso_humano/contratacion/{id}', 'contratacion');



                        Route::post('recurso_humano/contratacion/{id}/contratar', 'contratacion_contratar');



                        Route::get('recurso_humano/editar_usuario/{id}', 'editar_usuario');



                        Route::get('recurso_humano/en_contratacion', 'en_contratacion');



                        Route::get('recurso_humano/contratados', 'contratados');



                        Route::get('recurso_humano/contratados/{id}', 'contratados_ver');



                        Route::post('recurso_humano/contratados/{id}/agregar_documento', 'contratados_agregar_documento');



                        Route::get('recurso_humano/descartados', 'descartados');



                        Route::get('recurso_humano/retirados', 'retirados');

                        Route::get('recurso_humano/retirados/encuestas', 'retirados_encuestas');

                        Route::get('recurso_humano/retirados/encuestas/{id}', 'retirados_encuestas_ver');

                        Route::get('recurso_humano/retirados/encuestas/editar/{id}', 'retirados_encuestas_editar');

                        Route::get('recurso_humano/retirados/nuevo', 'retirados_nuevo');



                        Route::post('recurso_humano/retirados/nuevo', 'retirados_nuevo_post');



                        Route::get('recurso_humano/reportes/poblacion', 'reporte_poblacion');



                        Route::post('recurso_humano/reportes/poblacion', 'reporte_poblacion_post_filtros');



                        Route::get('recurso_humano/reportes/vacaciones', 'reporte_vacaciones');



                        Route::get('recurso_humano/reportes/guardas_clientes', 'reporte_guardas_clientes');
                        Route::get('recurso_humano/reportes/documentacion_pendiente', 'reporte_documentacion_pendiente');



                        Route::get('recurso_humano/config/general', 'config_general');



                        Route::get('recurso_humano/config/cargos', 'config_cargos');



                        Route::get('recurso_humano/config/notificaciones', 'config_notificaciones');



                        Route::get('recurso_humano/config/mi_firma', 'config_mi_firma');



                        Route::post('recurso_humano/config/mi_firma', 'config_mi_firma_post');







                        Route::get('recurso_humano/ver_entrevista/{usuario}', 'contratados_ver_entrevista'); 



                        Route::get('recurso_humano/editar_entrevista/{usuario}', 'contratados_editar_entrevista'); 



                        Route::get('recurso_humano/ver_verificacion_referencias/{usuario}', 'contratados_ver_verificacion_referencias'); 



                        Route::get('recurso_humano/editar_verificacion_referencias/{usuario}', 'contratados_editar_verificacion_referencias'); 



                        Route::get('recurso_humano/editar_visita_domiciliaria/{usuario}', 'contratados_editar_visita_domiciliaria'); 



                        Route::get('recurso_humano/ver_visita_domiciliaria/{usuario}', 'contratados_ver_visita_domiciliaria'); 







                        Route::get('/recurso_humano/proceso_disciplinario', 'proceso_disciplinario');

                        Route::get('/recurso_humano/faltas_disciplinarias', 'faltas_disciplinarias');

                        Route::get('/recurso_humano/faltas_disciplinarias/{id}/pdf', 'faltas_disciplinarias_pdf');

                        Route::get('/recurso_humano/faltas_disciplinarias/crear', 'faltas_disciplinarias_crear');

                        Route::post('/recurso_humano/faltas_disciplinarias/crear', 'faltas_disciplinarias_crear_post');

                        Route::post('/ajax/recurso_humano/faltas_disciplinarias/buscar_persona', 'ajax_faltas_disciplinarias_buscar_persona');



                        Route::get('/recurso_humano/proceso_disciplinario/{id}/pdf', 'proceso_disciplinario_pdf');



                        Route::get('/recurso_humano/proceso_disciplinario/{id}', 'proceso_disciplinario_crear');



                        Route::post('/recurso_humano/proceso_disciplinario/{id}', 'proceso_disciplinario_crear_post');



                        Route::get('/recurso_humano/proceso_disciplinario/crear', 'proceso_disciplinario_crear');



                        Route::post('/recurso_humano/proceso_disciplinario/crear', 'proceso_disciplinario_crear_post');







                        Route::get('/recurso_humano/contrato/{id}', 'genera_contrato');











                        Route::get('/cartas', 'cartas');



                        Route::get('/cartas/{carta}/{usuario}', 'carta_usuario');



                    });



                    Route::controller(config::class)->group(function(){



                        Route::get('/config/areas_administrativas', 'areas_administrativas'); 

                        Route::post('/config/area_administrativa/crear', 'areas_administrativas_crear');

                        Route::post('/config/area_administrativa/editar', 'areas_administrativas_editar');

                        Route::post('/config/area_administrativa/agregar_responsable', 'areas_administrativas_agregar_responsable');

                        Route::get('/config/articulos_m_i', 'articulos_m_i');



                        Route::post('/config/articulos_m_i/crear', 'articulos_m_i_crear');







                        Route::get('/config/contratos', 'contratos');



                        Route::post('/config/contratos/crear', 'contratos_crear');



                        Route::post('/config/contratos/editar', 'contratos_editar');







                        Route::post('/config/contratos/editar_contenido/{id}', 'contratos_editar_contenido');











                        Route::get('/config/cartas', 'cartas');



                        Route::post('/config/cartas/crear', 'cartas_crear');



                        Route::post('/config/cartas/editar', 'cartas_editar');



                        Route::post('/config/cartas/editar_contenido/{id}', 'cartas_editar_contenido');











                        Route::get('/config/cargos', 'cargos');



                        Route::get('/config/cargos/editar_documentos', 'cargos_editar_documentos');



                        Route::post('/config/cargos/editar_documentos', 'cargos_editar_documentos_post');



                        Route::post('/config/cargos/crear', 'cargos_crear');



                        Route::post('/config/cargos/editar', 'cargos_editar');



                        



                        Route::get('/config/documentos_gestion_humana', 'documentos_gestion_humana');



                        Route::post('/config/documentos_gestion_humana/crear', 'documentos_gestion_humana_crear');



                        Route::post('/config/documentos_gestion_humana/editar', 'documentos_gestion_humana_editar');







                        Route::get('/config/entrevista_aspecto', 'entrevista_aspecto');



                        Route::post('/config/entrevista_aspecto/crear', 'entrevista_aspecto_crear');



                        Route::post('/config/entrevista_aspecto/editar', 'entrevista_aspecto_editar');







                        Route::get('/config/entrevista_factor', 'entrevista_factor');



                        Route::post('/config/entrevista_factor/crear', 'entrevista_factor_crear');



                        Route::post('/config/entrevista_factor/editar', 'entrevista_factor_editar');







                         Route::get('/config/zonas', 'zonas');



                        Route::post('/config/zonas/crear', 'zonas_crear');



                        Route::post('/config/zonas/editar', 'zonas_editar');



                    });



                break;



                 case '9': /// COMERCIAL /// 

                    Route::controller(supervisor::class)->group(function(){

                        Route::get('/supervisor/supervision', 'supervision');

                        Route::get('/supervisor/supervision/{id}', 'supervision_cliente');

                        Route::get('/supervisor/supervision1/{id}', 'supervision_cliente1');

                    });



                    Route::controller(usuarios::class)->group(function(){

                        Route::get('/usuarios/visitas_domiciliarias', 'usuarios_visitas_domiciliarias');

                        Route::post('/usuarios/visitas_domiciliarias', 'usuarios_visitas_domiciliarias_post');

                        Route::get('/usuarios', 'usuarios');



                        Route::get('/usuarios/capacitaciones', 'usuarios_capacitaciones');

                        Route::get('/usuarios/capacitaciones/nueva', 'usuarios_capacitaciones_nueva');
                        Route::get('/usuarios/capacitaciones/editar/{id}', 'usuarios_capacitaciones_nueva');

                        Route::get('/ajax/usuarios/capacitaciones/asistentes/{id}', 'usuarios_capacitaciones_asistentes');

                        Route::get('/usuarios/capacitaciones/ver/{id}', 'capacitaciones_ver');



                        Route::get('/usuarios/nuevo', function(){

                            return redirect('/no_autorizado');

                        });



                        Route::get('/usuarios/editar/{id}', 'usuarios_editar');



                        Route::post('/usuarios/nuevo', 'usuarios_crear_post');



                        Route::post('/usuarios/editar/{id}', 'usuarios_editar_post');



                        Route::get('/usuarios/administrar/{id}', 'usuarios_administrar');



                        Route::get('/usuarios/evaluacion', 'evaluacion');



                        Route::post('/usuarios/evaluacion', 'evaluacion_crear');



                        Route::get('/usuarios/evaluaciones/editar/{id}', 'evaluacion_editar');



                        Route::get('/usuarios/evaluaciones/descargar/{id}', 'evaluacion_descargar');



                    });

                    Route::controller(config::class)->group(function(){

                        Route::get('/config/tipos_cliente', 'tipos_cliente');

                        Route::post('/config/tipos_cliente/crear', 'tipos_cliente_crear');

                        Route::get('/config/tipos_cliente/editar/{id}', 'tipos_cliente_edit');

                        Route::post('/config/tipos_cliente/editar/{id}/crear_motivo', 'tipos_cliente_edit_crear_motivo');

                    });





                    Route::controller(clientes::class)->group(function(){


                        Route::get('/clientes/residentes_vehiculos/importar', 'clientes_residentes_y_vehiculos_importar');

                        Route::get('/clientes/residentes_vehiculos/importar/{cliente}', 'clientes_residentes_y_vehiculos_importar');
                        

                        Route::post('/clientes/orden_servicio/auto_ocasionales', 'clientes_orden_servicio_ocasionales');





                        Route::post('/clientes/administrar/asignar_contrasena', 'clientes_administrar_asignar_contrasena');



                        Route::get('/clientes/visitas_reuniones', 'visitas_reuniones');



                        Route::get('/clientes/visitas_reuniones/nuevo', 'visitas_reuniones_nuevo');



                        Route::get('/clientes/visitas_reuniones/ver/{id}', 'visitas_reuniones_ver');



                        Route::get('/clientes/orden_servicio/auto_ocasionales', 'orden_servicio_auto_ocasionales');







                        Route::get('/clientes/pqrs', 'clientes_pqrs');



                        Route::get('/clientes/pqrs/nuevo', 'clientes_pqrs_nuevo');



                        Route::post('/clientes/pqrs/nuevo', 'clientes_pqrs_nuevo_post');



                        Route::get('/clientes/pqrs/ver/{id}', 'clientes_pqrs_ver');



                        Route::get('/clientes/pqrs/editar/{id}', 'clientes_pqrs_editar');



                        Route::post('/clientes/pqrs/editar/{id}', 'clientes_pqrs_editar_post');







                        Route::get('/clientes', 'clientes');



                        Route::get('/clientes/nuevo', 'clientes_nuevo');



                        Route::post('/clientes/nuevo', 'clientes_nuevo_post');



                        Route::get('/clientes/editar/{id}', 'clientes_editar');



                        Route::post('/clientes/editar/{id}', 'clientes_editar_post');



                        Route::get('/clientes/administrar/{id}', 'clientes_administrar');
                        Route::post('/ajax/clientes/administrar/info/{id}', 'clientes_administrar_info');
                        Route::post('/ajax/clientes/administrar/contactos/{id}', 'clientes_administrar_contactos');
                        Route::post('/ajax/clientes/administrar/puntos/{id}', 'clientes_administrar_puntos');
                        Route::post('/ajax/clientes/administrar/minuta/{id}', 'clientes_administrar_minuta');

                        Route::post('/ajax/clientes/administrar/copropiedades/{id}', 'clientes_administrar_copropiedades');
                        Route::post('/ajax/clientes/administrar/copropiedades', 'clientes_administrar_copropiedades_data');
                        Route::post('/ajax/clientes/administrar/copropiedades_grupo', 'clientes_administrar_copropiedad_grupo');
                        Route::post('/ajax/clientes/administrar/parqueadero/{id}', 'clientes_administrar_parqueadero');

                         Route::post('/clientes/administrar/regla_parqueadero/{id}', 'clientes_administrar_regla_parqueadero');

                        Route::post('/clientes/administrar/permisos_contacto', 'clientes_administrar_clientes_contacto');



                        Route::get('/clientes/detalles/{id}', 'clientes_detalles');



                        Route::post('/clientes/adminstrar/{id}/puntos', 'clientes_administrar_puntos_crear_post');



                        Route::post('/clientes/adminstrar/{id}/puntos/editar', 'clientes_administrar_puntos_editar_post');



                        Route::post('/clientes/adminstrar/{id}/residentes', 'clientes_administrar_residentes_crear_post');



                        Route::post('/clientes/adminstrar/{id}/residentes/editar', 'clientes_administrar_residentes_editar_post');



                        Route::post('/clientes/adminstrar/{id}/tareas_punto/editar', 'clientes_administrar_tareas_punto_editar_post');



                        Route::post('/clientes/adminstrar/{id}/residentes/vehiculo_crear', 'clientes_administrar_residentes_vehiculo_crear_post');



                        Route::get('/clientes/visitas', 'visitas');



                        Route::get('/clientes/visitas/crear', 'visitas_crear');



                        Route::post('/clientes/visitas/crear/subefoto/{id}', 'visitas_crear_sube_foto');



                        Route::post('/clientes/visitas/sube_foto/{id}', 'visitas_crear_sube_foto_archivo');



                        Route::get('/clientes/visitas/finalizar/{id}', 'visitas_finalizar');



                        Route::get('/clientes/visitas/ver/{id}', 'ver');



                        Route::post('/clientes/adminstrar/{id}/residentes/cambio_cliente', 'administrar_residentes_cambio_cliente');



                        Route::get('/clientes/importar', 'clientes_importar');



                        Route::get('/clientes/residentes/importar', 'clientes_residentes_importar');

                        Route::get('/clientes/residentes_vehiculos/importar', 'clientes_residentes_y_vehiculos_importar');

                        Route::get('/clientes/residentes_vehiculos/importar/{cliente}', 'clientes_residentes_y_vehiculos_importar');



                        Route::get('/clientes/residentes/vehiculos/importar', 'clientes_residentes_vehiculos_importar');



                        Route::get('/clientes/residentes/importar/{cliente}', 'clientes_residentes_importar');



                        Route::get('/clientes/residentes/vehiculos/importar/{cliente}', 'clientes_residentes_vehiculos_importar');



                        Route::get('/clientes/orden_servicio', 'clientes_orden_servicio');

                        Route::get('/clientes/orden_servicio/crear', 'clientes_orden_servicio_crear');

                        Route::get('/clientes/orden_servicio/editar/{id}', 'clientes_orden_servicio_crear');

                        Route::get('/clientes/orden_servicio/ver/{id}', 'clientes_orden_servicio_ver');



                        Route::get('/clientes/pqrs', 'clientes_pqrs');



                        Route::any('/reportes/visitas_analista', 'visitas_analista');



                    });



                break;



                case 'cliente':

                    Route::controller(vigilante::class)->group(function(){

                        Route::post('/correspondencia/subefoto', 'correspondencia_sube_foto');

                        Route::get('/correspondencia_ingreso', 'correspondencia');

                    });



                    Route::controller(supervisor::class)->group(function(){

                        Route::get('/supervisor/supervision/{id}', 'supervision_cliente');

                        Route::get('/supervisor/supervision1/{id}', 'supervision_cliente1');

                    });
  
                    Route::controller(ajax::class)->group(function(){
                        Route::post('/ajax/cliente/validar_cedula_residente', 'validar_cedula_residente');
                    });

                    Route::controller(cliente::class)->group(function(){
                      

                        //*                  RESERVAS
                        
                        //? "Esta ruta muestra todas las reservas de espacios realizadas por los clientes."
                        //?                        |
                        //?                        v 
                        Route::get('/cliente/reserva_espacios', 'reservas_espacios');
                      
                        //? "Esta ruta es para hacer un insert en la base de de datos con la nueva reserva"
                        //?                        |
                        //?                        v
                        Route::post('/cliente/reserva_espacios/crear_reserva', 'crear_reserva');
                  
                        //? "Esta ruta cancelamos la reserva haciendo update "
                        //?                        |
                        //?                        v 
                        Route::post('/cliente/reserva_espacios/cancelar_reserva', 'cancelar_reserva');
                        
                        //? "Esta ruta autorizamos la reserva  "
                        //?                        |
                        //?                        v
                        Route::post('/cliente/reserva_espacios/autorizar_reserva', 'autorizar_reserva');

                        //? "Esta ruta No damos autorizacion de la reserva  "
                        //?                        |
                        //?                        v 
                        Route::post('/cliente/reserva_espacios/no_autorizar_reserva', 'no_autorizar_reserva');

                        //*                 ESPACIOS

                        //? "Esta ruta permite crear una nueva zona comun "
                        //?                        |
                        //?                        v 
                        Route::post('cliente/espacios/crear', 'crear_espacio');//CREATED

                        //? "Esta ruta permite editar un espacio sus campos"
                        //?                        |
                        //?                        v 
                        Route::post('cliente/espacios/actualizar_espacios', 'actualizar_espacios');
                     
                        //? "Esta ruta permite editar un espacio sus campos"
                        //?                        |
                        //?                        v
                        Route::post('cliente/espacios/eliminar_espacio', 'eliminar_espacio');


                        //*                  COMUNICADOS

                        //? "Esta ruta muestra todos los comunicados"
                        //?                        |
                        //?                        v
                        Route::get('/cliente/comunicados', 'comunicados'); //READ
                        
                        //? "Esta ruta creamos un nuevo comunicado"
                        //?                        |
                        //?                        v
                        Route::post('/cliente/comunicados/subir_imagen', 'comunicados_subir_imagen'); //CREATED
                        
                        //? "Esta ruta editamos el comunicado"
                        //?                        |
                        //?                        v
                        Route::post('/cliente/comunicados/editar_imagen', 'editar_imagen'); //UPDATE
                      
                        //? "Esta ruta eliminamos el comunicado"
                        //?                        |
                        //?                        v
                        Route::post('/cliente/comunicados/eliminar_imagen', 'comunicados_eliminarImagen');//DELETE

       



                        Route::post('/cliente/eventos/crear_tipo', 'eventos_crear_tipo');
                        Route::post('/cliente/eventos/crear', 'eventos_crear');
                        Route::get('/cliente/eventos', 'eventos');                        
                       
                   
                                         
               
                        Route::get('/dependencias/importar', 'importar_dependencias');

                        Route::get('/minuta', 'minuta');



                        Route::get('residentes', 'residentes');



                        Route::post('residentes', 'residentes_crear_post');



                        Route::post('residentes/editar', 'residentes_editar_post');



                        Route::post('residentes/asignar_constrasela', 'residentes_asignar_constrasela_post');



                        Route::post('residentes/vehiculo_crear', 'residentes_vehiculo_crear_post');



                        Route::get('/correspondencia', 'correspondencia');



                        Route::get('/visitantes', 'visitantes');



                        Route::get('/vehiculos', 'vehiculos');



                        Route::get('/control_vehiculos', 'control_vehiculos');



                        Route::get('/configuracion', 'configuracion');

                        Route::post('/cliente/configuracion/administrar_areas', 'configuracion_administrar_areas');



                        Route::post('/configuracion/crear_tipo_residente', 'configuracion_crear_tipo_residente');



                        Route::post('/configuracion/crear_area', 'configuracion_crear_area');



                        Route::post('/configuracion/crear_rol', 'configuracion_crear_rol');



                        Route::post('/configuracion/crear_usuario', 'configuracion_crear_usuario');



                        Route::post('/configuracion/asignar_pass_usuario', 'configuracion_asignar_pass_usuario');







                        Route::get('/horarios', 'horarios');



                        Route::get('/autorizacion_salida', 'autorizacion_salida');



                        Route::post('/autorizacion_salida', 'autorizacion_salida_post');



                        Route::get('/actividades_extra', 'actividades_extra');






                        Route::post('/actividades_extra', 'actividades_extra_post');



                        Route::get('/registros', 'registros');



                    });
                    Route::controller(colegioAleman::class)->group(function(){
                        Route::get('/actividades_extra/importar', 'actividades_extra_importar');
                    });



                    Route::controller(clientes::class)->group(function(){



                        Route::get('/clientes/pqrs', 'clientes_pqrs');



                        Route::get('/clientes/pqrs/nuevo', 'clientes_pqrs_nuevo');



                        Route::get('/clientes/pqrs/ver/{id}', 'clientes_pqrs_ver');



                        Route::post('/clientes/pqrs/nuevo', 'clientes_pqrs_nuevo_post');



                        Route::get('/clientes/analisis_riesgos', 'analisis_riesgos');



                        Route::get('/clientes/analisis_riesgos/ver/{id}', 'analisis_riesgos_ver');



                    });



                    Route::controller(reportes::class)->group(function(){



                        Route::any('/reportes/novedad_cliente', 'novedad_cliente');



                        Route::any('/reportes/novedad_cliente/{id}', 'novedad_cliente_ver_pdf');



                    });



                    break;



                case '14':



                    Route::controller(vigilante::class)->group(function(){



                        Route::get('/ingresos_salidas', 'ingresos_salidas');



                        Route::get('/ingresos_salidas_ajax', 'ingresos_salidas_ajax');



                    });



                    Route::controller(cliente::class)->group(function(){



                        Route::get('/horarios', 'horarios');



                        Route::get('/autorizacion_salida', 'autorizacion_salida');



                        Route::post('/autorizacion_salida', 'autorizacion_salida_post');



                        Route::get('/actividades_extra', 'actividades_extra');



                        Route::post('/actividades_extra', 'actividades_extra_post');



                        Route::get('/registros', 'registros');



                    });
                    Route::controller(colegioAleman::class)->group(function(){
                        Route::get('/actividades_extra/importar', 'actividades_extra_importar');
                    });




                    break;

                    // PROGRAMACION

                case '15':

                    Route::controller(clientes::class)->group(function(){

                        Route::get('/clientes/servicios_ocasionales', 'servicios_ocasionales');

                    });

                    Route::controller(usuarios::class)->group(function(){

                        Route::get('/usuarios/capacitaciones', 'usuarios_capacitaciones');

                        Route::get('/usuarios/capacitaciones/nueva', 'usuarios_capacitaciones_nueva');
                        Route::get('/usuarios/capacitaciones/editar/{id}', 'usuarios_capacitaciones_nueva');

                        Route::get('/ajax/usuarios/capacitaciones/asistentes/{id}', 'usuarios_capacitaciones_asistentes');

                        Route::get('/usuarios/capacitaciones/ver/{id}', 'capacitaciones_ver');



                    });

                    Route::controller(recurso_humano::class)->group(function(){

                        Route::any('/cartas/carta_recomendacion', 'carta_recomendacion_cliente');

                        

                        Route::get('recurso_humano/config/mi_firma', 'config_mi_firma');

                        Route::post('recurso_humano/config/mi_firma', 'config_mi_firma_post');

                        Route::get('recurso_humano/editar_visita_domiciliaria/{usuario}', 'contratados_editar_visita_domiciliaria'); 

                        Route::get('/recurso_humano/proceso_disciplinario', 'proceso_disciplinario');

                        Route::get('/recurso_humano/faltas_disciplinarias', 'faltas_disciplinarias');

                        Route::get('/recurso_humano/faltas_disciplinarias/{id}/pdf', 'faltas_disciplinarias_pdf');

                        Route::get('/recurso_humano/faltas_disciplinarias/crear', 'faltas_disciplinarias_crear');

                        Route::post('/recurso_humano/faltas_disciplinarias/crear', 'faltas_disciplinarias_crear_post');

                        Route::post('/ajax/recurso_humano/faltas_disciplinarias/buscar_persona', 'ajax_faltas_disciplinarias_buscar_persona');

                        Route::get('/recurso_humano/proceso_disciplinario/{id}/pdf', 'proceso_disciplinario_pdf');

                        Route::get('/recurso_humano/proceso_disciplinario/{id}', 'proceso_disciplinario_crear');

                        Route::post('/recurso_humano/proceso_disciplinario/{id}', 'proceso_disciplinario_crear_post');

                        Route::get('/recurso_humano/proceso_disciplinario/crear', 'proceso_disciplinario_crear');

                        Route::post('/recurso_humano/proceso_disciplinario/crear', 'proceso_disciplinario_crear_post');

                    });

                    Route::controller(programacion::class)->group(function(){

                        Route::get('/solicitudes_permisos', 'solicitudes_permisos');

                        Route::post('/solicitudes_permisos/autorizacion_programacion', 'autorizacion_programacion_permiso');

                        Route::post('/solicitudes_permisos/rechazar_solicitud', 'autorizacion_programacion_permiso_rechazar');

                        Route::get('/programacion', 'inicio');

                        Route::get('/programacion/bitacora', 'bitacora');

                        Route::get('/programacion_turnos', 'programacion_turnos');
                        Route::post('/ajax/programacion_turnos/puesto', 'programacion_turnos_puesto');
                        Route::post('/ajax/programacion_turnos/servicio_ocasional', 'programacion_turnos_servicio_ocasional');
                        Route::post('/ajax/programacion_turnos/asignar_turno', 'programacion_turnos_asignar_turno');
                        Route::post('/ajax/programacion_turnos/programacion_empleado', 'programacion_turnos_empleado');
                        Route::post('/ajax/programacion_turnos/opciones/listado_servicios_ocasionales', 'programacion_turnos_opcion_listado_servicios_ocasionales');
                        Route::post('/ajax/programacion_turnos/opciones/ausencias', 'programacion_turnos_opcion_ausencias');
                        Route::post('/ajax/programacion_turnos/opciones/novedades_programacion', 'programacion_turnos_opcion_novedades_programacion');
                        Route::post('/ajax/programacion_turnos/opciones/turnos_vig', 'programacion_turnos_opcion_turnos_vig');
                        Route::post('/ajax/programacion_turnos/opciones/llenado_automatico', 'programacion_turnos_opcion_llenado_automatico');

                        
                        Route::post('/programacion/descargar', 'descargar');

                        Route::get('/programacion/puesto', 'puesto');

                        Route::get('/programacion/puesto/{id}', 'puesto');

                        Route::post('/ajax/programacion/seleccionar', 'seleccionar');

                        Route::post('/ajax/programacion/eliminar', 'eliminar');

                        Route::post('/ajax/programacion/autocompletar', 'autocompletar');

                        Route::get('/ajax/componentes/contenedor_programacion', 'componente_contenedor_programacion');

                        Route::post('/ajax/programacion/agregar_persona', 'programacion_agregar_persona');

                        Route::post('/programacion/novedad/anular', 'novedad_solicitar_anulacion');

                        Route::post('/ajax/programacion/asignar_otro_puesto', 'programacion_asignar_otro_puesto');

                        Route::get('/programacion/novedades', 'novedades');
                        Route::get('/programacion_turnos', 'programacion_turnos');
                        Route::post('/ajax/programacion_turnos/puesto', 'programacion_turnos_puesto');
                        Route::post('/ajax/programacion_turnos/servicio_ocasional', 'programacion_turnos_servicio_ocasional');
                        Route::post('/ajax/programacion_turnos/asignar_turno', 'programacion_turnos_asignar_turno');
                        Route::post('/ajax/programacion_turnos/programacion_empleado', 'programacion_turnos_empleado');

                        Route::get('/programacion/novedades/crear', 'novedades_crear');

                        Route::post('/programacion/novedades/crear', 'novedades_crear_post');

                        Route::get('/ajax/programacion/novedades/cubrir_puesto/{id}', 'ajax_novedades_cubrir_puesto');

                        Route::post('/ajax/programacion/novedades/cubrir_puesto_consulta_actual_turno', 'ajax_cubrir_puesto_consulta_actual_turno');

                    });

                    Route::controller(config::class)->group(function(){

                     

                        Route::get('/config/carta_recomendacion', 'carta_recomendacion');

                        Route::get('/config/festivos', 'festivos');

                        Route::post('/config/festivos/crear', 'festivos_crear');

                        Route::get('/config/clientes_centros_costos', 'clientes_centros_costos');

                    });

                    

                    break;



                    case '16':

                        Route::controller(recurso_humano::class)->group(function(){

                            Route::get('recurso_humano/config/mi_firma', 'config_mi_firma');

                            Route::post('recurso_humano/config/mi_firma', 'config_mi_firma_post');

                            Route::get('recurso_humano/editar_visita_domiciliaria/{usuario}', 'contratados_editar_visita_domiciliaria'); 

                            Route::get('/recurso_humano/proceso_disciplinario', 'proceso_disciplinario');

                            Route::get('/recurso_humano/faltas_disciplinarias', 'faltas_disciplinarias');

                            Route::get('/recurso_humano/faltas_disciplinarias/{id}/pdf', 'faltas_disciplinarias_pdf');

                            Route::get('/recurso_humano/faltas_disciplinarias/crear', 'faltas_disciplinarias_crear');

                            Route::post('/recurso_humano/faltas_disciplinarias/crear', 'faltas_disciplinarias_crear_post');

                            Route::post('/ajax/recurso_humano/faltas_disciplinarias/buscar_persona', 'ajax_faltas_disciplinarias_buscar_persona');

                            Route::get('/recurso_humano/proceso_disciplinario/{id}/pdf', 'proceso_disciplinario_pdf');

                            Route::get('/recurso_humano/proceso_disciplinario/{id}', 'proceso_disciplinario_crear');

                            Route::post('/recurso_humano/proceso_disciplinario/{id}', 'proceso_disciplinario_crear_post');

                            Route::get('/recurso_humano/proceso_disciplinario/crear', 'proceso_disciplinario_crear');

                            Route::post('/recurso_humano/proceso_disciplinario/crear', 'proceso_disciplinario_crear_post');

                        });

                        Route::controller(programacion::class)->group(function(){

                            Route::get('/programacion/novedades/aprobar_anulacion/{id}', 'novedades_aprobar_anulacion');

                            Route::get('/programacion/novedades/rechazar_anulacion/{id}', 'novedades_rechazar_anulacion');

                            Route::get('/solicitudes_permisos', 'solicitudes_permisos');

                            Route::get('/programacion', 'inicio');

                            Route::get('/programacion/bitacora', 'bitacora');
                            Route::get('/programacion_turnos', 'programacion_turnos');
                            Route::post('/ajax/programacion_turnos/puesto', 'programacion_turnos_puesto');
                                Route::post('/ajax/programacion_turnos/puesto', 'programacion_turnos_puesto');
                        Route::post('/ajax/programacion_turnos/asignar_turno', 'programacion_turnos_asignar_turno');
                            Route::post('/ajax/programacion_turnos/programacion_empleado', 'programacion_turnos_empleado');


                            Route::post('/programacion/descargar', 'descargar');

                            Route::get('/ajax/componentes/contenedor_programacion', 'componente_contenedor_programacion');

                            Route::post('/ajax/programacion/agregar_persona', 'programacion_agregar_persona');

                            Route::post('/ajax/programacion/asignar_otro_puesto', 'programacion_asignar_otro_puesto');

                            Route::get('/programacion/novedades', 'novedades');

                        });

                        Route::controller(nomina::class)->group(function(){

                            Route::post('/ajax/nomina/liquidar/validar_centros', 'validar_centros_costos');

                            Route::get('/nomina/liquidar/{nomina}/importa_centros_costos', 'importar_centros_costos');

                            Route::get('/ajax/nomina/novedades/persona/{id}', 'novedades_persona');

                            Route::any('/nomina/novedades', 'novedades');

                            Route::any('/nomina/configurar_horarios_mas_12', 'configurar_horarios_mas_12');

                            Route::any('/nomina/novedades/{persona}', 'novedades');

                            

                            Route::post('/nomina/novedades/aprobar_solicitud_vig/{id}', 'aprobar_solicitud_vig');

                            Route::post('/nomina/novedades/rechazar_solicitud_vig/{id}', 'rechazar_solicitud_vig');





                            Route::get('/nomina/liquidacion/novedades/{id}', 'liquidacion_novedades');

                            Route::get('/nomina/liquidaciones/{id}', 'liquidar');

                            Route::get('/nomina/liquidacion/turnos_vig/{id}', 'liquidacion_turnos_vig');

                            Route::get('/nomina/liquidacion/personas/{id}', 'liquidacion_personas');

                            Route::get('/nomina/liquidacion/centros_costos/{id}', 'liquidacion_centros_costos');

                            Route::get('/nomina/liquidacion/opciones/{id}', 'liquidacion_opciones');

                            Route::get('/nomina/liquidar', 'liquidar');

                            Route::get('/nomina/liquidar/menu/{nomina}', 'liquidar_menu');

                            Route::post('/nomina/liquidar', 'liquidar_crear');

                            Route::post('/nomina/liquidar/finalizar', 'liquidacion_finalizar');

                            Route::post('/nomina/liquidar/descargar/{id}', 'liquidacion_descargar');

                            Route::post('/nomina/liquidar/descargas/', 'liquidacion_descargas');

                            Route::get('/nomina/liquidar/finalizar_completa/{liquidacion_nomina}', 'liquidacion_nomina_finalizar');

                            Route::post('/ajax/nomina/liquidar/cambiar_turno', 'liquidacion_cambiar_turno');

                            Route::post('/ajax/nomina/liquidar/recalcular', 'liquidacion_recalcular');

                            Route::get('/nomina/liquidar/persona/informacion', 'liquidacion_persona_informacion');







                            Route::get('/nomina/liquidaciones', 'liquidaciones');
                            Route::get('/nomina/informe_detallado', 'informe_detallado');
                            Route::post('/ajax/nomina/informe_detallado', 'buscar_informe_detallado');

                            Route::get('/nomina/personas', 'personas');

                            Route::get('/nomina/centros_costos', 'centros_costos');



                        });

                        Route::controller(config::class)->group(function(){

                            Route::get('/config/nomina', 'nomina');

                            Route::get('/config/festivos', 'festivos');

                            Route::post('/config/festivos/crear', 'festivos_crear');

                            Route::get('/config/clientes_centros_costos', 'clientes_centros_costos');

                            Route::get('/config/turnos_extra', 'turnos_extra');

                            Route::get('/config/novedades_programacion', 'novedades_programacion');

                            Route::post('/config/novedades_programacion', 'novedades_programacion_post');

                        });

                        

                        break;



                ////////////////////////////////////////// OPERADOR DE MEDIOS TECNOLOGICOS





                case '18':

                    Route::controller(usuarios::class)->group(function(){

                        Route::get('/usuarios/capacitaciones', 'usuarios_capacitaciones');

                        Route::get('/usuarios/capacitaciones/nueva', 'usuarios_capacitaciones_nueva');
                        Route::get('/usuarios/capacitaciones/editar/{id}', 'usuarios_capacitaciones_nueva');

                        Route::get('/ajax/usuarios/capacitaciones/asistentes/{id}', 'usuarios_capacitaciones_asistentes');

                        Route::get('/usuarios/capacitaciones/ver/{id}', 'capacitaciones_ver');

                    });



                    Route::controller(reportes::class)->group(function(){



                        Route::get('/reportes/login', 'login');

                        Route::get('/reportes/problemas_marcacion', 'problemas_marcacion');



                        Route::any('/reportes/reporte_tecnico', 'reporte_tecnico');



                        Route::any('/reportes/reporte_tecnico/costos', 'reporte_tecnico_costos');



                        Route::any('/reportes/seguimiento_usuarios', 'seguimiento_usuarios');



                        Route::any('/reportes/record_patrullaje', 'record_patrullaje');
                        Route::post('/ajax/reportes/record_patrullaje_resultados', 'record_patrullaje_resultados');



                        Route::any('/reportes/record_patrullaje_global', 'record_patrullaje_global');



                        Route::any('/reportes/cumplimiento', 'cumplimiento');



                        Route::any('/reportes/cumplimiento/informe_pdf', 'cumplimiento_informe_pdf');



                        Route::any('/reportes/correspondencia', 'correspondencia');



                        Route::get('/reportes/correspondencia/pdf', 'correspondencia_pdf');



                        Route::any('/reportes/vehiculos', 'vehiculos');



                        Route::any('/reportes/vehiculos_base_datos', 'vehiculos_base_datos');



                        Route::any('/reportes/control_vehiculos', 'control_vehiculos');



                        Route::get('/reportes/minuta', 'minuta');



                        Route::post('/reportes/minuta', 'minuta_post');



                        Route::any('/reportes/visitantes', 'visitantes');



                        Route::any('/reportes/visitantes/pdf', 'visitantes_pdf');



                        Route::any('/reportes/control_equipo_comunicacion', 'control_equipo_comunicacion');



                        Route::any('/reportes/visitas_analista', 'visitas_analista');



                        Route::any('/reportes/control_ingreso_salida', 'control_ingreso_salida');



                        Route::any('/reportes/control_ingreso_salida/descargar/{id}', 'control_ingreso_salida_descargar');



                        Route::any('/reportes/kilometraje_supervisores', 'kilometraje_supervisores');



                        Route::any('/reportes/novedad_cliente', 'novedad_cliente');



                        Route::any('/reportes/novedad_vigsecol', 'novedad_vigsecol');



                        Route::any('/reportes/novedad_cliente/{id}', 'novedad_cliente_ver_pdf');



                        Route::any('/reportes/novedad_vigsecol/{id}', 'novedad_vigsecol_ver_pdf');



                        Route::any('/reportes/evaluaciones', 'evaluaciones');



                    });



                    Route::controller(clientes::class)->group(function(){



                        Route::get('/clientes', 'clientes');



                        Route::get('/clientes/pqrs/editar/{id}', 'clientes_pqrs_editar');



                        Route::post('/clientes/pqrs/editar/{id}', 'clientes_pqrs_editar_post');



                        Route::get('/clientes/visitas_reuniones', 'visitas_reuniones');



                        Route::get('/clientes/visitas_reuniones/nuevo', 'visitas_reuniones_nuevo');



                        Route::get('/clientes/visitas_reuniones/ver/{id}', 'visitas_reuniones_ver');







                        Route::get('/clientes/pqrs', 'clientes_pqrs');



                        Route::get('/clientes/pqrs/nuevo', 'clientes_pqrs_nuevo');



                        Route::post('/clientes/pqrs/nuevo', 'clientes_pqrs_nuevo_post');



                        Route::get('/clientes/pqrs/ver/{id}', 'clientes_pqrs_ver');



                        Route::get('/clientes/pqrs/editar/{id}', 'clientes_pqrs_editar');



                        Route::post('/clientes/pqrs/editar/{id}', 'clientes_pqrs_editar_post');







                        Route::get('/clientes/nuevo', 'clientes_nuevo');



                        Route::post('/clientes/nuevo', 'clientes_nuevo_post');



                        Route::get('/clientes/editar/{id}', 'clientes_editar');



                        Route::post('/clientes/editar/{id}', 'clientes_editar_post');



                        Route::get('/clientes/administrar/{id}', 'clientes_administrar');
                        Route::post('/ajax/clientes/administrar/info/{id}', 'clientes_administrar_info');
                        Route::post('/ajax/clientes/administrar/contactos/{id}', 'clientes_administrar_contactos');
                        Route::post('/ajax/clientes/administrar/puntos/{id}', 'clientes_administrar_puntos');
                        Route::post('/ajax/clientes/administrar/minuta/{id}', 'clientes_administrar_minuta');

                        Route::post('/ajax/clientes/administrar/copropiedades/{id}', 'clientes_administrar_copropiedades');
                        Route::post('/ajax/clientes/administrar/copropiedades', 'clientes_administrar_copropiedades_data');
                        Route::post('/ajax/clientes/administrar/copropiedades_grupo', 'clientes_administrar_copropiedad_grupo');
                        Route::post('/ajax/clientes/administrar/parqueadero/{id}', 'clientes_administrar_parqueadero');

                         Route::post('/clientes/administrar/regla_parqueadero/{id}', 'clientes_administrar_regla_parqueadero');

                        Route::post('/clientes/administrar/permisos_contacto', 'clientes_administrar_clientes_contacto');



                        Route::post('/clientes/administrar/asignar_contrasena', 'clientes_administrar_asignar_contrasena');



                        Route::get('/clientes/detalles/{id}', 'clientes_detalles');



                        Route::post('/clientes/adminstrar/{id}/puntos', 'clientes_administrar_puntos_crear_post');



                        Route::post('/clientes/adminstrar/{id}/puntos/editar', 'clientes_administrar_puntos_editar_post');



                        Route::post('/clientes/adminstrar/{id}/residentes', 'clientes_administrar_residentes_crear_post');



                        Route::post('/clientes/adminstrar/{id}/residentes/editar', 'clientes_administrar_residentes_editar_post');



                        Route::post('/clientes/adminstrar/{id}/tareas_punto/editar', 'clientes_administrar_tareas_punto_editar_post');



                        Route::post('/clientes/adminstrar/{id}/residentes/vehiculo_crear', 'clientes_administrar_residentes_vehiculo_crear_post');



                        Route::get('/clientes/visitas', 'visitas');



                        Route::get('/clientes/visitas/crear', 'visitas_crear');



                        Route::post('/clientes/visitas/crear/subefoto/{id}', 'visitas_crear_sube_foto');



                        Route::post('/clientes/visitas/sube_foto/{id}', 'visitas_crear_sube_foto_archivo');



                        Route::get('/clientes/visitas/finalizar/{id}', 'visitas_finalizar');



                        Route::get('/clientes/visitas/ver/{id}', 'ver');



                        Route::post('/clientes/adminstrar/{id}/residentes/cambio_cliente', 'administrar_residentes_cambio_cliente');



                        Route::get('/clientes/importar', 'clientes_importar');



                        Route::get('/clientes/residentes/importar', 'clientes_residentes_importar');

                        Route::get('/clientes/residentes_vehiculos/importar', 'clientes_residentes_y_vehiculos_importar');

                        Route::get('/clientes/residentes_vehiculos/importar/{cliente}', 'clientes_residentes_y_vehiculos_importar');



                        Route::get('/clientes/residentes/vehiculos/importar', 'clientes_residentes_vehiculos_importar');







                        Route::get('/clientes/residentes/importar/{cliente}', 'clientes_residentes_importar');



                        Route::get('/clientes/residentes/vehiculos/importar/{cliente}', 'clientes_residentes_vehiculos_importar');







                        Route::get('/clientes/analisis_riesgos', 'analisis_riesgos');



                        Route::get('/clientes/analisis_riesgos/crear', 'analisis_riesgos_crear');



                        Route::get('/clientes/analisis_riesgos/ver/{id}', 'analisis_riesgos_ver');



                        Route::any('/clientes/analisis_riesgos/editar/{id}', 'analisis_riesgos_editar');



                        Route::post('/clientes/analisis_riesgos/crear', 'analisis_riesgos_crear_post');







                        Route::get('/clientes/consignas_particulares', 'consignas_particulares');



                        Route::get('/clientes/consignas_particulares/crear', 'consignas_particulares_crear');



                        Route::post('/clientes/consignas_particulares/crear', 'consignas_particulares_crear_post');



                        Route::get('/clientes/consignas_particulares/ver/{id}', 'consignas_particulares_ver_pdf');







                        Route::get('/clientes/incidentes_seguridad', 'incidentes_seguridad');



                        Route::get('/clientes/incidentes_seguridad/nuevo', 'incidentes_seguridad_nuevo');







                    });

                break;

                 case '10':

                    Route::controller(programacion::class)->group(function(){

                        Route::get('/programacion', 'inicio');

                        Route::post('/programacion/novedad_turno', 'novedad_turno');

                        Route::post('/programacion/descargar', 'descargar');

                        Route::get('/programacion/puesto', 'puesto');

                        Route::get('/programacion/puesto/{id}', 'puesto');

                        Route::get('/ajax/componentes/contenedor_programacion', 'componente_contenedor_programacion');

                    });

                    Route::controller(recurso_humano::class)->group(function(){

                        Route::get('recurso_humano/config/mi_firma', 'config_mi_firma');

                        Route::post('recurso_humano/config/mi_firma', 'config_mi_firma_post');

                        Route::get('recurso_humano/editar_visita_domiciliaria/{usuario}', 'contratados_editar_visita_domiciliaria'); 

                        Route::get('/recurso_humano/proceso_disciplinario', 'proceso_disciplinario');

                        Route::get('/recurso_humano/faltas_disciplinarias', 'faltas_disciplinarias');

                        Route::get('/recurso_humano/faltas_disciplinarias/{id}/pdf', 'faltas_disciplinarias_pdf');

                        Route::get('/recurso_humano/faltas_disciplinarias/crear', 'faltas_disciplinarias_crear');

                        Route::post('/recurso_humano/faltas_disciplinarias/crear', 'faltas_disciplinarias_crear_post');

                        Route::post('/ajax/recurso_humano/faltas_disciplinarias/buscar_persona', 'ajax_faltas_disciplinarias_buscar_persona');



                        Route::get('/recurso_humano/proceso_disciplinario/{id}/pdf', 'proceso_disciplinario_pdf');



                        Route::get('/recurso_humano/proceso_disciplinario/{id}', 'proceso_disciplinario_crear');



                        Route::post('/recurso_humano/proceso_disciplinario/{id}', 'proceso_disciplinario_crear_post');



                        Route::get('/recurso_humano/proceso_disciplinario/crear', 'proceso_disciplinario_crear');



                        Route::post('/recurso_humano/proceso_disciplinario/crear', 'proceso_disciplinario_crear_post');

                    });

                    Route::controller(recurso_humano::class)->group(function(){

                        Route::get('recurso_humano/config/mi_firma', 'config_mi_firma');

                        Route::post('recurso_humano/config/mi_firma', 'config_mi_firma_post');

                        Route::get('recurso_humano/editar_visita_domiciliaria/{usuario}', 'contratados_editar_visita_domiciliaria'); 

                        Route::get('/recurso_humano/proceso_disciplinario', 'proceso_disciplinario');

                        Route::get('/recurso_humano/faltas_disciplinarias', 'faltas_disciplinarias');

                        Route::get('/recurso_humano/faltas_disciplinarias/{id}/pdf', 'faltas_disciplinarias_pdf');

                        Route::get('/recurso_humano/faltas_disciplinarias/crear', 'faltas_disciplinarias_crear');

                        Route::post('/recurso_humano/faltas_disciplinarias/crear', 'faltas_disciplinarias_crear_post');

                        Route::post('/ajax/recurso_humano/faltas_disciplinarias/buscar_persona', 'ajax_faltas_disciplinarias_buscar_persona');

                        Route::get('/recurso_humano/proceso_disciplinario/{id}/pdf', 'proceso_disciplinario_pdf');

                        Route::get('/recurso_humano/proceso_disciplinario/{id}', 'proceso_disciplinario_crear');

                        Route::post('/recurso_humano/proceso_disciplinario/{id}', 'proceso_disciplinario_crear_post');

                        Route::get('/recurso_humano/proceso_disciplinario/crear', 'proceso_disciplinario_crear');

                        Route::post('/recurso_humano/proceso_disciplinario/crear', 'proceso_disciplinario_crear_post');

                    });

                    Route::controller(tecnico::class)->group(function(){



                        Route::get('/tecnico/reporte_tecnico_se', 'reporte_tecnico_se');



                        Route::get('/tecnico/reporte_tecnico_se/crear', 'reporte_tecnico_se_crear');



                    });



                    Route::controller(clientes::class)->group(function(){



                        Route::get('/clientes/pqrs', 'clientes_pqrs');



                        Route::get('/clientes/pqrs/nuevo', 'clientes_pqrs_nuevo');



                        Route::post('/clientes/pqrs/nuevo', 'clientes_pqrs_nuevo_post');



                        Route::get('/clientes/pqrs/ver/{id}', 'clientes_pqrs_ver');







                        Route::get('/clientes', 'clientes');



                        Route::get('/clientes/nuevo', 'clientes_nuevo');



                        Route::post('/clientes/nuevo', 'clientes_nuevo_post');



                        Route::get('/clientes/editar/{id}', 'clientes_editar');



                        Route::post('/clientes/editar/{id}', 'clientes_editar_post');



                        Route::get('/clientes/administrar/{id}', 'clientes_administrar');
                        Route::post('/ajax/clientes/administrar/info/{id}', 'clientes_administrar_info');
                        Route::post('/ajax/clientes/administrar/contactos/{id}', 'clientes_administrar_contactos');
                        Route::post('/ajax/clientes/administrar/puntos/{id}', 'clientes_administrar_puntos');
                        Route::post('/ajax/clientes/administrar/minuta/{id}', 'clientes_administrar_minuta');

                        Route::post('/ajax/clientes/administrar/copropiedades/{id}', 'clientes_administrar_copropiedades');
                        Route::post('/ajax/clientes/administrar/copropiedades', 'clientes_administrar_copropiedades_data');
                        Route::post('/ajax/clientes/administrar/copropiedades_grupo', 'clientes_administrar_copropiedad_grupo');
                        Route::post('/ajax/clientes/administrar/parqueadero/{id}', 'clientes_administrar_parqueadero');

                         Route::post('/clientes/administrar/regla_parqueadero/{id}', 'clientes_administrar_regla_parqueadero');

                        Route::post('/clientes/administrar/permisos_contacto', 'clientes_administrar_clientes_contacto');



                        Route::post('/clientes/administrar/asignar_contrasena', 'clientes_administrar_asignar_contrasena');



                        Route::get('/clientes/detalles/{id}', 'clientes_detalles');







                        Route::get('/clientes/incidentes_seguridad', 'incidentes_seguridad');



                        Route::get('/clientes/incidentes_seguridad/nuevo', 'incidentes_seguridad_nuevo');



                        Route::get('/novedad_cliente/{cliente}', 'novedad_cliente');



                        Route::post('/novedad_cliente/{cliente}', 'novedad_cliente_post');



                        Route::get('/novedad_vigsecol/{cliente}', 'novedad_vigsecol');



                        Route::post('/novedad_vigsecol/{cliente}', 'novedad_vigsecol_post');



                    });



                    Route::controller(vigilante::class)->group(function(){



                        Route::get('/ingresos_salidas', 'ingresos_salidas');



                        Route::get('/ingresos_salidas_ajax', 'ingresos_salidas_ajax');



                        Route::get('/vehiculos', 'vehiculos');



                        Route::get('/vehiculos/historial', 'vehiculos_historial');



                        Route::post('/vehiculos', 'vehiculos_post');



                        Route::get('/minuta', 'minuta');



                        Route::post('/minuta', 'minuta_post');



                        Route::get('/marcacion', 'marcacion');



                        Route::get('/marcacion/apertura_ronda', 'marcacion_apertura_ronda');



                        Route::post('/marcacion/apertura_ronda', 'marcacion_apertura_ronda_post');



                        Route::post('/marcacion/finalizar_ronda', 'marcacion_finalizar_ronda_post');



                        Route::get('/marcacion/confirmar/{id}', 'marcacion_confirmar');



                        Route::post('/marcacion/confirmar/subefoto', 'marcacion_confirmar_sube_foto');



                        Route::post('/marcacion/confirmar/{id}', 'marcacion_confirmar_post');



                        Route::get('/visitantes', 'visitantes');



                        Route::post('/visitantes/subefoto', 'visitantes_sube_foto');



                        Route::post('/correspondencia/subefoto', 'correspondencia_sube_foto');



                        Route::post('/visitantes', 'visitantes_post');



                        Route::get('/correspondencia', 'correspondencia');



                        Route::get('/clientes/adminstrar/{id}/residentes', 'clientes_administrar_residentes');



                        Route::post('/clientes/adminstrar/{id}/residentes', 'clientes_administrar_residentes_crear_post');



                        Route::post('/clientes/adminstrar/{id}/residentes/editar', 'clientes_administrar_residentes_editar_post');



                        Route::post('/clientes/adminstrar/{id}/residentes/vehiculo_crear', 'clientes_administrar_residentes_vehiculo_crear_post');



                        Route::get('/reporte_condiciones_inseguras', 'reporte_condiciones_inseguras');



                        Route::post('/reporte_condiciones_inseguras/subefoto', 'reporte_condiciones_inseguras_subefoto');



                    });



                    Route::controller(reportes::class)->group(function(){



                        Route::any('/reportes/seguimiento_usuarios', 'seguimiento_usuarios');



                        Route::any('/reportes/record_patrullaje', 'record_patrullaje');
                        Route::post('/ajax/reportes/record_patrullaje_resultados', 'record_patrullaje_resultados');



                        Route::any('/reportes/record_patrullaje_global', 'record_patrullaje_global');



                        Route::any('/reportes/cumplimiento', 'cumplimiento');



                        Route::any('/reportes/cumplimiento/informe_pdf', 'cumplimiento_informe_pdf');



                        Route::any('/reportes/novedad_vigsecol', 'novedad_vigsecol');



                        Route::any('/reportes/novedad_vigsecol/{id}', 'novedad_vigsecol_ver_pdf');



                        Route::any('/reportes/seguimiento_usuarios', 'seguimiento_usuarios');



                        Route::any('/supervision_tiempo_real', 'omt_supervision_tiempo_real');

                        Route::any('/reportes/visitantes', 'visitantes');



                        Route::any('/reportes/visitantes/pdf', 'visitantes_pdf');

                        Route::any('/reportes/ingresos_salidas', 'ingresos_salidas');



                        Route::any('/ajax/componenetes/omt/supervision_tiempo_real', 'ajax_componente_control');



                    });

                case '20':

                    Route::controller(clientes::class)->group(function(){

                        Route::get('/clientes/orden_servicio', 'clientes_orden_servicio');

                        Route::get('/clientes/orden_servicio/crear', 'clientes_orden_servicio_crear');

                        Route::get('/clientes/orden_servicio/editar/{id}', 'clientes_orden_servicio_crear');

                        Route::get('/clientes/orden_servicio/ver/{id}', 'clientes_orden_servicio_ver');

                        Route::get('/clientes/editar/{id}', 'clientes_editar');

                        Route::post('/clientes/editar/{id}', 'clientes_editar_post');

                        Route::get('/clientes/administrar/{id}', 'clientes_administrar');
                        Route::post('/ajax/clientes/administrar/info/{id}', 'clientes_administrar_info');
                        Route::post('/ajax/clientes/administrar/contactos/{id}', 'clientes_administrar_contactos');
                        Route::post('/ajax/clientes/administrar/puntos/{id}', 'clientes_administrar_puntos');
                        Route::post('/ajax/clientes/administrar/minuta/{id}', 'clientes_administrar_minuta');

                        Route::post('/ajax/clientes/administrar/copropiedades/{id}', 'clientes_administrar_copropiedades');
                        Route::post('/ajax/clientes/administrar/copropiedades', 'clientes_administrar_copropiedades_data');
                        Route::post('/ajax/clientes/administrar/copropiedades_grupo', 'clientes_administrar_copropiedad_grupo');
                        Route::post('/ajax/clientes/administrar/parqueadero/{id}', 'clientes_administrar_parqueadero');

                         Route::post('/clientes/administrar/regla_parqueadero/{id}', 'clientes_administrar_regla_parqueadero');

                        Route::post('/clientes/administrar/permisos_contacto', 'clientes_administrar_clientes_contacto');

                        Route::post('/clientes/administrar/asignar_contrasena', 'clientes_administrar_asignar_contrasena');

                        Route::get('/clientes/detalles/{id}', 'clientes_detalles');

                        Route::get('/clientes/servicios_ocasionales', 'servicios_ocasionales');

                    });

                    Route::controller(nomina::class)->group(function(){

                        Route::get('/ajax/nomina/novedades/persona/{id}', 'novedades_persona');

                        Route::any('/nomina/novedades', 'novedades');

                        Route::any('/nomina/configurar_horarios_mas_12', 'configurar_horarios_mas_12');

                        Route::any('/nomina/novedades/{persona}', 'novedades');

                        

                        Route::post('/nomina/novedades/aprobar_solicitud_vig/{id}', 'aprobar_solicitud_vig');

                        Route::post('/nomina/novedades/rechazar_solicitud_vig/{id}', 'rechazar_solicitud_vig');





                        Route::get('/nomina/liquidacion/novedades/{id}', 'liquidacion_novedades');

                        Route::get('/nomina/liquidaciones/{id}', 'liquidar');

                        Route::get('/nomina/liquidacion/turnos_vig/{id}', 'liquidacion_turnos_vig');

                        Route::get('/nomina/liquidacion/personas/{id}', 'liquidacion_personas');

                        Route::get('/nomina/liquidacion/centros_costos/{id}', 'liquidacion_centros_costos');

                        Route::get('/nomina/liquidacion/opciones/{id}', 'liquidacion_opciones');

                        Route::get('/nomina/liquidar', 'liquidar');

                        Route::get('/nomina/liquidar/menu/{nomina}', 'liquidar_menu');

                        Route::post('/nomina/liquidar', 'liquidar_crear');

                        Route::post('/nomina/liquidar/finalizar', 'liquidacion_finalizar');

                        Route::post('/nomina/liquidar/descargar/{id}', 'liquidacion_descargar');

                        Route::post('/nomina/liquidar/descargas/', 'liquidacion_descargas');

                        Route::get('/nomina/liquidar/finalizar_completa/{liquidacion_nomina}', 'liquidacion_nomina_finalizar');

                        Route::post('/ajax/nomina/liquidar/cambiar_turno', 'liquidacion_cambiar_turno');

                        Route::get('/nomina/liquidar/persona/informacion', 'liquidacion_persona_informacion');







                        Route::get('/nomina/liquidaciones', 'liquidaciones');
                        Route::get('/nomina/informe_detallado', 'informe_detallado');

                        Route::get('/nomina/personas', 'personas');

                        Route::get('/nomina/centros_costos', 'centros_costos');



                    });

                break;

                 case '13':



                    Route::controller(config::class)->group(function(){

                        Route::get('/config/contratos', 'contratos');



                        Route::post('/config/contratos/crear', 'contratos_crear');



                        Route::post('/config/contratos/editar', 'contratos_editar');







                        Route::post('/config/contratos/editar_contenido/{id}', 'contratos_editar_contenido');


                        Route::get('/config/bodegas', 'bodegas');



                        Route::post('/config/bodegas/crear', 'bodegas_crear');



                        Route::post('/config/bodegas/editar', 'bodegas_editar');







                        Route::get('/config/items_dotacion', 'items_dotacion');



                        Route::post('/config/items_dotacion/crear', 'items_dotacion_crear');



                        Route::post('/config/items_dotacion/editar', 'items_dotacion_editar');



                        



                        Route::get('/config/tallas', 'tallas');



                        Route::post('/config/tallas/crear', 'tallas_crear');



                        Route::post('/config/tallas/editar', 'tallas_editar');







                        Route::get('/config/tallas_categorias', 'tallas_categorias');



                        Route::post('/config/tallas_categorias/crear', 'tallas_categorias_crear');



                        Route::post('/config/tallas_categorias/editar', 'tallas_categorias_editar');

                    });



                    Route::controller(recurso_humano::class)->group(function(){

                        Route::get('/cartas', 'cartas');

                        Route::get('recurso_humano/bd', 'base_datos');

                        

                        Route::get('recurso_humano/crear_persona', 'crear_persona');



                        Route::post('recurso_humano/crear_persona', 'crear_persona_post');



                        Route::get('recurso_humano/contratacion/{id}', 'contratacion');



                        Route::post('recurso_humano/contratacion/{id}/contratar', 'contratacion_contratar');



                        Route::get('recurso_humano/editar_usuario/{id}', 'editar_usuario');



                        Route::get('recurso_humano/en_contratacion', 'en_contratacion');



                        Route::get('recurso_humano/contratados', 'contratados');



                        Route::get('recurso_humano/contratados/{id}', 'contratados_ver');



                        Route::post('recurso_humano/contratados/{id}/agregar_documento', 'contratados_agregar_documento');



                        Route::get('recurso_humano/descartados', 'descartados');



                        Route::get('recurso_humano/retirados', 'retirados');

                        Route::get('recurso_humano/retirados/encuestas', 'retirados_encuestas');

                        Route::get('recurso_humano/retirados/encuestas/editar/{id}', 'retirados_encuestas_editar');



                        Route::get('recurso_humano/retirados/nuevo', 'retirados_nuevo');



                        Route::post('recurso_humano/retirados/nuevo', 'retirados_nuevo_post');



                        Route::get('recurso_humano/reportes/poblacion', 'reporte_poblacion');



                        Route::post('recurso_humano/reportes/poblacion', 'reporte_poblacion_post_filtros');



                        Route::get('recurso_humano/reportes/vacaciones', 'reporte_vacaciones');



                        Route::get('recurso_humano/reportes/guardas_clientes', 'reporte_guardas_clientes');



                        Route::get('recurso_humano/config/general', 'config_general');



                        Route::get('recurso_humano/config/cargos', 'config_cargos');



                        Route::get('recurso_humano/config/notificaciones', 'config_notificaciones');



                        Route::get('recurso_humano/config/mi_firma', 'config_mi_firma');



                        Route::post('recurso_humano/config/mi_firma', 'config_mi_firma_post');







                        Route::get('recurso_humano/ver_entrevista/{usuario}', 'contratados_ver_entrevista'); 



                        Route::get('recurso_humano/editar_entrevista/{usuario}', 'contratados_editar_entrevista'); 



                        Route::get('recurso_humano/ver_verificacion_referencias/{usuario}', 'contratados_ver_verificacion_referencias'); 



                        Route::get('recurso_humano/editar_verificacion_referencias/{usuario}', 'contratados_editar_verificacion_referencias'); 



                        Route::get('recurso_humano/editar_visita_domiciliaria/{usuario}', 'contratados_editar_visita_domiciliaria'); 



                        Route::get('recurso_humano/ver_visita_domiciliaria/{usuario}', 'contratados_ver_visita_domiciliaria'); 







                        Route::get('/recurso_humano/proceso_disciplinario', 'proceso_disciplinario');

                        Route::get('/recurso_humano/faltas_disciplinarias', 'faltas_disciplinarias');

                        Route::get('/recurso_humano/faltas_disciplinarias/{id}/pdf', 'faltas_disciplinarias_pdf');

                        Route::get('/recurso_humano/faltas_disciplinarias/crear', 'faltas_disciplinarias_crear');

                        Route::post('/recurso_humano/faltas_disciplinarias/crear', 'faltas_disciplinarias_crear_post');

                        Route::post('/ajax/recurso_humano/faltas_disciplinarias/buscar_persona', 'ajax_faltas_disciplinarias_buscar_persona');



                        Route::get('/recurso_humano/proceso_disciplinario/{id}/pdf', 'proceso_disciplinario_pdf');



                        Route::get('/recurso_humano/proceso_disciplinario/{id}', 'proceso_disciplinario_crear');



                        Route::post('/recurso_humano/proceso_disciplinario/{id}', 'proceso_disciplinario_crear_post');



                        Route::get('/recurso_humano/proceso_disciplinario/crear', 'proceso_disciplinario_crear');



                        Route::post('/recurso_humano/proceso_disciplinario/crear', 'proceso_disciplinario_crear_post');







                        Route::get('/recurso_humano/contrato/{id}', 'genera_contrato');











                        Route::get('/cartas', 'cartas');



                        Route::get('/cartas/{carta}/{usuario}', 'carta_usuario');



                    });



                    Route::controller(almacen::class)->group(function(){

                        Route::get('/almacen/entrega/nueva', 'entrega_nueva');

                        Route::get('/almacen/entrega', 'entregas');

                        Route::get('/almacen/entrega/{id}', 'entrega_ver');

                        Route::get('/almacen/entrega/editar/{id}', 'entrega_editar');



                        Route::get('/almacen/conteo', 'conteos');

                        Route::get('/almacen/conteos/ver/{id}', 'conteos_ver');

                        Route::get('/almacen/conteos/crear', 'conteos_crear');

                        Route::post('/almacen/conteos/crear', 'conteos_crear_post');



                        Route::get('/almacen/verificacion_compras', 'verificacion_compras');

                        Route::get('/almacen/verificacion_compras/crear', 'verificacion_compras_crear');

                        Route::post('/almacen/verificacion_compras/crear', 'verificacion_compras_crear_post');

                        Route::get('/almacen/verificacion_compras/ver', 'verificacion_compras_ver');



                        Route::get('/almacen/acta_baja', 'acta_baja');

                        Route::get('/almacen/acta_baja/crear', 'acta_baja_crear');

                        Route::post('/almacen/acta_baja/crear', 'acta_baja_crear_post');

                        Route::get('/almacen/acta_baja/ver/{id}', 'acta_baja_ver');



                        Route::get('/almacen/control_elementos_reparacion', 'control_elementos_reparacion');

                        Route::get('/almacen/control_elementos_reparacion/crear', 'control_elementos_reparacion_crear');

                        Route::post('/almacen/control_elementos_reparacion/crear', 'control_elementos_reparacion_crear_post');

                        Route::get('/almacen/control_elementos_reparacion/ver/{id}', 'control_elementos_reparacion_ver');



                        Route::get('/almacen/entregas_libros_reglamentarios', 'entregas_libros_reglamentarios');

                        Route::post('/almacen/entregas_libros_reglamentarios/crear', 'entregas_libros_reglamentarios_crear_post');

                        Route::get('/almacen/entregas_libros_reglamentarios/ver', 'entregas_libros_reglamentarios_ver');

                    });



                    break;

                case '19':
                    Route::controller(encuesta::class)->group(function(){

                        Route::get('/encuestas', 'encuestas');

                        Route::get('/encuestas/crear', 'encuestas_crear');

                        Route::post('/encuestas/cerrar/{id}', 'encuestas_cerrar');

                        Route::get('/encuestas/editar/{id}', 'encuestas_editar')->name('encuestas.editar');

                        Route::get('/encuestas/administrar/{id}', 'encuestas_administrar')->name('encuestas.administrar');

                        Route::get('/encuestas/administrar/versiones/{id}', 'encuestas_administrar_versiones');

                        Route::post('/encuestas/administrar/versiones/{id}', 'encuestas_administrar_versiones_crear');
                        Route::post('/ajax/encuestas/editar_respuesta', 'encuestas_editar_respuesta');
                        Route::post('/encuestas/administrar/versiones/{id}/editar', 'encuestas_administrar_versiones_editar');

                        Route::get('/encuestas/administrar/respuestas/{id}', 'encuestas_administrar_respuestas');

                        Route::any('/encuestas/administrar/estadisticas/{id}', 'encuestas_administrar_estadisticas');



                    }); 
                    Route::controller(sst::class)->group(function(){

                        Route::get('/sst/encuesta_sociodemografica/resultados/{id}', 'encuesta_sociodemografica_resultados');

                        Route::post('/sst/encuesta_sociodemografica/crear_encuesta', 'encuesta_sociodemografica_crear_encuesta');

                        Route::get('/sst/chequeo_vehiculos', 'chequeo_vehiculos');

                        Route::get('/sst/chequeo_vehiculos/ver/{id}', 'chequeo_vehiculos_ver');

                        Route::get('/sst/chequeo_vehiculos/crear', 'chequeo_vehiculos_crear');

                        Route::get('/sst/inspeccion_seguridad', 'inspeccion_seguridad');
                        Route::get('/sst/documentos', 'documentos');
                        Route::post('/sst/documentos', 'documentos_post');

                        Route::get('/sst/inspeccion_seguridad/crear', 'inspeccion_seguridad_crear');

                        Route::get('/sst/inspeccion_seguridad/ver/{id}', 'inspeccion_seguridad_ver');

                    });

                    Route::controller(config::class)->group(function(){

                        Route::get('/config/chequeo_vehiculos', 'chequeo_vehiculos');

                        Route::post('/config/chequeo_vehiculos/crear_concepto', 'chequeo_vehiculos_crear_concepto');

                        Route::post('/config/chequeo_vehiculos/crear_elemento', 'chequeo_vehiculos_crear_elemento');

                    });

                    Route::controller(recurso_humano::class)->group(function(){

                        Route::get('recurso_humano/config/mi_firma', 'config_mi_firma');

                        Route::post('recurso_humano/config/mi_firma', 'config_mi_firma_post');

                        Route::get('recurso_humano/editar_visita_domiciliaria/{usuario}', 'contratados_editar_visita_domiciliaria'); 

                        Route::get('/recurso_humano/proceso_disciplinario', 'proceso_disciplinario');

                        Route::get('/recurso_humano/faltas_disciplinarias', 'faltas_disciplinarias');

                        Route::get('/recurso_humano/faltas_disciplinarias/{id}/pdf', 'faltas_disciplinarias_pdf');

                        Route::get('/recurso_humano/faltas_disciplinarias/crear', 'faltas_disciplinarias_crear');

                        Route::post('/recurso_humano/faltas_disciplinarias/crear', 'faltas_disciplinarias_crear_post');

                        Route::post('/ajax/recurso_humano/faltas_disciplinarias/buscar_persona', 'ajax_faltas_disciplinarias_buscar_persona');

                        Route::get('/recurso_humano/proceso_disciplinario/{id}/pdf', 'proceso_disciplinario_pdf');

                        Route::get('/recurso_humano/proceso_disciplinario/{id}', 'proceso_disciplinario_crear');

                        Route::post('/recurso_humano/proceso_disciplinario/{id}', 'proceso_disciplinario_crear_post');

                        Route::get('/recurso_humano/proceso_disciplinario/crear', 'proceso_disciplinario_crear');

                        Route::post('/recurso_humano/proceso_disciplinario/crear', 'proceso_disciplinario_crear_post');

                    });

                    break;



            }



    }



    Route::controller(ajax::class)->group(function(){



        Route::get('/ajax/autorizacion_programacion/{id}', 'autorizacion_programacion_permisos');



        Route::get('/camparar_base_datos', 'camparar_base_datos');

        Route::get('/notificaciones', 'notificaciones');

        Route::post('/ajax/notificaciones/marcar_vista', 'notificaciones_marcar_vista');

        Route::get('/crear_notificaciones', 'crear_notificaciones');

        Route::get('/reporte_operativo_clientes', 'reporte_operativo_clientes');

        Route::post('/ajax/programacion/novedad_ingreso_retiro/valida_fechas_programadas', 'programacion_novedad_ingreso_retiro_valida_fechas_programadas');

        Route::post('/ajax/programacion/novedades_crear/asignar_puesto', 'programacion_novedad_asignar_puesto');

        Route::get('/ajax/clientes/administrar/permisos_contacto/{id}', 'clientes_administrar_permisos_contacto');

        Route::get('/ajax/clientes/administrar/notificaciones/{id}', 'clientes_administrar_notificaciones');

        Route::get('/documentos_gh', 'documentos_gh');

        Route::get('/ajax/marcar_registro_visto', 'marcar_registro_visto');



        Route::post('/ajax/documentos_gh', 'documentos_gh_ajax');

        Route::get('/ajax/reportes/record_patrullaje/detalles', 'reportes_record_patrullaje_detalles');

        Route::get('/ajax/usuarios/persmisos_areas/{id}', 'permisos_areas_usuario');



        Route::post('/ajax/real_foto', 'real_foto');



        Route::post('/ajax/existe_cedula', 'existe_cedula');

        Route::post('/ajax/existe_cedula_cantidad', 'existe_cedula_cantidad');



        Route::post('/ajax/bitacora_programacion', 'bitacora_programacion');







     







        Route::get('/ajax/vigilantes_cliente/{cliente}', 'vigilantes_cliente');



        Route::get('/ajax/recurso_humano/contratados/contrato/{contrato}', 'recurso_humano_contrato');



        Route::get('/ajax/recurso_humano/contratados/contrato_firmas/{contrato}', 'recurso_humano_contrato_firmas');



        



        Route::post('/ajax/tecnico/actualiza_equipos', 'tecnico_actualiza_equipos');



        Route::post('/ajax/tecnico/finalizar_reporte', 'tecnico_finalizar_reporte');



        Route::post('/ajax/vigilante/vehiculos/busca_placa', 'vigilante_vehiculos_busca_placa');



        Route::get('/ajax/maps/mostrar_punto', 'maps_mostrar_punto');



        Route::get('/ajax/vigilante/busca_cedula_visitante', 'vigilante_busca_cedula_visitante');



        Route::get('/ajax/cliente/punto/eliminar/{id}', 'eliminar_punto_cliente');



        Route::get('/ajax/geolocalizar', 'geolocalizar');



        Route::get('/ajax/clientes/administrar/registros_marcacion_punto/{id}', 'ajax_registros_marcacion_punto');



        Route::get('/ajax/vigilante/validar_punto', 'vigilante_validar_punto');



        Route::get('/ajax/vigilante/carga_visitas', 'ajax_vigilante_carga_visitas_cliente');



        Route::get('/ajax/vigilante/carga_visitas_salida', 'ajax_vigilante_carga_visitas_salida_cliente');



        Route::get('/ajax/clientes/administrar/registro_visitantes/{id}', 'ajax_clientes_administrar_registro_visitantes');



        Route::get('/ajax/clientes/administrar/vehiculos/{id}', 'ajax_clientes_administrar_vehiculos');



        Route::post('/ajax/registro_visitante', 'ajax_registro_visitante');



        Route::post('/ajax/visitantes/salida_visitante', 'salida_visitante');



        Route::get('/ajax/visitantes/bloquear_acceso/{id}', 'visitante_bloquear_acceso');

        Route::get('/programacion_limpieza', 'programacion_limpieza');



        Route::get('/ajax/visitantes/permitir_acceso/{id}', 'visitante_permitir_acceso');



        Route::get('/ajax/usuarios/administrar/busca_turnos', 'usuarios_administrar_busca_turnos');



        Route::get('/ajax/usuarios/administrar/asignar_turno', 'usuarios_administrar_asignar_turno');



        Route::get('/ajax/supervisor/patrullaje/guardar/{id}', 'supervisor_patrullaje_guardar');



        Route::get('/ajax/supervisor/patrullaje/busca_firma/{id}', 'supervisor_patrullaje_busca_firma');



        Route::get('/ajax/clientes/visitas/crear/{id}', 'clientes_visitas_crear_actualiza');



        Route::get('/ajax/clientes/visitas/eliminar_foto/', 'clientes_visitas_elimina_foto');



        Route::post('ajax/captura-firma', 'captura_firma');



        Route::get('/ajax/clientes/visitas/busca_firmas', 'cliente_visitas_busca_firmas');



        Route::get('/ajax/clientes/administrar/tareas_punto/{id}', 'clientes_administrar_tareas_punto');



        Route::post('/ajax/autoform', 'autoform');

        Route::post('/autoform/close', 'autoform_close');



        Route::post('/ajax/autoitem', 'autoitem');



        Route::post('/ajax/componente', 'componente');



        Route::post('/ajax/eliminar_item', 'eliminar_item');



        Route::get('/ajax/borra_formulario', 'borra_formulario');



        Route::post('/ajax/cliente/crear_contacto', 'cliente_crear_contacto');



        Route::get('/ajax/cliente/residente/eliminar/{id}', 'cliente_residente_eliminar');



        Route::post('/ajax/clientes/accionistas', 'clientes_accionistas');



        Route::post('/ajax/clientes/referencias_comerciales', 'clientes_referencias_comerciales');



        Route::post('/ajax/clientes/analisis_riesgos/sube_imagen/{id_riesgo}', 'analisis_riesgo_sube_imagen');



        Route::post('/ajax/clientes/actividades_internacionales', 'clientes_actividades_internacionales');



        Route::post('/ajax/sube_foto', 'sube_foto');



        Route::post('/ajax/autoformfile', 'autoformfile');



        Route::post('/ajax/recurso_humano/editar_visita_domiciliaria/sube_foto', 'recurso_humano_sube_foto_visita_domiciliaria');



        Route::get('/ajax/programacion/personas_sin_programacion', 'programacion_personas_sin_programar');

        Route::get('/ajax/correos_eliminados/aprobar/{id}', 'correos_eliminados_aprobar');

        Route::get('/ajax/correos_eliminados/descartar/{id}', 'correos_eliminados_descartar');

        Route::get('/ajax/correos_eliminados/crear_excepcion/{id}', 'correos_eliminados_crear_excepcion');



    });



    if(empty($_SESSION['nombre']) && $_SERVER['REQUEST_URI']!="/" && $_SERVER['REQUEST_URI']!="/cookie_session" && $_SERVER['REQUEST_URI']!="/android2/login" && $_SERVER['REQUEST_URI']!="/android/login" && $_SERVER['REQUEST_URI']!="/android/inicia_ronda" && !strpos($_SERVER['REQUEST_URI'], 'capacitaciones/asistentes_qr/') && !strpos($_SERVER['REQUEST_URI'], 'cron') && !strpos($_SERVER['REQUEST_URI'], 'autorizaciones_contacto') && !strpos($_SERVER['REQUEST_URI'], 'correos')  && !strpos($_SERVER['REQUEST_URI'], 'wapi') && !strpos($_SERVER['REQUEST_URI'], 'encuesta') && !strpos($_SERVER['REQUEST_URI'], 'captura-firma')  && !strpos($_SERVER['REQUEST_URI'], 'signature') && !strpos($_SERVER['REQUEST_URI'], 'android') && !strpos($_SERVER['REQUEST_URI'], 'orden_servicio')  && !strpos($_SERVER['REQUEST_URI'], 'copy') &&  !strpos($_SERVER['REQUEST_URI'], 'android_services') &&  !strpos($_SERVER['REQUEST_URI'], 'ref') &&  !strpos($_SERVER['REQUEST_URI'], 'api')  &&  !strpos($_SERVER['REQUEST_URI'], 'panico'))
    {



        echo "<script> location.href='/';</script>";



        exit();



    }



    if(!auth::valida_turno() && $_SERVER['REQUEST_URI']!="/fuera_turno" && !empty($_SESSION['rol']))



    {



        echo "<script> location.href='/fuera_turno';</script>";



        exit();



    }



    if($_SERVER['REQUEST_URI']=="/server")



    {



        dd($_SERVER);



    }



}



Route::controller(auth::class)->group(function(){



    Route::get('/inicio_principal', 'inicio_principal');

    Route::get('/cookie_session', 'cookie_session');



    Route::get('/logout', 'logout');



    Route::get('/', 'inicio');



    Route::post('/', 'login_post');



    Route::post('/android/login', 'login_android');



    Route::get('/cript/{bla}', 'encript');



    Route::get('/cron/copy', 'cron_copy');

    Route::get('/prueba_correo', 'prueba_correo');

    Route::get('/filtrar_correos', 'filtrar_correos');



});



Route::controller(clientes::class)->group(function(){

    Route::get('/signature/cliente_os/{basex3}', 'firmar_orden_servicio_externo');

    Route::post('/signature/cliente_os/{basex3}', 'firmar_orden_servicio_externo_post');

    Route::get('/clientes/orden_servicio/ver/{id}', 'clientes_orden_servicio_ver');

});

Route::controller(auth::class)->group(function(){

    Route::get('/{ruta}', 'no_encontrado');

});

Route::controller(colegioAleman::class)->group(function(){

    Route::post('/android/aleman/login', 'login_android');

    Route::post('/android/aleman/data_auxiliares', 'data_auxiliares_android');

    Route::post('/android/aleman/sincronizacion', 'sincronizacion_android');

    Route::post('/api/aleman/zkteco/registros', 'sincronizar_registros');

    Route::any('/api/aleman/zkbio/personas', 'api_zkbio_personas');

    Route::get('/android/aleman/ingresos_salidas', 'ingresos_salidas');

    Route::get('/android/aleman/ingresos_salidas', 'ingresos_salidas');

});

Route::controller(cron::class)->group(function(){

    Route::get('/cron/indicador_cliente_mensual', 'indicador_cliente_mensual');

});

Route::controller(ajax::class)->group(function(){

    Route::post('ajax/captura-firma', 'captura_firma');

});


Route::controller(sst::class)->group(function(){
    Route::post('/ajax/sst/obtener_documentos', 'ajax_obtener_documentos');
}); 
Route::controller(encuesta::class)->group(function(){

    Route::get('/encuesta/{token}', 'encuesta');

    Route::post('/encuesta/{token}', 'encuesta_post');

}); 

Route::controller(usuarios::class)->group(function(){
    Route::get('/capacitaciones/asistentes_qr/{token}', 'capacitacion_asistente_qr');
    Route::post('/ajax/capacitaciones/asistentes_qr/{token}', 'capacitacion_asistente_qr_ajax');
    Route::post('/ajax/capacitaciones/asistentes_qr/{token}/cedula/buscar', 'capacitacion_asistente_qr_ajax_cedula');
}); 



//// Renderizar scripts y css
Route::get('/scripts/{path}', function ($path) {
    $fullPath = resource_path("js/" . $path);
    if (file_exists($fullPath)) {
        return response()->file($fullPath)->header('Content-Type', 'application/javascript');
    }
    abort(404);
})->where('path', '.*');
Route::get('/css/{path}', function ($path) {
    $fullPath = resource_path("css/" . $path);
    if (file_exists($fullPath)) {
        return response()->file($fullPath)->header('Content-Type', 'text/css');
    }
    abort(404);
})->where('path', '.*');
