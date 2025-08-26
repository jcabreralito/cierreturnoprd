<div>
    <div>
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            {{--  Embedemos el panel de microstrategy  --}}
            <div id="mstrContainer" style="height: 100vh;">
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/embeddinglib.js') }}"></script>
    <script>
        //

        baseRestURL = "https://servicios.litoprocess.com/MicroStrategyLibrary";
        projectID = "4144A4A74427236922F4B197856EB90B1";
        dossierHrsExtras = "9770A6194B88033B7B8910804E03ED2C1/share";

        // console.log(baseRestURL);
        let usernameA = "capitalh";
        let passwordA = "chu_mstr56&";

        var dossierUrlAuditorias = baseRestURL + '/app/' + projectID + '/' + dossierHrsExtras;

        microstrategy.dossier.create({
            placeholder: document.getElementById("mstrContainer"),
            url: dossierUrlAuditorias,
            disableNotification: true,

            enableResponsive: true,
            enableCustomAuthentication: true,
            customAuthenticationType: microstrategy.dossier.CustomAuthenticationType.AUTH_TOKEN,
            getLoginToken: login

        }).then(function (dossierMensual) {
            console.log("Dossier creado exitosamente:", dossierMensual);
        }).catch(function (error) {
            console.error("Error al crear el dossier:", error);
        });


        function login() {
            var options = {
                method: 'POST',
                credentials: 'include',
                mode: 'cors',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(
                    { "loginMode": 1, "username": usernameA, "password": passwordA }
                )
            };

            return fetch(baseRestURL + '/api/auth/login', options).then(function (response) {
                if (response.ok) {
                    return response.headers.get('x-mstr-authToken');
                } else {
                    response.json().then(function (json) { //console.log(json);
                    });
                }
            }).catch(function (error) { // console.log(error);
            });
        };

    </script>
</div>
