@extends('l')

@section('2')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    </link>

    <style>
        .imagen-Seleccionada {
            /* estilo de la imagen que seleccionamos en la parte superior derecha  */
            display: flex;
            height: 100%;
            border-radius: 8px;
            padding: 5%;
            background-position: center;

        }




        .file-list {
            /*
                                                                                                 Contenedor con bordes redondeados
                                                                                    Todo el contenido está dentro de una caja de fondo rojo con bordes redondeados. */
            width: 100%;
            background-color: #e5e0e0;
            padding: 10px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;


        }

        .file-left {
            /*🖼️ Miniatura de imagen
                                                                                                      A la izquierda, se muestra una vista previa pequeña del archivo de imagen

                                                                                                */

            display: flex;
            align-items: center;
            gap: 10px;
            flex-grow: 1;
        }

        .file-icon {
            /* 📝 Nombre del archivo
                                                                                                    Junto a la miniatura aparece el nombre del archivo: comunicado_1745940112.png.  */
            width: 40px;
            height: 40px;
            background-color: white;
            border-radius: 5px;
            background-size: cover;
            background-position: center;
        }

        .fecha-btn-list {
            border-radius: 8px;
            right: 8px;
            background-color: rgba(185, 183, 183, 0.8);
            color: white;
            padding: 6px 10px;
            cursor: pointer;
        }

        .link-btn-list {
            margin-right: 4px;
            border-radius: 8px;
            right: 8px;
            background-color: rgba(88, 141, 247, 0.8);
            color: white;
            padding: 6px 10px;
            cursor: pointer;
        }

        .edit-btn-list {
            margin-left: 4px;
            border-radius: 8px;
            right: 8px;
            background-color: rgba(88, 141, 247, 0.8);
            color: white;
            padding: 6px 10px;
            cursor: pointer;
        }


        .delete-btn {
            margin-left: 4px;
            border-radius: 8px;
            right: 8px;
            background-color: rgba(227, 76, 76, 0.8);
            color: white;
            padding: 6px 10px;
            cursor: pointer;
        }






        .fecha-btn-imagen {
            border-radius: 8px;
            font-size: 18px;
            bottom: 100px;
            right: 8px;
            background-color: rgba(185, 183, 183, 0.8);
            color: white;
            padding: 6px 10px;
            position: absolute;
            cursor: pointer;
        }

        .link-btn-imagen {
            border-radius: 8px;
            font-size: 18px;
            bottom: 55px;
            right: 8px;
            background-color: rgba(0, 166, 255, 0.8);
            color: white;
            padding: 6px 10px;
            position: absolute;
            cursor: pointer;
        }

        .delete-btn-imagen {
            border-radius: 8px;
            font-size: 18px;
            bottom: 8px;
            right: 8px;
            background-color: rgba(255, 0, 0, 0.8);
            color: white;
            padding: 6px 20px;
            position: absolute;
            cursor: pointer;
        }



        .contenedor-principal {
            margin-top: 10px;

            margin-left: 10px;
            gap: 10px;
            /* Espacio entre los dos contenedores */
            position: relative;
            display: flex;
            width: 100%;
            height: 350px;
            margin-bottom: 10px;
            /* o el alto que desees */
        }


        .container-top-rigth {
            display: flex;
            flex-direction: column;
            /* ← aquí está la clave */
            flex: 1;
            gap: 10px;
            cursor: pointer;
            width: 100%;
            height: 350px;
            overflow: hidden;
            border-radius: 8px;
        }

        .container-Seleccionar-image {
            display: flex;
            /* ¡Esto es esencial! */
            flex: 3;
            position: relative;

            border: 2px dashed black;
            margin: 10px;
            overflow: hidden;
            justify-content: center;
            /* centra horizontalmente */
            align-items: center;
            /* centra verticalmente */

            border-radius: 8px;
        }

        .button-guardar {
            flex: 1;
            margin: 10px;

            overflow: hidden;
            border-radius: 8px;
            background: #375CA6;
        }

        .carousel-container {
            flex: 3;
            position: relative;
            width: 100%;
            height: 350px;
            overflow: hidden;
            margin-bottom: 10px;
            border-radius: 8px;
            background: #eee;
        }

        .carousel-track {
            /* esta es la animacion de transicion */
            display: flex;
            transition: transform 0.5s ease;
            height: 100%;
            width: 100%;
        }

        .carousel-track img {
            width: 100%;
            height: 100%;
            /* object-fit: cover; */
            border: 2px solid #ccc;
            border-radius: 10px;
            flex-shrink: 0;
        }

        .carousel-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            z-index: 10;
        }

        .left {
            left: 10px;
        }

        .right {
            right: 10px;
        }








        .modal-dialog input {
            width: 100%;
            margin: 8px 0;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
    </style>




    <div class="contenedor-principal">


        <div class="carousel-container">
            <div class="carousel-track img" id="carouselTrack">


                <!-- Aquí se agregarán dinámicamente las imágenes -->
            </div>
            <button class="carousel-btn left" id="prevBtn">⬅️</button>
            <button class="carousel-btn right" id="nextBtn">➡️</button>
        </div> {{-- container-carrusel --}}



        <div class="container-top-rigth">




            <div class="container-Seleccionar-image" id="container-Seleccionar-image">

                <div class="delete-btn-imagen" id="eliminarImagenSeleccionada" style="display:none"> Eliminar 🗑️ </div>

                <div class="link-btn-imagen" id="linkBtnImagen" style="display:none"> link accion <i
                        class="fa-solid fa-link " style="padding: 1px "></i></div>

                <div class="fecha-btn-imagen" id="fechaCaducidadBtnImagen" style="display:none"> Caducidad <i
                        class="fa-solid fa-calendar-days" style="padding: 4px"> </i></div>

                <i id="openSelectorImages" style="font-size: 48px;">🖼️ +</i>
                <div class="imagen-Seleccionada" id="imagenSeleccionada">

                </div>




                <input type="file" name="img" id="imageUpload" style="display:none" accept="image/*">

                </form>




            </div>{{-- container-Seleccionar-image --}}

            <div class="button-guardar" id="guardarButtom"
                style="flex: 1; display: flex; align-items: center; justify-content: center;">

                <label style="color: white;" for="w3review">guardar</label>
            </div>
 

        </div> {{-- container-top-rigth --}}




    </div> {{-- contenedor-principal --}}






    <div id="fileListContainer">
        <!-- Aquí se agregan dinámicamente las imágenes -->
    </div>





    <!-- Modal -->
    <div class="modal fade" id="eliminarComunicadoModal" tabindex="-1" role="dialog" aria-labelledby="fechaModalLabel">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document"> <!-- Tamaño pequeño + centrado -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">¿Deseas eliminar este comunicado?</h5>

                </div>          

                <p style="margin: 5px">   Esta acción no se puede deshacer.</p>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" onclick="confirmarEliminacionComunicado()">Eliminar</button>
                </div>
            </div>
        </div>
    </div>













    <!-- Modal -->
    <div class="modal fade" id="linkRedireccionModal" tabindex="-1" role="dialog" aria-labelledby="linkModalLabel">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document"> <!-- Tamaño pequeño + centrado -->
            <div class="modal-content">

                <label for="linkInput" style="padding: 10px">Link de redirección:</label>
                <div style="padding: 5px">
                    <input type="url" id="linkInput" class="form-control" placeholder="https://ejemplo.com"
                        style="padding: 10px">
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarLink()">Guardar</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="fechaCaducidadModal" tabindex="-1" role="dialog" aria-labelledby="fechaModalLabel">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document"> <!-- Tamaño pequeño + centrado -->
            <div class="modal-content">


                <label for="fechaInput" style="padding: 10px">Fecha de caducidad:</label>
                <div style="padding: 5px"> <input type="date" id="fechaInput" style="padding: 10px"></div>


                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarFecha()">Guardar</button>
                </div>
            </div>
        </div>
    </div>



    <!-- Modal -->
    <div class="modal fade" id="editarImagen" tabindex="-1" role="dialog" aria-labelledby="fechaModalLabel">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document"> <!-- Tamaño pequeño + centrado -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar campos</h5>

                </div>

                <label for="fechaInput" style="padding: 10px">Fecha de caducidad:</label>
                <div style="padding: 5px"> <input type="date" id="fechaInputEdit" style="padding: 10px"></div>

                <label for="linkInput" style="padding: 10px">Link de redirección:</label>
                <div style="padding: 5px">
                    <input type="url" id="linkInputEdit" class="form-control" placeholder="https://ejemplo.com"
                        style="padding: 10px">
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarEdicion()">Guardar</button>
                </div>
            </div>
        </div>
    </div>



    <!-- Modal solo con spinner -->
    <div class="modal fade" id="spinnerModal" tabindex="-1" role="dialog" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content" style="background: transparent; border: none; box-shadow: none;">
                <div
                    style="
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: auto;">
                </div>
            </div>
        </div>
    </div>

    <!-- Animación del spinner -->
    <style>
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>


<script>
    // Declaración de variables /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    let comunicados = @json($comunicados); // Lista de comunicados traídos de la base de datos
    let images = [];
    let idImagen = "";

    const track = document.getElementById('carouselTrack'); // Contenedor del carrusel de imágenes
    const listImages = document.getElementById('fileListContainer'); // Contenedor de la lista de imágenes
    let currentIndex = 0; // Índice actual de la imagen en el carrusel
    const uploadInput = document.getElementById('imageUpload'); // Input para subir imágenes
    const guardarImagenBtn = document.getElementById('guardarButtom'); // Botón de guardar
    const openSelectorImages = document.getElementById('openSelectorImages'); // Botón para abrir el selector de imágenes
    const linkBtnImagen = document.getElementById('linkBtnImagen'); // Botón para añadir un enlace a la imagen
    const fechaCaducidadBtnImagen = document.getElementById('fechaCaducidadBtnImagen'); // Botón para añadir una fecha de caducidad a la imagen
    const botonEliminarImagenSeleccionada = document.getElementById('eliminarImagenSeleccionada'); // Botón para eliminar la imagen seleccionada
    const imagenSeleccionada = document.getElementById('imagenSeleccionada'); // Contenedor de la imagen seleccionada
    let selectedFile = null; // Archivo de imagen seleccionado

    // Cargar inicialmente las imágenes
    updateCarouselImages();

    // Listeners para los modales /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    linkBtnImagen.addEventListener('click', () => {
        $('#linkRedireccionModal').modal('show');
    });

    fechaCaducidadBtnImagen.addEventListener('click', () => {
        $('#fechaCaducidadModal').modal('show');
    });

    // Evento para eliminar la imagen seleccionada
    botonEliminarImagenSeleccionada.addEventListener('click', () => {
        selectedFile = null;
        document.getElementById('imageUpload').value = '';
        imagenSeleccionada.innerHTML = ''; // Limpiar el contenido del div
        imagenSeleccionada.className = 'imagen-Seleccionada';
        document.getElementById('eliminarImagenSeleccionada').style.display = "none";
        document.getElementById("linkInput").value = "";
        document.getElementById("fechaInput").value = "";
        document.getElementById('openSelectorImages').style.display = "block";
        images.splice(images.length - 1, 1);
        updateCarouselImages();
        currentIndex = 0;
        updateCarousel();
    });

    // Botones de navegación del carrusel
    document.getElementById('prevBtn').addEventListener('click', () => {
        if (currentIndex > 0) {
            currentIndex--;
            updateCarousel();
        }
    });

    document.getElementById('nextBtn').addEventListener('click', () => {
        if (currentIndex < images.length - 1) {
            currentIndex++;
            updateCarousel();
        }
    });

    // Ajuste automático en resize
    window.addEventListener('resize', updateCarousel);

    // Evento al hacer click en "Guardar"
    guardarImagenBtn.addEventListener('click', () => {
        if (selectedFile) {
            const confirmar = confirm(`¿Desea subir la imagen "${selectedFile.name}"?`);
            if (confirmar) {
                guardarImagen();
            } else {
                console.log("Carga cancelada por el usuario.");
            }
        } else {
            alert('No hay imagen seleccionada.');
        }
    });

    // Evento al hacer click en el botón de subir imagen
    openSelectorImages.addEventListener('click', () => {
        if (!selectedFile) {
            uploadInput.click();
        }
    });

    // Cuando se selecciona un archivo
    uploadInput.addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;

        openSelectorImages.style.display = 'none';
        botonEliminarImagenSeleccionada.style.display = 'block';
        document.getElementById('linkBtnImagen').style.display = "block";
        document.getElementById('fechaCaducidadBtnImagen').style.display = "block";

        selectedFile = file; // Guardamos la imagen

        const reader = new FileReader();
        reader.onload = function(e) {
            if (!images.includes(e.target.result)) {
                images.push(e.target.result);
                updateImagesList();
            }

            imagenSeleccionada.innerHTML = ''; // Limpiar el contenido del div
            imagenSeleccionada.className = 'imagen-Seleccionada';
            const img = document.createElement('img');
            img.src = e.target.result;
            imagenSeleccionada.appendChild(img);

            currentIndex = images.length - 1;
            updateCarousel();
        };
        reader.readAsDataURL(file);
    });

    // Funciones /////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    function confirmarEliminacionComunicado(){



        let formData = new FormData();
      
        formData.append("id", idImagen);
        
        $('#eliminarComunicadoModal').modal('hide');

        $('#spinnerModal').modal('show');
        
        fetch("comunicados/eliminar_imagen", {
                method: "POST",
                body: formData,
            })
            .then(async (response) => {
                if (!response.ok) {
                    throw new Error(`Error del servidor: ${response.status}`);
                }
                const data = await response.json();
                if (data.status === "success") {
                    alert("comunicado eliminado");
                    location.reload(); // Recargar la página
                } else {
                    console.warn("⚠️ El servidor respondió con error:", data);
                    alert(data.message || "No se pudo completar la acción.");
                    location.reload(); // Recargar la página
                }
            })
            .catch((error) => {
                console.error("❌ Error en la petición:", error);
                alert("Ocurrió un error al intentar eliminar el comunicado. Inténtalo de nuevo.");
                location.reload(); // Recargar la página
            });




    }














    // Función para guardar la imagen y los datos asociados
    function guardarImagen() {
        let formData = new FormData();
        formData.append("link", document.getElementById("linkInput").value);
        formData.append("fechaDuracion", document.getElementById("fechaInput").value);
        formData.append("img", document.getElementById('imageUpload').files[0]);

        fetch("comunicados/subir_imagen", {
                method: "POST",
                body: formData,
            })
            .then(async (response) => {
                if (!response.ok) {
                    throw new Error(`Error del servidor: ${response.status}`);
                }
                const data = await response.json();
                if (data.status === "success") {
                    console.log("✅ Imagen subida y datos guardados:", data);
                    alert("Imagen subida correctamente.");
                    location.reload(); // Recargar la página
                } else {
                    console.warn("⚠️ El servidor respondió con error:", data);
                    alert(data.message || "No se pudo completar la acción.");
                    location.reload(); // Recargar la página
                }
            })
            .catch((error) => {
                console.error("❌ Error en la petición:", error);
                alert("Ocurrió un error al intentar subir la imagen. Inténtalo de nuevo.");
                location.reload(); // Recargar la página
            });
    }

    // Función para editar los datos de una imagen
    function guardarEdicion() {
        let formData = new FormData();
        const link = document.getElementById("linkInputEdit").value;
        const fechaInput = document.getElementById("fechaInputEdit").value;

        if (link && (!link.startsWith("http://") && !link.startsWith("https://"))) {
            alert("Por favor ingresa un enlace válido que comience con http:// o https://");
            return;
        }

        const hoy = new Date();
        const hoyStr = hoy.toISOString().split('T')[0]; // Formato YYYY-MM-DD

        if (fechaInput && fechaInput < hoyStr) {
            alert("La fecha no puede ser menor a la actual.");
            return;
        }

        $('#editarImagen').modal('hide');
        $('#spinnerModal').modal('show');

        formData.append("link", link);
        formData.append("fechaDuracion", fechaInput);
        formData.append("id", idImagen);

        fetch("comunicados/editar_imagen", {
                method: "POST",
                body: formData,
            })
            .then(async (response) => {
                if (!response.ok) {
                    throw new Error(`Error del servidor: ${response.status}`);
                }
                const data = await response.json();
                if (data.status === "success") {
                    alert("Datos actualizados correctamente.");
                    $('#spinnerModal').modal('hide');
                    location.reload(); // Recargar la página
                } else {
                    console.warn("⚠️ El servidor respondió con error:", data);
                    alert(data.message || "No se pudo completar la acción.");
                    location.reload(); // Recargar la página
                }
            })
            .catch((error) => {
                console.error("❌ Error en la petición:", error);
                alert("Ocurrió un error al intentar actualizar los datos. Inténtalo de nuevo.");
                location.reload(); // Recargar la página
            });
    }

    // Función para guardar la fecha de caducidad
    function guardarFecha() {
        const fechaInput = document.getElementById("fechaInput").value;
        if (!fechaInput) {
            alert("Por favor selecciona una fecha.");
            return;
        }
        const hoy = new Date();
        const hoyStr = hoy.toISOString().split('T')[0]; // Formato YYYY-MM-DD
        if (fechaInput >= hoyStr) {
            fechaCaducidadBtnImagen.innerHTML = `${fechaInput} <i class="fa-solid fa-calendar-days" style="padding: 4px"></i>`;
            $('#fechaCaducidadModal').modal('hide');
        } else {
            alert("La fecha no puede ser menor a la actual.");
        }
    }
   
    // Función para guardar el enlace
    function guardarLink() {
        const link = document.getElementById("linkInput").value;
        if (!link) {
            alert("Por favor ingresa un enlace.");
            return;
        }
        if (!link.startsWith("http://") && !link.startsWith("https://")) {
            alert("El enlace debe comenzar con http:// o https://");
            return;
        }
        linkBtnImagen.innerHTML = `<a href="${link}" target="_blank"> Abrir link <i class="fa-solid fa-arrow-up-right-from-square" style="margin-left: 4px;"></i></a> <i class="fa-solid fa-link" style="padding: 1px"></i>`;
        $('#linkRedireccionModal').modal('hide');
    }

    // Función para actualizar las imágenes en el carrusel
    function updateCarouselImages() {
        const hoy = new Date().toISOString().split('T')[0];
        let filteredComunicados = comunicados.filter(item => (item.fechaDuracion === null || item.fechaDuracion === "0000-00-00" || item.fechaDuracion >= hoy) && item.activo == 1);
        images = filteredComunicados.map(item => item.imagen);

        track.innerHTML = '';
        listImages.innerHTML = ''; // También vaciamos la lista previa

        const seen = new Set();

        filteredComunicados.forEach((item) => {
            const imgSrc = item.imagen;
            if (seen.has(imgSrc)) return; // Evitar duplicados
            seen.add(imgSrc);

            const img = document.createElement('img');
            img.src = imgSrc;
            track.appendChild(img);

            const fileElement = document.createElement('div');
            fileElement.dataset.id = item.id;
            fileElement.dataset.fecha = item.fecha;
            fileElement.dataset.imagen = item.imagen;
            fileElement.dataset.fechaDuracion = item.fechaDuracion;
            fileElement.dataset.activo = item.activo;
            fileElement.dataset.link = item.link;

            function link() {
                if (item.link) {
                    return `<div class="link-btn-list"><a href="${item.link}" target="_blank"> Abrir link <i class="fa-solid fa-arrow-up-right-from-square" style="margin-left: 4px;"></i></a></div>`;
                } else {
                    return `<div class="link-btn-list">vacio <i class="fa-solid fa-link" style="padding: 1px"></i></div>`;
                }
            }

            function caducidad() {
                if (item.fechaDuracion && item.fechaDuracion !== "0000-00-00") {
                    const hoy = new Date();
                    const fechaItem = new Date(item.fechaDuracion);
                    const hoyStr = hoy.toISOString().split('T')[0];
                    const fechaItemStr = item.fechaDuracion.split('T')[0];
                    const diferenciaTiempo = fechaItem.getTime() - hoy.getTime();
                    const diferenciaDias = Math.ceil(diferenciaTiempo / (1000 * 60 * 60 * 24));
                    let texto = '';

                    if (fechaItemStr === hoyStr) {
                        texto = 'Expira hoy';
                    } else if (diferenciaDias > 0) {
                        texto = `Expira en ${diferenciaDias} día(s)`;
                    } else {
                        texto = 'Expiró';
                    }

                    return `<div class="fecha-btn-list">${texto} <i class="fa-solid fa-calendar-days" style="padding: 4px"></i></div>`;
                } else {
                    return `<div class="fecha-btn-list">Sin fecha <i class="fa-solid fa-calendar-days" style="padding: 4px"></i></div>`;
                }
            }

            fileElement.className = 'file-list';
            fileElement.innerHTML = `
                <div class="file-left">
                    <div class="file-icon" style="background-image: url('${imgSrc}');"></div>
                    <span>${imgSrc.split('/').pop()}</span>
                </div>
                ${link()}
                ${caducidad()}
                <div class="delete-btn"><i class="fa-solid fa-trash" style="padding: 4px;"></i></div>
                <div class="edit-btn-list"><i class="fa-solid fa-pen-to-square" style="padding: 4px;"></i></div>
            `;

            listImages.appendChild(fileElement);

            const editBtn = fileElement.querySelector('.edit-btn-list');
            editBtn.addEventListener('click', () => {
                idImagen = fileElement.dataset.id;
                document.getElementById("fechaInputEdit").value = fileElement.dataset.fechaDuracion;
                document.getElementById("linkInputEdit").value = fileElement.dataset.link;
                $('#editarImagen').modal('show');
            });

            const deleteBtn = fileElement.querySelector('.delete-btn');
            deleteBtn.addEventListener('click', () => {
                const id = fileElement.dataset.id;
                idImagen = fileElement.dataset.id;
                const imagen = fileElement.dataset.imagen;
                const fecha = fileElement.dataset.fecha;            
                $('#eliminarComunicadoModal').modal('show');
                // fileElement.remove();
                // seen.delete(imagen);
                // console.log('Eliminado:', { id, imagen, fecha });
            });
        });

        fechaCaducidadBtnImagen.style.display = "none";
        fechaCaducidadBtnImagen.innerHTML = `Caducidad <i class="fa-solid fa-calendar-days" style="padding: 4px"></i>`;
        linkBtnImagen.style.display = "none";
        linkBtnImagen.innerHTML = ` Link accion <i class="fa-solid fa-link" style="padding: 1px"></i>`;
    }

    // Función para actualizar las imágenes en el carrusel sin vaciar la lista
    function updateImagesList() {
        track.innerHTML = '';
        const seen = new Set();
        images.forEach(imgSrc => {
            if (seen.has(imgSrc)) return; // Evitar duplicados
            seen.add(imgSrc);
            const img = document.createElement('img');
            img.src = imgSrc;
            track.appendChild(img);
        });
    }

    // Función para mover el carrusel
    function updateCarousel() {
        const width = track.clientWidth;
        track.style.transform = `translateX(-${currentIndex * width}px)`;
    }
</script>



   
@endsection
