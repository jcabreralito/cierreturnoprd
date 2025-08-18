
document.addEventListener('generarReporteJornadas', function(event) {
    let filters = event.detail.filters;
    let nombreReporte = event.detail.nombreDocumento;
    let semanaActual = event.detail.semana;

    generateReportHrs(filters, nombreReporte, semanaActual);
});

/**
 * Función para generar el archivo de excel de horas extras
 *
 * @param {Object} filters - Filtros para la generación del reporte
 * @param {string} nombreReporte - Nombre del reporte a generar
 * @param {string} semanaActual - Semana actual para la generación del reporte
 * @return void
 */
function generateReportHrs(filters, nombreReporte, semanaActual) {
    // Creamos el formulario que se enviara para descargar el excel
    formDataDownloadExcel = new FormData();
    formDataDownloadExcel.append("_token", document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    formDataDownloadExcel.append("_method", "POST");
    formDataDownloadExcel.append("filters", JSON.stringify(filters));
    formDataDownloadExcel.append("semanaActual", semanaActual);

    // Ejecutamos la acción de ajax
    fetch(serve + "/generateReportHrs", {
        method: "POST",
        body: formDataDownloadExcel,
        async: true,
    }).then(response => response.json())
    .then(data => {
        let response = data.response

        if (response) {

            // Creamos un nuevo libro de trabajo
            const workbook = XLSX.utils.book_new();

            // Definimos las cabeceras por defecto
            const headers = [
                [
                    'Personal',
                    'Jornada',
                    'Fecha',
                ]
            ];

            // Datos de comisiones
            const list = data.data;

            // Verifica si la lista está vacía
            if (!list || list.length === 0) {
                return; // Sale de la función si no hay datos
            }

            // Convertir los datos al formato esperado por XLSX.utils.aoa_to_sheet
            // Primero, mapea los datos para que coincidan con el orden de los headers
            const dataRows = list.map(item => [
                item.personal,
                item.jornada,
                item.Fecha
            ]);

            // Combina los headers con los datos
            const dataItems = [...headers, ...dataRows];

            const worksheet = XLSX.utils.aoa_to_sheet(dataItems);

            // Añadir la hoja de cálculo al libro de trabajo
            XLSX.utils.book_append_sheet(workbook, worksheet, 'Reporte');

            // Escribimos el libro de trabajo en un archivo
            XLSX.writeFile(workbook, nombreReporte+'.xlsx');

        } else {
            const Msg = Swal.mixin({
                toast: true,
                position: "center",
                icon: "warning",
                showConfirmButton: false,
                timer: 3000
            });

            Msg.fire({ title: 'No se encontro información para generar' });
        }
    })
    .catch((e) => {
        const Msg = Swal.mixin({
            toast: true,
            position: "center",
            icon: "warning",
            showConfirmButton: false,
            timer: 3000
        });

        console.log(e.message);

        Msg.fire({ title: "Ocurrió un error, al obtener la información" });
    });
}
