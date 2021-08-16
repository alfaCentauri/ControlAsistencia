/**
 * Envia los datos del formulario de la asistencia de entrada.
 **/
function sendDataAjax(ruta) {
    //
    const formData = new FormData();
    formData.append("empleadoCedula", selectEmpleados);
    formData.append("horaEntrada", horaEntrada);
    $.ajax({
        url: ruta,
        data: formData,
        dataType: 'text',
        cache: false,
        async: false,
        contentType: false,
        processData: false,
        mimeType: 'multipart/form_data',
        type: "POST",
        success: function (data) {
            if (typeof (data) != 'undefined') {
                result = JSON.parse(data);
            }
        },
        error: function () {
            uploadStatus = false;
            alert("Error!");
        },
        complete: function () {
            console.log("Completado...");
            $("#inputArchivo").val(null);
        }
    });
}
