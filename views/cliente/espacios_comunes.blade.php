<!-- //! INDICE
   
//** ALERTAS MODALES : aqui se colocan todas las alertas y modales de las vistas

//** VISTAS BODY : todo lo que va en la vista principal calendario y lista de espacios comunes}

//** HEAD : todo lo que va en la cabezera y encima de las vistas

//* SCRIPTS PARA LA VIEW [VER RESERVAS] CALENDARIO : todos los scripts con metodo relacionados al calendario

///////////////////////////////////////////////////

//* HEAD -->

@extends('l')

@section('2')



    <div class="row me-2">

        <script src='https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.15/index.global.min.js'></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- //* HEAD -->

        <!-- //* VISTAS BODY PRINCIPALES -->

        <!-- VISTA CALENDARIO    -->

        <div style="margin-left: 20px; margin-rigth: 20px; padding: 5px; gap: 10px; display: flex;">

            <!-- Columna izquierda (calendario) -->


            <div style="width: 100%;">
                <div class="col-12" id='verCalendar' style="display : block  ">
                    <!-- BOTON VER ESPACIOS WIDGET   -->


                    <div class="col-12 ">
                        <center>
                            <h4>Reservas de Espacios </h4>
                        </center>
                    </div>
                    <button class="btn btn-success right ms-2 ma-2 " onclick="verReservasPendientesPorAutorizar();">
                        Autorizar reservas <div style="position: relative; display: inline-block;">
                            <i class="fas fa-bell bell-icon"></i>
                            <div id="notificacionesReservas"
                                style="
                              position: absolute;
                              top: -6px;
                              right: -6px;
                              background-color: red;
                              color: white;
                              font-size: 10px;
                              font-weight: bold;
                              padding: 2px 6px;
                              border-radius: 50%;
                              z-index: 1;
                              box-shadow: 0 0 0 2px white;
                            ">
                                0
                            </div>
                        </div> </button>

                    <button class="btn btn-success right ms-2 ma-2 " onclick="verEspacios();">Administrar espacios</button>

                    <div class="col-12" id='calendar' style="display : block "></div>


                </div>
            </div>
        </div>

        <!--VISTA CALENDARIO   FIN  -->




        <!-- VISTA ESPACIOS COMUNES   -->
        <div id="vistaEspacios" class="col-12" hidden>
            <div class="col-12 ">
                <center>
                    <h4>Espacios comunes</h4>
                </center>
            </div>
            <button class="btn btn-success right " onclick="$('#btnCrearEspacio').modal('show');">Crear espacio</button>
            <!-- BOTON CREAR ESPACIOS WIDGET   -->
            <button class="btn right me-2 ms-2" style="background-color:rgb(29, 60, 214); color: white;"
                onclick="verReservas();">Ver reservas</button>

            <table class="dt table" style="     min-width: 100%;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Descripción</th>
                        <th>Horas min</th>
                        <th>Horas max</th>
                        <th>Hora inicio</th>
                        <th>Hora fin</th>
                        <th>Editar</th>
                        <th>Eliminar</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($espacios as $e)
                        <tr>
                            <td>{{ $e->id }}</td>
                            <td>{{ $e->descripcion }}</td>
                            <td>{{ $e->horas_min }}</td>
                            <td>{{ $e->horas_max }}</td>
                            <td>{{ $e->hora_inicio }}</td>
                            <td>{{ $e->hora_fin }}</td>
                            <td class="center">
                                <button class="btn btn-success"
                                    onclick="editarEspacio(
                                                '{{ $e->id }}', 
                                                '{{ $e->descripcion }}', 
                                                '{{ $e->horas_min }}', 
                                                '{{ $e->horas_max }}', 
                                                '{{ $e->hora_inicio }}', 
                                                '{{ $e->hora_fin }}'
                                                   );">
                                    <i class="fa fa-edit"></i></button>
                            </td>

                            <td class="center">
                                <form id="form2" action="/cliente/espacios/eliminar_espacio" method="post">
                                    <input type="hidden" name="idEliminar" value="0">



                                    <button type="button" class="btn btn-danger"
                                        onclick="confirmarEliminacionEspacio('form2','{{ $e->id }}')">Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- VISTA ESPACIOS COMUNES   FIN -->

        <!-- //* VISTAS BODY PRINCIPALES -->

        <!-- ALERTAS MODALES-->


        <!-- Alerta Modal  cargando -->

        <div class="modal fade" id="loadingModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
            data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="background: transparent; border: none; box-shadow: none;">
                    <div class="modal-body text-center">
                        <h3 style="color: red;">Eliminando reserva</h3>
                        <div id="circular" class="loader-container">
                            <div class="loader"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerta Modal  cargando FIN -->


        <!-- Alerta Modal  creando creando reserva -->

        <div class="modal fade" id="loadingModalCreandoReserva" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
            data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="background: transparent; border: none; box-shadow: none;">
                    <div class="modal-body text-center">
                        <h3 style="color: blue;">Creando reserva</h3>
                        <div id="circular" class="loader-container">
                            <div class="loader"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerta Modal  creando reservas FIN -->



        <!-- Alerta Modal editar espacio -->
        <div class="modal" id="btnEditarEspacio" tabindex="-1" role="dialog">

            <form id="EditarEspacio" action="/cliente/espacios/actualizar_espacios" method="post">

                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">

                                <input type="hidden" id="idEditar" name="idEditar" value=""> <!-- ID oculto -->


                                <!-- Campo para la descripción del evento -->
                                <div class="col-12 form-group">
                                    <label for="descripcion">Descripción</label>
                                    <input id="descripcion" type="text" name="descripcion" class="form-control" required>
                                </div>

                                <!-- Selección del mínimo de horas -->
                                <div class="col-12 form-group">
                                    <label for="minHoras">Mínimo horas de reserva</label>
                                    <input class="form-control" id="minHoras" name="minHoras" required type="number"
                                        oninput="validarNumero(this)">
                                </div>
                                <!-- Selección del máximo de horas -->
                                <div class="col-12 form-group">
                                    <label for="maxHoras">Máximo horas de reserva</label>
                                    <input id="maxHoras" type="number" name="maxHoras" oninput="validarNumero(this)"
                                        class="form-control" required>
                                </div>


                                <!-- Selección de la hora de inicio -->
                                <div class="col-12 form-group">
                                    <label for="horaInicio">Hora inicio</label>
                                    <input type="time" id="horaInicio" name="horaInicio" class="form-control"
                                        required>
                                </div>

                                <!-- Selección de la hora de fin -->
                                <div class="col-12 form-group">
                                    <label for="horaFin">Hora fin</label>
                                    <input type="time" id="horaFin" name="horaFin" class="form-control" required>
                                </div>


                            </div>
                        </div>


                        <!-- Botones de acción -->
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Guardar</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!-- Alerta Modal editar espacio  fin -->


        <!-- Alerta Modal crear espacio -->
        <div class="modal" id="btnCrearEspacio" tabindex="-1" role="dialog">
            <form id="CrearEspacioForm" action="/cliente/espacios/crear" method="post">

                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Crear espacio comun</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">


                                <!-- Campo para la descripción del evento -->
                                <div class="col-12 form-group">
                                    <label for="descripcion">Descripción</label>
                                    <input id="descripcionEspacio" type="text" name="descripcionEspacio"
                                        class="form-control" required>
                                </div>

                                <!-- Selección del mínimo de horas -->
                                <div class="col-12 form-group">
                                    <label for="minHoras">Mínimo horas de reserva</label>
                                    <input class="form-control" id="minHorasFormCrearEspacio"
                                        name="minHorasFormCrearEspacio" required type="number"
                                        oninput="validarNumero(this)">
                                </div>
                                <!-- Selección del máximo de horas -->
                                <div class="col-12 form-group">
                                    <label for="maxHoras">Máximo horas de reserva</label>
                                    <input id="maxHorasFormCrearEspacio" type="number" name="maxHorasFormCrearEspacio"
                                        oninput="validarNumero(this)" class="form-control" required>
                                </div>

                                <div class="col-12 form-group">
                                    <label for="horaInicio">Hora apertura <span></span></label>
                                    <input type="time" id="horaInicioFormCrearEspacio"
                                        name="horaInicioFormCrearEspacio" class="form-control" required>
                                </div>

                                <!-- Selección de la hora de fin -->
                                <div class="col-12 form-group">
                                    <label for="horaFin">Hora cierre</label>
                                    <input type="time" id="horaFinFormCrearEspacio" name="horaFinFormCrearEspacio"
                                        class="form-control" required>
                                </div>

                            </div>
                        </div>


                        <!-- Botones de acción -->
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Guardar</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!-- Alerta Modal crear espacio  fin -->


        <!-- Alerta Modal añadirReserva reserva -->
        <div class="modal fade" id="añadirReserva" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
            aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Nueva Reserva</h5><span id="fechaNuevaReservaText"> dddddddd</span>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div id="circular" class="loader-container" hidden>
                        <div class="loader"></div>
                    </div>
                    <div id="formCrearReserva" class="modal-body">

                        <div class="row">

                            <input type="hidden" id="idEditar" name="idEditar" value=""> <!-- ID oculto -->


                            <div class="col-12 form-group">
                                <label>Seleccionar residente</label>

                                <select class="form-control select2modal " id="optionsResidenteIdFormNuevaReserva">
                                    <option value="" disabled selected>buscar...</option>
                                    @foreach ($cliente_residente as $residente)
                                        <option value="{{ $residente->id }}">{{ $residente->nombre }}</option>
                                    @endforeach
                                </select>

                            </div>



                            <div class="col-12 form-group">

                                <label for="optionsEspaciosIdFormNuevaReserva">Selecciona un espacio</label>

                                <select id="optionsEspaciosIdFormNuevaReserva" class="form-control select2modal "
                                    required>

                                    <option value="" disabled selected>Seleccione...</option>

                                    @foreach ($espacios as $e)
                                        <option value="{{ $e->id }}">{{ $e->descripcion }}</option>
                                    @endforeach

                                </select>

                                <div style="display: flex; justify-content: space-between; font-size: 0.8em;">
                                    <span id="horasMin">Hora min</span>
                                    <span id="horasMax">Hora max</span>
                                </div>

                            </div>




                            <!-- Selección de la hora de inicio -->
                            <div class="col-12 form-group">
                                <label for="horaInicio">Hora inicio <span style="font-size: 0.8em;"
                                        id="horaInicioSpanFormNuevaReserva">()</span></label>
                                <input type="time" id="horaInicioFormNuevaReserva" name="horaInicioFormNuevaReserva"
                                    class="form-control" required>
                            </div>

                            <!-- Selección de la hora de fin -->
                            <div class="col-12 form-group">
                                <label for="horaFin">Hora fin <span style="font-size: 0.8em;"
                                        id="horaFinSpanFormNuevaReserva">()</span></label>
                                <input type="time" id="horaFinFormNuevaReserva" name="horaFinFormNuevaReserva"
                                    class="form-control" required>
                            </div>



                        </div>

                        <div class="col-12 form-group">
                            <label for="observaciones">observaciones</label>
                            <input id="observacionesFormNuevaReserva" type="text" name="observacionesFormNuevaReserva"
                                class="form-control" required rows="4"
                                placeholder="Escribe tu observación aquí...">
                        </div>

                    </div>



                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button id="btnGuardarFormNuevaReserva" type="button" class="btn btn-primary">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Alerta Modal añadirReserva reserva fin   -->

        <!-- //!--------------------------------------------------------------------------------------------------------- -->



        <!-- Alerta Modal autorizar reservas-->
        <div class="modal fade" id="modalAutorizarReservas" tabindex="-1" role="dialog"
            aria-labelledby="fechaModalLabel">
            <div class="modal-dialog modal-sm modal-dialog-centered" role="document"> <!-- Tamaño pequeño + centrado -->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Autorizacion de reservas</h5>

                    </div>
                    <div id="pendienteColumna",
                        style="
        width:100%;
        color: #000;
        font-weight: bold;
        border: 1px solid rgb(192, 190, 190);
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 10px;
        text-align: center;
      ">


                        <!-- Lista de objetos debajo -->


                        <!-- Puedes seguir agregando objetos -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerta Modal autorizar reservas  fin-->


        <!-- ALERTAS MODALES FIN-->

        <!--  SCRIPTS PARA LA VIEW  [VER RESERVAS] CALENDARIO -->
        <script>
            // --------------------------------------------  VARIABLES --------------------------------------------
            let miVariable = @json($reservas);
            let reservaciones = @json($reservas); // hacemos el cast a un objeto json
            let calendar;
            let idEvento;
            let selectedDate;
            let horaUltimaReserva;
            let reservasArray = [];
            let reservasArrayAutorizar = [];
            let listaReservasMesActual = [];



            //---------------------------------------------   LISTENERS --------------------------------------------



            // escuchamos los eventos del calendario
            document.addEventListener('DOMContentLoaded', function() {


                for (let index = 0; index < miVariable.length; index++) {

                    const timestamp = Date.parse(miVariable[index]["fecha"]);
                    const fechae = new Date(timestamp).toISOString().split('T')[0];


                    reservasArrayAutorizar.push(

                        {
                            id: miVariable[index]["id"],
                            title: miVariable[index]["descripcion"],
                            start: fechae,
                            // allDay: true,
                            //end: miVariable[index]["hora_fin"],
                            extendedProps: {
                                idReserva: miVariable[index]["id"],
                                nombre: miVariable[index]["nombre"],
                                espacioComun: miVariable[index]["descripcion"],
                                horaInicio: miVariable[index]["hora_inicio"],
                                horaFin: miVariable[index]["hora_fin"],
                                observaciones: miVariable[index]["observaciones"],
                                correo: miVariable[index]["correo"],
                                confirmacion: miVariable[index]["confirmacion"],


                            },

                        });

                    if (miVariable[index]["confirmacion"] == 1) {
                        reservasArray.push(

                            {
                                id: miVariable[index]["id"],
                                title: miVariable[index]["descripcion"],
                                start: fechae,
                                // allDay: true,
                                //end: miVariable[index]["hora_fin"],
                                extendedProps: {
                                    idReserva: miVariable[index]["id"],
                                    nombre: miVariable[index]["nombre"],
                                    espacioComun: miVariable[index]["descripcion"],
                                    horaInicio: miVariable[index]["hora_inicio"],
                                    horaFin: miVariable[index]["hora_fin"],
                                    observaciones: miVariable[index]["observaciones"],
                                    correo: miVariable[index]["correo"],
                                    confirmacion: miVariable[index]["confirmacion"],


                                },

                            });
                    }

                }



                var calendarEl = document.getElementById('calendar');

                //obtenemos el calendar por el id y creamos un objeto calendar           
                calendar = new FullCalendar.Calendar(

                    calendarEl, {
                        timeZone: 'local',
                        locale: 'es', // Traducción al español
                        initialView: 'dayGridMonth',
                        selectable: false,
                        editable: false,
                        showNonCurrentDates: false, // Oculta días fuera del mes actual
                        fixedWeekCount: false, // Opcional: evita que se rellene con semanas vacías
                        // headerToolbar: {
                        //     left: 'prev,next today',
                        //     center: 'title',
                        //     right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek '
                        // },
                        events: reservasArray,

                        eventContent: function(arg) {


                            return {
                                html: `
    <div style="width: 100%; padding: 4px;">
        
      <div style="display: flex; width: 100%;">
         <div style="width: 60%; color: white; padding: 2px 0px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;">
          ${arg.event.title}
       </div>
        <div style="width: 40%; text-align: right; padding: 2px 0px;">
          ${convertirHoraAMPM(arg.event.extendedProps.horaInicio)}
        </div>
      </div>

      <div style="display: flex; width: 100%; margin-top: 0px;">
        <div style="width: 60%; color: white; padding: 2px 0px; border-radius: 4px;">
    ${arg.event.id}

        </div>
        <div style="width: 40%; text-align: right; padding: 2px 0px;">
          ${convertirHoraAMPM(arg.event.extendedProps.horaFin)}
        </div>
      </div>
    </div>
  `
                            };
                        },




                        //funcion al dar click en el cuadro de la fecha

                        dateClick: function(info) {




                            // Fecha que tienes en formato de string
                            const fechaString = info["date"];

                            // Convertir a objeto Date
                            const fecha1 = new Date(fechaString);

                            // Otra fecha para comparar
                            const fecha2 = new Date(); // Fecha actual

                            fecha1.setHours(0, 0, 0, 0);
                            fecha2.setHours(0, 0, 0, 0);



                            document.getElementById("fechaNuevaReservaText").innerText = formatoFechaEnLetras(
                                info["dateStr"]);

                            // Comparación de fechas
                            if (fecha1.getTime() > fecha2.getTime()) {
                                selectedDate = info["dateStr"];
                                $('#añadirReserva').modal('show');
                                //  console.log("Las fechas son iguales.");
                            } else if (fecha1.getTime() < fecha2.getTime()) {
                                //   console.log("La fecha1 es en el pasado.");
                            } else {


                                selectedDate = info["dateStr"];
                                $('#añadirReserva').modal('show');
                                //  console.log("Las fechas son iguales.");
                            }


                        },

                        eventClick: function(info) {

                            const cardReservaHtml = ` 
      <div style="border: 1px solid #ccc; border-radius: 10px; padding: 15px; text-align: left; margin-top: 10px;">

        <div><strong>${info.event.title}</strong> <span style="float:right;">${convertirHoraAMPM(info.event.extendedProps.horaInicio)}</span></div>
        <div>${info.event.extendedProps.nombre}<span style="float:right;">${convertirHoraAMPM(info.event.extendedProps.horaFin)}</span></div>
        <div style="margin-top:5px;">Fecha reserva <span style="float:right;">${formatoFechaEnLetras(info.event.start)}</span></div>

      </div>`;






                            promptsDetallesReserva(
                                info.event.extendedProps.correo,
                                info.event.extendedProps.nombre,
                                info.event.start,
                                convertirHoraAMPM(info.event.extendedProps.horaInicio),
                                convertirHoraAMPM(info.event.extendedProps.horaFin),
                                info.event.title,
                                3, //confirmacion,
                                info.event.id,

                                cardReservaHtml,
                                '',



                            );
                         

                        },

                        eventDidMount: function(info) {
                            idReserva = info.event.extendedProps.idReserva;
                            nombre = info.event.extendedProps.nombre;
                            espacioComun = info.event.extendedProps.espacioComun;
                            observaciones = info.event.extendedProps.observaciones;
                            horaInicio = info.event.extendedProps.horaInicio;
                            horaFin = info.event.extendedProps.horaFin;;


 

                            var tooltip = new bootstrap.Tooltip(info.el, {
                                title: `
        <div style="text-align: left; font-size: 14px;">
            <strong>📍 Espacio:</strong> ${espacioComun}<br><br>

            <strong>👤 Nombre:</strong> ${nombre}<br><br>

            <strong>⏰ Hora inicio:</strong> ${convertirHoraAMPM(horaInicio)}<br>
            <strong>⏱ Hora final:</strong> ${convertirHoraAMPM(horaFin)}<br><br>

            <strong>📝 Observaciones:</strong><br>
            ${observaciones || '<em>Sin observaciones</em>'}
        </div>
    `,
                                html: true,
                                placement: 'top',
                                trigger: 'hover',
                            });

                        },

                        datesSet: function(info) { // la fecha al cambiar en el calendario las flechas




                        }

                    });

                // metodo para renderizar y mostrar el calendario  


                calendar.render();


                let fechaActual = new Date();

                listaReservasMesActual = reservasArrayAutorizar.filter(reserva => {

                    const fechaReserva = new Date(reserva.start + "T00:00:00");

                    return (


                        fechaReserva >= fechaActual && // fecha hoy o futura
                        fechaReserva.getMonth() === fechaActual.getMonth() &&
                        fechaReserva.getFullYear() === fechaActual.getFullYear() &&
                        reserva.extendedProps.confirmacion !== 1 // excluir confirmadas (1)
                    );
                });

                document.getElementById("notificacionesReservas").textContent = listaReservasMesActual.length;



                $('#optionsEspaciosIdFormNuevaReserva').on('change', function() {

                    idEspacio = document.getElementById("optionsEspaciosIdFormNuevaReserva")
                        .value; // obtenemos el id del espacio seleccionado 

                    let selectedId = parseInt($(this).val()); // obtenemos el index del select con el this.val()

                    let listaReservaciones = Object.values(
                        reservaciones); // la [var] reservaciones   la convertimos a una objeto lista


                    listaReservaciones = listaReservaciones
                        .filter( // filtramos las reservas por fecha y tipo de espacio seleccionado ej: fecha : 2025-03-20 , espacio : salon de eventos
                            espacio => new Date(espacio.fecha).toISOString().split("T")[0] ===
                            new Date(selectedDate).toISOString().split("T")[0] &&
                            espacio.espacio === parseInt(idEspacio));



                    listaReservaciones.sort((a,
                        b
                    ) => { // ordenamos las reservaciones de tipo salon de eventos y por fecha seleccionada del calendario

                        let horaA = new Date(`1970-01-01T${a.hora_fin}`);
                        let horaB = new Date(`1970-01-01T${b.hora_fin}`);
                        return horaA - horaB;
                    });





                    // hacemos la validacion de las reservas de que si hay alguna reserva ya hecha de algun tipo buscaremos  la ultima reserva
                    // y iniciaremos una nueva reserva con la hora en la que finaliza la ultima reserva para poder hacer validaciones de disponibilidad 

                    if (listaReservaciones.length > 0) {

                        horaUltimaReserva = listaReservaciones[listaReservaciones.length - 1]["hora_fin"];

                        let contentEspacios = @json($espacios);

                        let espacioSeleccionado = contentEspacios.find(espacio => espacio.id === selectedId);


                        document.getElementById("horaInicioFormNuevaReserva").value = listaReservaciones[
                            listaReservaciones.length - 1]["hora_fin"];

                        document.getElementById("horaInicioSpanFormNuevaReserva").innerText =
                            " ( no debe ser menor a las : " + convertirHoraAMPM(listaReservaciones[
                                listaReservaciones.length - 1]["hora_fin"]) + " )";

                        document.getElementById("horaFinFormNuevaReserva").value = espacioSeleccionado.hora_fin;

                        document.getElementById("horaFinSpanFormNuevaReserva").innerText =
                            " ( no debe ser mayor a las :  " + convertirHoraAMPM(espacioSeleccionado.hora_fin) +
                            " )";

                        document.getElementById("horasMin").innerText = "Hora min : " + espacioSeleccionado
                            .horas_min;
                        document.getElementById("horasMax").innerText = "Hora Max : " + espacioSeleccionado
                            .horas_max;



                    } else {


                        // si no se encuentra ninguna reserva del tipo seleccionado entonces usaremos para empezar la reserva con la hora de inicio que se hace la apertura del espacio

                        let contentEspacios = @json($espacios);

                        let espacioSeleccionado = contentEspacios.find(espacio => espacio.id === selectedId);

                        horaUltimaReserva = espacioSeleccionado.hora_inicio;

                        document.getElementById("horaInicioFormNuevaReserva").value = espacioSeleccionado
                            .hora_inicio;

                        document.getElementById("horaInicioSpanFormNuevaReserva").innerText =
                            " ( no debe ser menor a las : " + convertirHoraAMPM(espacioSeleccionado
                                .hora_inicio) + " )";

                        document.getElementById("horaFinFormNuevaReserva").value = espacioSeleccionado.hora_fin;

                        document.getElementById("horaFinSpanFormNuevaReserva").innerText =
                            " ( no debe ser mayor a las :  " + convertirHoraAMPM(espacioSeleccionado.hora_fin) +
                            " )";

                        document.getElementById("horasMin").innerText = "Hora min : " + espacioSeleccionado
                            .horas_min;
                        document.getElementById("horasMax").innerText = "Hora Max : " + espacioSeleccionado
                            .horas_max;


                    }







                });


                document.getElementById('btnGuardarFormNuevaReserva').addEventListener('click', GuardarReserva);


            });



            document.getElementById("btnEditarEspacio").addEventListener("submit", function(event) {



                    // Capturamos los valores de los inputs
                    let descripcion = document.getElementById("descripcion").value;
                    let minHoras = parseInt(document.getElementById("minHoras").value);
                    let maxHoras = parseInt(document.getElementById("maxHoras").value);
                    let horaInicio = document.getElementById("horaInicio").value;
                    let horaFin = document.getElementById("horaFin").value;

                    console.log("📌 Descripción:", descripcion);
                    console.log("📌 Mínimo de horas:", minHoras);
                    console.log("📌 Máximo de horas:", maxHoras);
                    console.log("📌 Hora de inicio:", horaInicio);
                    console.log("📌 Hora de fin:", horaFin);

                    // Validar que los campos no estén vacíos
                    if (!descripcion || !horaInicio || !horaFin || isNaN(minHoras) || isNaN(maxHoras)) {
                        alert("⚠️ Todos los campos son obligatorios.");
                        event.preventDefault();
                        return;
                    }

                    // Validar que el mínimo y el máximo de horas sean valores válidos
                    if (minHoras <= 0) {
                        alert("⚠️ El mínimo de horas debe ser mayor a 0.");
                        event.preventDefault();
                        return;
                    }

                    if (maxHoras <= 0) {
                        alert("⚠️ El máximo de horas debe ser mayor a 0.");
                        event.preventDefault();
                        return;
                    }

                    if (minHoras > 24) {
                        alert("⚠️ El mínimo de horas no puede ser mayor a 24.");
                        event.preventDefault();
                        return;
                    }

                    if (maxHoras > 24) {
                        alert("⚠️ El máximo de horas no puede ser mayor a 24.");
                        event.preventDefault();
                        return;
                    }

                    if (minHoras > maxHoras) {
                        alert("⚠️ El mínimo de horas no puede ser mayor que el máximo.");
                        event.preventDefault();
                        return;
                    }

                    // Convertir las horas a objetos Date para comparar correctamente
                    let horaInicioDate = new Date(`1970-01-01T${horaInicio}:00`);
                    let horaFinDate = new Date(`1970-01-01T${horaFin}:00`);

                    // Validar que la hora de inicio sea menor que la hora de fin
                    if (horaInicioDate >= horaFinDate) {
                        alert("⚠️ La hora de inicio debe ser menor que la hora de fin.");
                        event.preventDefault();
                        return;
                    }

                    // Si todo está bien, se puede enviar el formulario o proceder con la lógica deseada
                    console.log("✅ Validaciones superadas, puedes continuar con la edición.");

                }

            );

            document.getElementById("horaInicioFormCrearEspacio").addEventListener("change", function(event) {

                var horaMin = parseInt(document.getElementById("minHorasFormCrearEspacio").value);
                var horaMax = parseInt(document.getElementById("maxHorasFormCrearEspacio").value);


                if (horaMin > 24 || horaMax > 24) {


                    document.getElementById("horaInicioFormCrearEspacio").value = "";

                    document.getElementById("horaFinFormCrearEspacio").value = "";

                    alert("la cantidad de horas debe ser inferior a 24 ");

                    return;


                }


            });

            // este metodo escuha todo lo relacionado con el formulario de creacion de espacio
            document.getElementById("CrearEspacioForm").addEventListener("submit", function(event) {


                // Capturamos los valores de los inputs
                let descripcion = document.getElementById("descripcionEspacio").value.trim();
                let minHoras = parseInt(document.getElementById("minHorasFormCrearEspacio").value);
                let maxHoras = parseInt(document.getElementById("maxHorasFormCrearEspacio").value);
                let horaInicio = document.getElementById("horaInicioFormCrearEspacio").value;
                let horaFin = document.getElementById("horaFinFormCrearEspacio").value;






                // Imprimir los valores en consola
                console.log("📌 Datos del formulario:");



                console.log("Descripción:", descripcion);
                console.log("Mínimo de Horas:", minHoras);
                console.log("Máximo de Horas:", maxHoras);
                console.log("Hora de Inicio:", horaInicio);
                console.log("Hora de Fin:", horaFin);
                // Validar que los campos no estén vacíos
                if (!descripcion || !horaInicio || !horaFin) {
                    alert("⚠️ Todos los campos son obligatorios.");
                    event.preventDefault();
                    return;
                }
                // Validar que el maximo de horas sea igual o menor a 24
                if (minHoras == 0) {
                    alert("⚠️ El minimo  de horas no puede ser 0");
                    event.preventDefault();
                    return;
                }
                // Validar que el maximo de horas sea igual o menor a 24
                if (maxHoras == 0) {
                    alert("⚠️ El maximo  de horas no puede ser 0");
                    event.preventDefault();
                    return;
                }
                // Validar que el mínimo de horas no sea mayor que el máximo
                if (minHoras > 24) {
                    alert("⚠️ El mínimo de horas no puede ser mayor que el máximo.");
                    event.preventDefault();
                    return;
                }
  
                // Validar que el maximo de horas sea igual o menor a 24
                if (maxHoras > 24) {
                    alert("⚠️ El maximo  de horas no puede ser mayor a 24");
                    event.preventDefault();

                    return;
                }


                // Validar que el mínimo de horas no sea mayor que el máximo
                if (minHoras > maxHoras) {
                    alert("⚠️ El mínimo de horas no puede ser mayor que el máximo.");
                    event.preventDefault();
                    return;
                }

                // Validar que la hora de inicio sea menor que la hora de fin
                if (horaInicio >= horaFin) {
                    alert("⚠️ La hora de inicio debe ser menor que la hora de fin.");
                    event.preventDefault();
                    return;
                }

                // Si todo está bien, se envía el formulario









            });

            //--------------------------------------------   FUNCTIONS  --------------------------------------------

            //                          logica espacios
            function verEspacios() {
                document.getElementById("vistaEspacios").hidden = false;
                document.getElementById("verCalendar").style.display = "none";


            }

            function editarEspacio(id, descrip, minHor, maxHor, horaIni, horaF) {
                $('#btnEditarEspacio').modal('show');

                document.getElementById("descripcion").value = descrip;
                document.getElementById("minHoras").value = minHor;
                document.getElementById("maxHoras").value = maxHor;
                document.getElementById("horaInicio").value = horaIni;
                document.getElementById("horaFin").value = horaF;
                document.getElementById("idEditar").value = id;


            }

            function confirmarEliminacionEspacio(formId, idEspacio) {


                let form = document.getElementById(formId);
                form.querySelector('input[name="idEliminar"]').value = idEspacio;

                if (confirm(`¿Deseas eliminar el espacio con ID ${idEspacio}?`)) {
                    form.submit(); // Envía el formulario específico
                } else {
                    alert("Eliminación cancelada.");
                }
            }



            //                         logica reservaciones
            function verReservas() {

                document.getElementById("vistaEspacios").hidden = true;
                document.getElementById("verCalendar").style.display = "block";


            }

            function GuardarReserva() {

                let miVariable = @json($espacios);
                let formData = new FormData();

                // Obtener valores de los inputs
                let title = document.getElementById("optionsEspaciosIdFormNuevaReserva").options[
                    document.getElementById("optionsEspaciosIdFormNuevaReserva").selectedIndex
                ].text;

                let idEspacio = document.getElementById("optionsEspaciosIdFormNuevaReserva").value;
                let residenteId = document.getElementById("optionsResidenteIdFormNuevaReserva").value;
                let horaInicioSeleccion = document.getElementById("horaInicioFormNuevaReserva").value;
                let horFinSeleccion = document.getElementById("horaFinFormNuevaReserva").value;
                let observaciones = document.getElementById("observacionesFormNuevaReserva").value;

                // Validar campos vacíos
                if (!title || !idEspacio || !residenteId || !horaInicioSeleccion || !horFinSeleccion || !
                    observaciones) {
                    alert("⚠️ Todos los campos son obligatorios.");
                    return; // Salir de la función si falta algún dato
                }

                // Buscar datos del espacio seleccionado
                let espacioSeleccionado = miVariable.find(espacio => espacio.id === parseInt(idEspacio));

                if (!espacioSeleccionado) {
                    alert("⚠️ El espacio seleccionado no es válido.");
                    return;
                }

                let horaInicioEspacio = horaUltimaReserva; // Última reserva del mismo espacio
                let horaFinEspacio = espacioSeleccionado.hora_fin;
                let horasMinEspacio = espacioSeleccionado.horas_min;
                let horasMaxEspacio = espacioSeleccionado.horas_max;

                // Convertir a objetos de fecha
                const startTime = new Date(`1970-01-01T${horaInicioSeleccion}`);
                const endTime = new Date(`1970-01-01T${horFinSeleccion}`);
                const startTimeEspacio = new Date(`1970-01-01T${horaInicioEspacio}`);
                const endTimeEspacio = new Date(`1970-01-01T${horaFinEspacio}`);

                // Calcular diferencia en horas
                const diffInHours = (endTime - startTime) / (1000 * 60 * 60);

                // Validaciones de horarios
                if (startTime < startTimeEspacio) {
                    alert("⚠️ La hora seleccionada no puede ser menor a la hora de inicio del espacio.");
                    return;
                }

                if (endTime > endTimeEspacio) {
                    alert("⚠️ La hora seleccionada no puede ser mayor a la hora final del espacio.");
                    return;
                }

                if (startTime >= endTime) {
                    alert("⚠️ La hora inicial debe ser menor que la hora final.");
                    return;
                }

                if (diffInHours < horasMinEspacio) {
                    alert(`⚠️ Se debe reservar el espacio por al menos ${horasMinEspacio} horas.`);
                    return;
                }

                if (diffInHours > horasMaxEspacio) {
                    alert(`⚠️ Se debe reservar el espacio por un máximo de ${horasMaxEspacio} horas.`);
                    return;
                }
                $('#loadingModalCreandoReserva').modal('show');
                // Si todas las validaciones pasan, proceder con la reserva
                document.getElementById("formCrearReserva").hidden = true;
                document.getElementById("circular").hidden = false;

                // Agregar datos al formData
                formData.append("fecha", selectedDate);
                formData.append("espacio", idEspacio);
                formData.append("residente", residenteId);
                formData.append("hora_inicio", horaInicioSeleccion);
                formData.append("hora_fin", horFinSeleccion);
                formData.append("observaciones", observaciones);


                $('#añadirReserva').modal('hide');

                // Enviar datos al servidor
                fetch("reserva_espacios/crear_reserva", {
                        method: "POST",
                        body: formData
                    })
                    .then(response => response.json())
                    .then((data) => {


                        if (data.status === "success") {


                            // Agregar evento al calendario
                            // calendar.addEvent({
                            //     title: title,
                            //     start: selectedDate,
                            //     allDay: true,
                            //     color: 'invisible',
                            //     extendedProps: {

                            //         horaInicio: horaInicioSeleccion,
                            //         horaFin: horFinSeleccion,
                            //         observaciones: observaciones,

                            //     }
                            // });
             

                            document.getElementById("formCrearReserva").hidden = false;
                            document.getElementById("circular").hidden = true;
                            location.reload(); // 🔄 Recargar la página

                        } else {
  

                            alert("no puedes hacer esta reserva");

                            location.reload(); // 🔄 Recargar la página

                        }


                    })
                    .catch(error => console.error("Error:", error));

            }

            function verReservasPendientesPorAutorizar() {


                let fechaActual = new Date();

                fechaActual.setHours(0, 0, 0, 0); // Asegura comparar solo la fecha sin hora

                listaReservasMesActual = reservasArrayAutorizar.filter(reserva => {

                    const fechaReserva = new Date(reserva.start + "T00:00:00");

                    return (


                        fechaReserva >= fechaActual && // fecha hoy o futura
                        fechaReserva.getMonth() === fechaActual.getMonth() &&
                        fechaReserva.getFullYear() === fechaActual.getFullYear() &&
                        reserva.extendedProps.confirmacion !== 1 // excluir confirmadas (1)
                    );
                });

                document.getElementById("notificacionesReservas").textContent = listaReservasMesActual.length;
                // Seleccionar el contenedor
                const columna = document.getElementById("pendienteColumna");
                columna.innerHTML = ``; // Esto elimina todos los hijos
                if (listaReservasMesActual.length == 0) {
                    columna.innerHTML = `
  <div style="padding: 20px; text-align: center; color: #555;">
    <i class="fas fa-check-circle" style="font-size: 24px; color: #28a745;"></i>
    <p style="margin-top: 10px; font-size: 16px;">No tienes reservas por aprobar</p>
  </div>
`;
                }
                listaReservasMesActual.forEach((reserva) => {

                    const div = document.createElement("div");
                    div.style.border = "1px solid black";
                    div.style.borderRadius = "8px";
                    div.style.width = "100%";

                    div.style.padding = "10px";
                    div.style.marginBottom = "10px";
                    div.style.boxSizing = "border-box";

                    // ROW 1: Título y hora
                    const row1 = document.createElement("div");
                    row1.style.display = "flex";
                    row1.style.justifyContent = "space-between";
                    row1.style.alignItems = "left";
                    row1.style.width = "100%";

                    const titulo = document.createElement("div");
                    titulo.textContent = reserva.title;
                    titulo.style.fontSize = "14px";
                    titulo.style.fontWeight = "500";

                    const hora = document.createElement("div");
                    hora.textContent = convertirHoraAMPM(reserva.extendedProps.horaInicio);
                    hora.style.fontSize = "14px";
                    hora.style.fontWeight = "500";

                    row1.appendChild(titulo);
                    row1.appendChild(hora);

                    // ROW 2:
                    const row2 = document.createElement("div");
                    row2.style.display = "flex";
                    row2.style.justifyContent = "space-between";
                    row2.style.alignItems = "left";
                    row2.style.width = "100%";

                    const nombre = document.createElement("div");
                    nombre.textContent = reserva.extendedProps.nombre ||
                        "Nombre no disponible";
                    nombre.style.fontWeight = "500";

                    nombre.style.fontSize = "14px";
                    nombre.style.overflow = "hidden";
                    nombre.style.whiteSpace = "nowrap";
                    nombre.style.textOverflow = "ellipsis";


                    const horaFin = document.createElement("div");
                    horaFin.textContent = convertirHoraAMPM(reserva.extendedProps.horaFin);

                    horaFin.style.fontSize = "14px";
                    horaFin.style.fontWeight = "500";

                    row2.appendChild(nombre);
                    row2.appendChild(horaFin);

                    // ROW fecha:
                    const rowFecha = document.createElement("div");
                    rowFecha.style.display = "flex";
                    rowFecha.style.justifyContent = "space-between";
                    rowFecha.style.alignItems =
                        "center"; // Mejor para alinear verticalmente
                    rowFecha.style.width = "100%";

                    const fechaTitle = document.createElement("div");
                    fechaTitle.textContent = "Fecha reserva";
                    fechaTitle.style.fontSize = "14px";
                    fechaTitle.style.fontWeight = "500";

                    const fecha = document.createElement("div");
                    fecha.textContent = formatoFechaEnLetras(reserva.start);
                    fecha.style.fontSize = "14px";
                    fecha.style.fontWeight = "500";
                    fecha.style.overflow = "hidden";
                    fecha.style.whiteSpace = "nowrap";
                    fecha.style.textOverflow = "ellipsis";

                    // Agregar elementos a la fila
                    rowFecha.appendChild(fechaTitle);
                    rowFecha.appendChild(fecha);
                    // ROW 3: Estado y botones
                    const row3 = document.createElement("div");
                    row3.style.display = "flex";
                    row3.style.justifyContent = "space-between";
                    row3.style.alignItems = "center";

                    // Sub-row que contiene los botones
                    const subRow3 = document.createElement("div");
                    subRow3.style.display = "flex";
                    subRow3.style.gap = "10px"; // espacio entre los botones
                    subRow3.style.alignItems = "center";

                    // Estado visual
                    const estado = document.createElement("div");
                    estado.textContent = "pendiente";
                    estado.style.backgroundColor = "#FFA94D";
                    estado.style.color = "white";
                    estado.style.fontSize = "12px";
                    estado.style.padding = "3px 12px";
                    estado.style.borderRadius = "10px";
                    estado.style.width = "80px";
                    estado.style.textAlign = "center";

                    // Botón Autorizar
                    const boton = document.createElement("div");
                    boton.textContent = "Autorizar";
                    boton.style.padding = "6px 12px";
                    boton.style.backgroundColor = "#1FA516";
                    boton.style.color = "white";
                    boton.style.borderRadius = "8px";
                    boton.style.cursor = "pointer";
                    boton.style.textAlign = "center";

                    // Botón No Autorizar
                    const botonNoAutorizar = document.createElement("div");
                    botonNoAutorizar.textContent = "No autorizar";
                    botonNoAutorizar.style.padding = "6px 12px";
                    botonNoAutorizar.style.backgroundColor = "#FF0000";
                    botonNoAutorizar.style.color = "white";
                    botonNoAutorizar.style.borderRadius = "8px";
                    botonNoAutorizar.style.cursor = "pointer";
                    botonNoAutorizar.style.textAlign = "center";

                    // Añadir botones al sub-row
                    subRow3.appendChild(boton);
                    subRow3.appendChild(botonNoAutorizar);

                    // Añadir estado y sub-row al row principal
                    row3.appendChild(estado);
                    row3.appendChild(subRow3);
                    // Ensamblar todo
                    div.appendChild(row1);
                    div.appendChild(row2);
                    div.appendChild(rowFecha);

                    div.appendChild(row3);

                    columna.appendChild(div);


                    const cardReservaHtml = ` 
      <div style="border: 1px solid #ccc; border-radius: 10px; padding: 15px; text-align: left; margin-top: 10px;">

        <div><strong>${reserva.title}</strong> <span style="float:right;">${convertirHoraAMPM(reserva.extendedProps.horaInicio)}</span></div>
        <div>${reserva.extendedProps.nombre}<span style="float:right;">${convertirHoraAMPM(reserva.extendedProps.horaFin)}</span></div>
        <div style="margin-top:5px;">Fecha reserva <span style="float:right;">${reserva.start}</span></div>

      </div>`;



                    boton.onclick = () => {
                        $('#modalAutorizarReservas').modal('hide');


                        promptsAutorizarReserva(
                            reserva.extendedProps.correo,

                            reserva.extendedProps.nombre,
                            reserva.start,
                            convertirHoraAMPM(reserva.extendedProps.horaInicio),
                            convertirHoraAMPM(reserva.extendedProps.horaFin),
                            reserva.title,
                            1,
                            reserva.id,
                            cardReservaHtml,


                        );

                    };




                    botonNoAutorizar.onclick = () => {
                        $('#modalAutorizarReservas').modal('hide');


                        promptsNoAutorizarReserva(
                            reserva.extendedProps.correo,
                            reserva.extendedProps.nombre,
                            reserva.start,
                            convertirHoraAMPM(reserva.extendedProps.horaInicio),
                            convertirHoraAMPM(reserva.extendedProps.horaFin),
                            reserva.title,
                            2,
                            reserva.id,
                            cardReservaHtml,


                        );

                        // promptsDetallesDeReserva( 
                        //     "rgmr293@gmail.com",
                        //     reserva.extendedProps.nombre,
                        //     reserva.start,
                        //     convertirHoraAMPM(reserva.extendedProps.horaInicio),
                        //     convertirHoraAMPM(reserva.extendedProps.horaFin),
                        //     reserva.title,
                        //     2,
                        //     reserva.id,
                        //     cardReservaHtml,)

                    };









                });






                $('#modalAutorizarReservas').modal('show');
            }

            function verDetallesDeReserva(idReserva, espacio, cliente, horaInicio, HoraFin, observations) {


                document.getElementById("alertTitle").innerText = espacio;
                document.getElementById("cliente").innerText = cliente;
                document.getElementById("inicio").innerText = horaInicio;
                document.getElementById("fin").innerText = HoraFin;
                document.getElementById("alertaModalLabel").innerText = "Detalles de reserva N° " + idReserva;


                document.getElementById("alertObservations").innerText = observations;
                var myModal = new bootstrap.Modal(document.getElementById('alertaModal'));
                myModal.show();
            }




            //                         utilidades
            function convertirHoraAMPM(hora) {
                let [horaNum, minutos] = hora.split(":").map(Number);
                let ampm = horaNum >= 12 ? "PM" : "AM";
                horaNum = horaNum % 12 || 12; // Convierte 0 a 12 para el formato AM/PM
                return `${horaNum}:${minutos.toString().padStart(2, "0")} ${ampm}`;
            }

            function formatoFechaEnLetras(fecha) {
                const meses = [
                    "enero", "febrero", "marzo", "abril", "mayo", "junio",
                    "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"
                ];

                let dateObj = new Date(fecha);
                let dia = dateObj.getDate() + 1;
                let mes = meses[dateObj.getMonth()];
                let año = dateObj.getFullYear();

                return `${mes} ${dia} del ${año}`;
            }
            // validamos la entrada de numeros para q no sea mayor a dos digitos y bloque otras entradas de teclado
            function validarNumero(input) {
                input.value = input.value.replace(/\D/g, ''); // Elimina todo lo que no sea número
                if (input.value.length > 2) {
                    input.value = input.value.slice(0, 2); // Limita a 2 dígitos
                }


            }




            //                         prompts

            function promptsAutorizarReserva(correo, nombre, fecha, horaInicio, horaFin, espacio, confirmacion, idReserva,
                cardReservaHtml, notaAutorizacion) {
                Swal.fire({
                    title: '¿Deseas autorizar esta reserva?',
                    html: cardReservaHtml,
                    text: 'Puedes dejar una nota de la autorización:',
                    input: 'textarea',
                    inputPlaceholder: 'Escribe aquí tu observación o nota...',
                    showCancelButton: true,
                    confirmButtonText: 'Autorizar',
                    cancelButtonText: 'Cancelar',
                    icon: 'question'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const nota = result.value ? result.value : '(Sin nota)';

                        Swal.fire({
                            title: 'Procesando...',
                            text: 'Autorizando la reserva, por favor espera.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
          
                        let formData = new FormData();
                        formData.append("correo", correo);
                        formData.append("nombre", nombre);
                        formData.append("fecha", fecha);
                        formData.append("horaInicio", horaInicio);
                        formData.append("horaFin", horaFin);
                        formData.append("espacio", espacio);
                        formData.append("confirmacion", confirmacion);
                        formData.append("idReserva", idReserva);
                        formData.append("notaAutorizacion", nota);
  
                        fetch("/cliente/reserva_espacios/autorizar_reserva", {
                                method: "POST",
                                body: formData
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Error en la solicitud: ' + response.status);
                                }
                                return response.json();
                            })
                            .then((data) => {
                                console.log("Respuesta backend:", data);

                                if (data.status === "success") {
                                    Swal.fire({
                                        title: 'Reserva Autorizada ✅',
                                        text: 'Nota registrada: ' + nota,
                                        icon: 'success'
                                    }).then(() => {
                                        location.reload(); // 🔄 Recarga la página cuando se cierra el modal
                                    });


                                } else {
                                    Swal.fire({
                                        title: 'No se pudo autorizar ❌',
                                        text: data.message || 'No puedes hacer esta reserva.',
                                        icon: 'error'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire({
                                    title: 'Error ❌',
                                    text: 'No se pudo autorizar la reserva. Intenta de nuevo.',
                                    icon: 'error'
                                }).then(() => {
                                    location.reload(); // 🔄 Recarga la página cuando se cierra el modal
                                });
                            });
                    }
                });
            }

            function promptsNoAutorizarReserva(correo, nombre, fecha, horaInicio, horaFin, espacio, confirmacion, idReserva,
                cardReservaHtml, notaAutorizacion) {
                Swal.fire({
                    title: '¿Deseas NO autorizar esta reserva?',
                    html: cardReservaHtml,
                    input: 'textarea',
                    inputPlaceholder: 'Escribe aquí el motivo de la no autorización...',
                    showCancelButton: true,
                    confirmButtonText: 'No Autorizar',
                    cancelButtonText: 'Cancelar',
                    icon: 'warning'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const nota = result.value ? result.value : '(Sin nota)';

                        Swal.fire({
                            title: 'Procesando...',
                            text: 'Rechazando la reserva, por favor espera.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        let formData = new FormData();
                        formData.append("correo", "rgmr293@gmail.com");
                        formData.append("nombre", nombre);
                        formData.append("fecha", fecha);
                        formData.append("horaInicio", horaInicio);
                        formData.append("horaFin", horaFin);
                        formData.append("espacio", espacio);
                        formData.append("confirmacion", confirmacion);
                        formData.append("idReserva", idReserva);
                        formData.append("notaAutorizacion", nota);
                        formData.append("estado", 0);

  
                        fetch("/cliente/reserva_espacios/no_autorizar_reserva", {
                                method: "POST",
                                body: formData
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Error en la solicitud: ' + response.status);
                                }
                                return response.json();
                            })
                            .then((data) => {
                                console.log("Respuesta backend:", data);

                                if (data.status === "success") {
                                    Swal.fire({
                                        title: 'Reserva No Autorizada ❌',
                                        text: 'Nota registrada: ' + nota,
                                        icon: 'info'
                                    }).then(() => {
                                        location.reload(); // 🔄 Recarga la página cuando se cierra el modal
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'No se pudo autorizar ❌',
                                        text: data.message || 'No puedes hacer esta reserva.',
                                        icon: 'error'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire({
                                    title: 'Error ❌',
                                    text: 'No se pudo autorizar la reserva. Intenta de nuevo.',
                                    icon: 'error'
                                });
                            });
                    }
                });
            }

            function promptsDetallesReserva(
                correo,
                nombre,
                fecha,
                horaInicio,
                horaFin,
                espacio,
                confirmacion,
                idReserva,
                cardReservaHtml,
                notaAutorizacion
            ) {


                const fechaEvento = new Date(fecha);
                const hoy = new Date(); // Fecha actual

                // Limpiar horas si solo te importa la comparación de fecha (no hora)
                fechaEvento.setHours(0, 0, 0, 0);
                hoy.setHours(0, 0, 0, 0);


                Swal.fire({
                    title: 'Detalles de reserva',
                    html: cardReservaHtml,
                    showCancelButton: true,
                    showConfirmButton: fechaEvento < hoy ? false : true,

                    confirmButtonText: 'Cancelar Reserva',
                    cancelButtonText: 'Volver',
                    icon: 'info',
                    confirmButtonColor: '#D82929FF'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Mostrar un input para escribir la nota de cancelación
                        Swal.fire({
                            title: '¿Deseas cancelar esta reserva?',
                            input: 'textarea',
                            inputPlaceholder: 'Motivo de la cancelación (opcional)...',
                            showCancelButton: true,
                            confirmButtonText: 'Confirmar cancelación',
                            cancelButtonText: 'Volver',
                            icon: 'warning',
                            confirmButtonColor: '#D82929FF'
                        }).then((res) => {
                            if (res.isConfirmed) {
                                const nota = res.value ? res.value : '(Sin motivo)';

                                Swal.fire({
                                    title: 'Procesando...',
                                    text: 'Cancelando la reserva, por favor espera.',
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });
                                let formData = new FormData();
                                formData.append("correo", "rgmr293@gmail.com");
                                formData.append("nombre", nombre);
                                formData.append("fecha", formatoFechaEnLetras(fecha), );
                                formData.append("horaInicio", horaInicio);
                                formData.append("horaFin", horaFin);
                                formData.append("espacio", espacio);
                                formData.append("confirmacion", confirmacion);
                                formData.append("idReserva", idReserva);
                                formData.append("notaAutorizacion", nota);
                                formData.append("estado", 0);

                                fetch("/cliente/reserva_espacios/cancelar_reserva", {
                                        method: "POST",
                                        body: formData
                                    })
                                    .then(response => {
                                        if (!response.ok) {
                                            throw new Error('Error en la solicitud: ' + response.status);
                                        }
                                        return response.json();
                                    })
                                    .then((data) => {
                                        if (data.status === "success") {
                                            Swal.fire({
                                                title: 'Reserva cancelada ✅',
                                                text: 'Motivo: ' + nota,
                                                icon: 'success'
                                            }).then(() => {
                                                location
                                            .reload(); // 🔄 Recarga la página cuando se cierra el modal
                                            });
                                        } else {
                                            Swal.fire({
                                                title: 'No se pudo cancelar ❌',
                                                text: data.message ||
                                                    'Hubo un problema al cancelar la reserva.',
                                                icon: 'error'
                                            });
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        Swal.fire({
                                            title: 'Error ❌',
                                            text: 'No se pudo completar la cancelación.',
                                            icon: 'error'
                                        });
                                    });
                            }
                        });
                    }
                });
            }

          
      
      </script>
        <!--  FINALIZA SCRIPTS    PARA LA VIEW  [VER ESPACION COMUNES]  -->


        <!-- stilo para circular progres  -->

        <style>
            .stack-container {
                position: relative;
                display: inline-block;
            }

            .notification-badge {
                position: absolute;
                top: -6px;
                right: -6px;
                background-color: red;
                color: white;
                font-size: 10px;
                font-weight: bold;
                padding: 2px 5px;
                border-radius: 50%;
                z-index: 10;
                box-shadow: 0 0 0 2px white;
            }

            .bell-icon {
                font-size: 20px;
                color: white;
            }

            .btn-notification {
                display: flex;
                align-items: center;
                justify-content: center;
                background-color: #198754;
                border: none;
                padding: 8px 12px;
                border-radius: 6px;
                cursor: pointer;
            }








            .loader-container {
                display: grid;
                place-items: center;
                /* Centra tanto vertical como horizontalmente */
                height: 40vh;
            }



            .modal-backdrop {
                backdrop-filter: blur(20px);
                /* Mayor desenfoque */
                background-color: rgba(0, 0, 0, 0.3);
                /* Oscurece un poco más el fondo */
            }


            .loader {

                border: 16px solid #f3f3f3;
                /* Light grey */
                border-top: 16px solid #3498db;
                /* Blue */
                border-radius: 50%;
                width: 120px;

                height: 120px;
                animation: spin 2s linear infinite;
            }

            @keyframes spin {
                0% {
                    transform: rotate(0deg);
                }

                100% {
                    transform: rotate(360deg);
                }
            }
        </style>
        <!-- fin stilo para circular progres  -->



        <!-- stilo para swich boton -->
        <style>
            #modalAutorizarReservas .modal-dialog {
                max-width: 60% !important;
            }

            .switch {
                position: relative;
                display: inline-block;
                width: 50px;
                height: 24px;
            }

            .switch input {
                opacity: 0;
                width: 0;
                height: 0;
            }

            .slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #ccc;
                border-radius: 24px;
                transition: 0.4s;
            }

            .slider:before {
                position: absolute;
                content: "";
                height: 18px;
                width: 18px;
                left: 4px;
                bottom: 3px;
                background-color: white;
                transition: 0.4s;
                border-radius: 50%;
            }

            input:checked+.slider {
                background-color: #3CE73CFF;
                /* Rojo */
            }

            input:checked+.slider:before {
                transform: translateX(26px);
            }
        </style>

        <!-- final stilo para swich boton -->




    @stop
