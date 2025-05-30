
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
