<script>
    let serve = '{{ env('APP_ENV') != 'local' ? '/horasextralito' : env('APP_URL') }}';
    let idU = '{{ auth()->user()->Id_Usuario }}';
    let role = {{ $role }}
    let tDist = {{ $tipoDistinccion }}

    // Obtenemos los permisos del usuario
    const permisos = @json($permisos);

    window.addEventListener('refreshSubrows', function($event) {
        const ids = event.detail.ids;
        let requiredToggle = event.detail.requiredToggle;

        ids.forEach(id => {
            toggleSubRow(id, requiredToggle);
        });
    });

    window.addEventListener('closeSubrowsPs', function($event) {
        // busca todos los subrows que están abiertos y los cierra
        const subRows = document.querySelectorAll('[id^="subtrPs-"]');
        subRows.forEach(subRow => {
            subRow.classList.add('hidden');
            const icon = document.querySelector('#trPs-' + subRow.id.split('-')[1] + ' .icone');
            icon.classList.remove('fa-circle-chevron-up');
            icon.classList.add('fa-circle-chevron-down');

            // Limpia el contenido del subrow
            subRow.innerHTML = '';
        });
    });

    window.addEventListener('refreshFieldStatus', function($event) {
        const select = document.getElementById('estatusF-' + event.detail.id);
        select.value = event.detail.preValue;
    });

    function toggleSubRow(id, requiredToggle = false) {
        const subRow = document.getElementById('subtr-' + id);
        let val = !requiredToggle ? subRow.classList.contains('hidden') : true
        if (val) {
            subRow.classList.remove('hidden');

            // cambiar el icono de la fila
            const icon = document.querySelector('#tr-' + id + ' .icone');
            icon.classList.remove('fa-circle-chevron-down');
            icon.classList.add('fa-circle-chevron-up');

            // Obtenemos por ajax el contenido de la fila y lo insertamos
            const url = serve + '/getdata/:id';
            const urlFinal = url.replace(':id', id);

            // Hacemos la petición
            fetch(urlFinal)
                .then(response => response.text())
                .then(data => {
                    // Iteramos el listado de los colaboradores en el subrow
                    const subRow = document.getElementById('subtr-' + id);

                    // Convertimos el json a objeto
                    data = JSON.parse(data);

                    // Creamos el html
                    let html = '';

                    // Si hay colaboradores los mostramos
                    if (data.length > 0) {
                        // Iteramos el listado de colaboradores
                        html += `<td colspan="16">
                                    <div class="custom-scrollbar block overflow-y-auto shadow-md rounded-b-md px-8">
                                        <div class="grid grid-cols-3 gap-3 w-8/12">
                                            <div class="font-semibold py-3">
                                                <h5>Usuarios relacionados</h5>
                                            </div>
                                            <div class="font-semibold py-3">
                                                <h5>Horas reportables</h5>
                                            </div>
                                            <div class="font-semibold py-3">
                                                <h5>Bitacora</h5>
                                            </div>
                                        </div>

                                `;
                        data.forEach(element => {
                            html += `
                                <div class="px-2 pb-4 grid grid-cols-3 gap-3 px-4 w-8/12">
                                    <p>${element.Personal} - ${element.nombreCompleto}</p>
                                    <div class="flex items-center w-full space-x-5">
                                        <div>`
                            if (permisos.includes('6')) {
                                html += `
                                                        <input type="checkbox" name="personal[]"
                                                            ${element.estatusRelacion == 1 ? 'checked' : ''}
                                                        value="${element.id}"`
                                if (element.estatusSolicitud == 3 || element.estatusSolicitud == 4 || element.cerrada == 1) {
                                    html +=
                                        `disabled class="cursor-not-allowed bg-blue-200 disabled:opacity-50" ></div>`;
                                } else {
                                    html +=
                                        `wire:click="changeEstatusPersonal('${element.idRel}', $event.target.checked, '${element.folio}', '${element.horas}')" ></div>`;
                                }
                            } else {
                                html +=
                                    `
                                                        <input type="checkbox" name="personal[]"
                                                            ${element.estatusRelacion == 1 ? 'checked' : ''}
                                                        value="${element.id}" disabled class="cursor-not-allowed bg-blue-200 disabled:opacity-50" ></div>`;
                            }

                            if (permisos.includes('5')) {
                                html += `
                                                    <div>
                                                        <input type="number" name="horas" value="`
                                html += element.horas_reportables != null ? element.horas_reportables : element.horas;
                                html += `"`;

                                if (element.estatusRelacion == 1 || element.estatusSolicitud == 3 || element.cerrada == 1) {
                                    html +=
                                        `disabled class="cursor-not-allowed disabled:opacity-50 p-0.5 rounded-md border-gray-300 text-xxs text-center isNumberFloat" ></div>`;
                                } else {
                                    html +=
                                        `wire:change="changeHoras('${element.idRel}', $event.target.value, '${element.horas}')" class="p-0.5 rounded-md border-gray-300 text-xxs text-center focus:outline-none focus:ring-0 focus:border-gray-500 isNumberFloat"></div>`;
                                }
                            } else {
                                html += `
                                                    <div class="text-center flex items-center justify-center">
                                                        <p class="text-xxs text-gray-500">Horas asistidas: ${element.horas_reportables != null ? element.horas_reportables : element.horas}</p>
                                                    </div>`;
                            }

                            html += `
                                    </div>
                                    <div class="${element.estatusRelacion == 1 ? 'flex items-center text-center' : 'hidden'}">
                                        <p class="text-xs text-gray-500">Autorizó y fecha: ${element.Nombre} - ${element.updated_at}</p>
                                    </div>
                                </div>
                            `;
                        });

                        html += `</div></td>`;
                    } else {
                        // Si no hay colaboradores mostramos un mensaje
                        html = `
                            <td colspan="16">
                                <div class="text-center text-gray-500 h-full grid place-content-center col-span-2">No se han seleccionado colaboradores</div>
                            </td>
                        `;
                    }

                    // Insertamos el html
                    subRow.innerHTML = html;
                });
        } else {
            // Ocultamos el subrow
            if (subRow) {
                subRow.classList.add('hidden');
            }

            // cambiar el icono de la fila
            const icon = document.querySelector('#tr-' + id + ' .icone');
            icon.classList.remove('fa-circle-chevron-up');
            icon.classList.add('fa-circle-chevron-down');
        }
    }

    function toggleSubRowPs(id, requiredToggle = false) {
        const subRow = document.getElementById('subtrPs-' + id);
        let val = !requiredToggle ? subRow.classList.contains('hidden') : true
        if (val) {
            subRow.classList.remove('hidden');

            // cambiar el icono de la fila
            const icon = document.querySelector('#trPs-' + id + ' .icone');
            icon.classList.remove('fa-circle-chevron-down');
            icon.classList.add('fa-circle-chevron-up');

            // Obtenemos por ajax el contenido de la fila y lo insertamos
            const url = serve + '/getdataps';

            // Obtenemos los datos a enviar
            const dataToSend = {
                id: id, // El ID de la fila
                filtroDepartamento: @this.filtroDepartamento,
                filtroMaquina: @this.filtroMaquina,
                filtroEstatus: @this.filtroEstatus,
                filtroMotivo: @this.filtroMotivo,
                filtroTurno: @this.filtroTurno,
                filtroFecha: @this.filtroFecha,
                filtroHoraInicio: @this.filtroHoraInicio,
                filtroHoraFin: @this.filtroHoraFin,
                filtroOp: @this.filtroOp,
                filtroObservaciones: @this.filtroObservaciones,
                filtroSort: @this.filtroSort,
                filtroSortType: @this.filtroSortType,
                filtroFolio: @this.filtroFolio,
                filtroSemana: @this.filtroSemana,
                filtroPersonal: @this.filtroPersonal,
                filtroPersonalNombre: @this.filtroPersonalNombre,
            };

            // Hacemos la petición
            fetch(url, {
                method: 'POST', // Cambiamos el método a POST
                headers: {
                    'Content-Type': 'application/json', // Indicamos que enviamos JSON
                    'X-CSRF-TOKEN': '{{ csrf_token() }}', // Incluimos el token CSRF para Laravel
                },
                body: JSON.stringify(dataToSend), // Convertimos los datos a JSON
            })
                .then(response => response.text())
                .then(data => {
                    // Iteramos el listado de los colaboradores en el subrow
                    const subRow = document.getElementById('subtrPs-' + id);

                    // Convertimos el json a objeto
                    data = JSON.parse(data);

                    // Creamos el html
                    let html = '';

                    // Si hay colaboradores los mostramos
                    if (data.length > 0) {
                        // Iteramos el listado de colaboradores
                        html += `<td colspan="7">
                            <div class="custom-scrollbar block overflow-y-auto shadow-md rounded-b-md pl-32">
                                <table class="table-auto w-full border-collapse border border-gray-300 tblNormal">
                                    <thead>
                                        <tr class="bg-gray-400 text-white">
                                            <th class="px-4 py-2 font-semibold">Folio Sol.</th>
                                            <th class="px-4 py-2 font-semibold">Usuario Reg.</th>
                                            <th class="px-4 py-2 font-semibold">Depto.</th>
                                            <th class="px-4 py-2 font-semibold">Recurso</th>
                                            <th class="px-4 py-2 font-semibold">Desde día</th>
                                            <th class="px-4 py-2 font-semibold">Semana</th>
                                            <th class="px-4 py-2 font-semibold text-center">Horas solicitud</th>
                                            <th class="px-4 py-2 font-semibold text-center">Horas reales</th>
                                            <th class="px-4 py-2 font-semibold">Motivo</th>
                                            <th class="px-4 py-2 font-semibold">Observaciones</th>
                                            <th class="px-4 py-2 font-semibold">Estatus</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        `;

                        data.forEach(element => {
                        html += `
                        <tr>
                            <td class="px-4 py-2">${element.folio}</td>
                            <td class="px-4 py-2">${element.usuario}</td>
                            <td class="px-4 py-2">${element.departamento}</td>
                            <td class="px-4 py-2">${element.maquina}</td>
                            <td class="px-4 py-2">${element.desde_dia}</td>
                            <td class="px-4 py-2">${element.semana}</td>
                            <td class="px-4 py-2 text-center">${element.horas}</td>
                            <td class="px-4 py-2 text-center">${element.horasReales}</td>
                            <td class="px-4 py-2">${element.motivo}</td>
                            <td class="px-4 py-2">${element.observaciones}</td>
                            <td class="px-4 py-2">${element.estatus}</td>
                        </tr>
                        `;
                        });

                        html += `
                                    </tbody>
                                </table>
                            </div>
                        </td>`;
                    } else {
                        // Si no hay colaboradores mostramos un mensaje
                        html = `
                            <td colspan="8">
                                <div class="text-center text-gray-500 h-full grid place-content-center col-span-2">No hay solicitudes relacionadas</div>
                            </td>
                        `;
                    }

                    // Insertamos el html
                    subRow.innerHTML = html;
                });
        } else {
            // Ocultamos el subrow
            if (subRow) {
                subRow.classList.add('hidden');
            }

            // cambiar el icono de la fila
            const icon = document.querySelector('#trPs-' + id + ' .icone');
            icon.classList.remove('fa-circle-chevron-up');
            icon.classList.add('fa-circle-chevron-down');
        }
    }

    /**
     * Función para obtener el listado de OPs relacionadas a la solicitud

     * @param idSolicitud
     * @return mixed
     */
    function getOps(idSolicitud) {
        // Obtenemos por ajax el contenido de la fila y lo insertamos
        const url = serve + '/getops/:id';
        const urlFinal = url.replace(':id', idSolicitud);

        // Hacemos la petición
        fetch(urlFinal)
            .then(response => response.text())
            .then(data => {
                // Convertimos el json a objeto
                data = JSON.parse(data);

                // Creamos el html
                let html = '';

                // Si hay OPs las mostramos
                if (data.length > 0) {
                    // Iteramos el listado de OPs
                    html += `<div class="custom-scrollbar block overflow-y-auto rounded-b-md px-8 py-4">
                            `;

                    data.forEach(element => {
                        html += `
                            <div class="px-2 pb-4">
                                <p class="text-sm"><span class="text-sky-400">${element.NumOrdem}</span> - ${element.Descricao}</p>
                            </div>
                        `;
                    });

                    html += `</div>`;
                } else {
                    // Si no hay OPs mostramos un mensaje
                    html = `
                        <div class="block overflow-y-auto custom-scrollbar rounded-b-md px-8 py-4">No se han seleccionado OPs</div>
                    `;
                }

                // Insertamos el html en un sweetalert
                Swal.fire({
                    title: 'OPs relacionadas',
                    html: html,
                    showCloseButton: false,
                    showConfirmButton: false,
                    showCancelButton: false,
                    focusConfirm: false,
                    // Alto del modal
                    customClass: {
                        popup: 'h-[60vh]'
                    }
                });

                let swalcontainer = document.getElementById('swal2-html-container');
                swalcontainer.classList.add('custom-scrollbar');

            });
    }

    /**
     * Función para cambiar el estatus de una solicitud
     *
     * @param idSolicitud
     * @param select
     * @param prevValue
     * @return mixed
     */
    function changeEstatus(idSolicitud, select, prevValue) {
        // Obtenemos el valor del select
        let estatus = select.value;

        if (estatus == 3) {
            // Preguntamos con sweetalert si desea cambiar el estatus
            Swal.fire({
                title: '¿Deseas finalizar esta solicitud?',
                text: "Una vez ya no podrás realizar cambios",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, finalizar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Hacemos la petición
                    @this.changeEstatus(idSolicitud, estatus, prevValue);
                } else {
                    // Si no se confirma la acción, volvemos a seleccionar el estatus anterior
                    select.value = prevValue;
                }
            }, prevValue);
        } else {
            // Hacemos la petición
            @this.changeEstatus(idSolicitud, estatus, prevValue);
        }
    }

    /**
     * Función para marcar como finalizada una solicitud
     *
     * @param tipo
     * @return mixed
     */
    function markAsFinalized(tipo = 1) {
        showLoader();
        // Validamos si ha seleccionado alguna solicitud
        let selected = document.querySelectorAll('input[name="solicitudesPf[]"]:checked');
        if (selected.length == 0) {
            // Esperamos 2 segundos y ocultamos el loader
            setTimeout(() => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    text: "Por favor selecciona al menos una solicitud",
                    icon: 'warning',
                });
                hideLoader();
            }, 500);
            return;
        }

        let configSwal = {
            title: tipo == 1 ? '¿Deseas marcar como finalizadas las solicitudes?' : (@this.tipoDistinccion == 1) ? '¿Deseas marcar como finalizadas estas solicitudes?' : ((@this.tipoDistinccion == 2) ? '¿Deseas marcar como por autorizar estas solicitudes?' : '¿Deseas marcar como finalizada estas solicitudes?'),
            text: tipo == 1 ? "Una vez ya no podrás realizar cambios" : "Se marcará como por autorizar las solicitudes seleccionadas",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, continuar',
            cancelButtonText: 'Cancelar'
        }

        // Si el tipo es 1
        if (tipo == 1) {
            // adjuntamos un select
            configSwal.input = 'select';
            configSwal.inputOptions = {
                3: 'Finalizada',
                9: 'Finalizada CR'
            }
        }

        hideLoader();
        // Preguntamos con sweetalert si desea cambiar el estatus
        Swal.fire(configSwal).then((result) => {
            if (result.isConfirmed) {

                // Validamos si el tipo es 1
                let estatus = (tipo == 1 ? result.value : 4);


                // Hacemos la petición
                @this.changeEstatusFinalizado(estatus)
            }
        });
    }

    /**
     * Función para eliminar una solicitud
     *
     * @param idSolicitud
     * @return mixed
     */
    function deleteSolicitud(idSolicitud) {
        // Preguntamos con sweetalert si desea eliminar la solicitud
        Swal.fire({
            title: '¿Deseas eliminar esta solicitud?',
            text: "Una vez eliminada no podrás recuperarla",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Hacemos la petición
                @this.deleteSolicitudW(idSolicitud);
            }
        });
    }

    /**
     * Función para preguntar que tipo de solicitud desea marcar como finalizadas
     *
     * @param opc1
     * @param opc2
     * @return mixed
     */
    function preguntarTipoDeMarcado(opc1, opc2) {

        let confS = {
            title: 'Que tipo de solicitudes deseas seleccionar?',
            text: 'Solo se habilitarán las solicitudes de acuerdo al estatus seleccionado',
            icon: 'warning',
            input: 'select',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, continuar',
            cancelButtonText: 'Cancelar',
            inputValidator: (value) => {
                return new Promise((resolve) => {
                    if (value === '') {
                        resolve('Por favor selecciona una opción');
                    } else {
                        resolve();
                    }
                })
            }
        }

        if (role == 1) {
            confS.inputOptions = {
                1: 'Pendientes',
                2: 'Registrando',
                3: 'Por autorizar',
            }
        } else {
            confS.inputOptions = {
                1: 'Pendientes',
                2: 'Registrando'
            }
        }

        // Preguntamos con sweetalert como desea marcar la solicitud con un select
        Swal.fire(confS).then((result) => {
                if (result.isConfirmed) {
                    // Si el resultado es confirmado, mostramos el loader
                    showLoader();
                    // Obtenemos el valor del select
                    let tipo = result.value;

                    // Hacemos la petición
                    @this.preguntarTipoDeMarcadoW(tipo, opc1, opc2);
                }
            })
        }
</script>
