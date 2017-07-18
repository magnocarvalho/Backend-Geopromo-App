/*
 * @author Vinicius Baroni Soares
 */

urlRaiz = 'http://localhost/geopromo/ServerBack';

window.fbAsyncInit = function () {
    // Configuração do SDK JavaScript do FB
    FB.init({
        // appId: '1728558777419207', // FB App ID
        appId      : '348574885553283', // Test app ID
        cookie: true,  // enable cookies to allow the server to access the session
        xfbml: true,  // parse social plugins on this page
        version: 'v2.9' // use graph api version 2.9
    });

    // Verifica se o usuário está conectado ao FB. Caso esteja, realiza as ações definidas
    FB.getLoginStatus(function (response) {
        if (response.status === 'connected') {
            /**
             * Variável "action" é definida globalmente nos arquivos que incluem este (index.html e minhaconta.html)
             */

            // Mostra um indicador de carregamento
            $('body').append('<span class="loading-image load-bottom"></span>');

            // Caso a variável action defina que está em login, realiza a busca dos dados do usuário do FB
            if(action === 'login') {
                getFbUserData();
            }

            // Caso defina que está em logout, finaliza a sessão do FB depois de finalizada a sessão local
            // (quando este arquivo é chamado)
            else if(action === 'logout') {
                FB.logout(function () {
                    location.href = 'index.html';
                });
            }

        } else { // Caso não esteja conectado ao FB, simplesmente redireciona
            // Caso defina que está em logout, finaliza a sessão do FB depois de finalizada a sessão local
            // (quando este arquivo é chamado)
            if(action === 'logout') {
                location.href = 'index.html';
            }
        }
    });
};

// Carrega o SDK do FB assicronamente
(function (d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s);
    js.id = id;
    js.src = "//connect.facebook.net/pt_BR/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));



// Realiza o login no FB com o SDK JavaScript
function fbLogin() {
    // Mostra um indicador de carregamento
    $('body').append('<span class="loading-image load-bottom"></span>');

    FB.login(function (response) {
        if (response.authResponse) {
            getFbUserData();
        } else { // Caso o usuário não autorize ou cancele o login
            //alert('Não foi possível realizar o login com o Facebook');
        }
    });
}

function getFbUserData() {
    FB.api('/me', {locale: 'pt_BR', fields: 'id, first_name, last_name, email, birthday, gender, locale, picture'},
        function (response) {
            console.log(response);
            // Save user data
            saveFBUserData(response);
        });
}

// Salva os dados no BD
function saveFBUserData(data) {
    $.ajax({
        url: urlRaiz + '/api/user/fblogin',
        dataType: 'json',
        data: {provider: 'facebook', userData: JSON.stringify(data)},
        method: 'post',
        success: function (data) {
            // Após a criação da sessão no lado do servidor, realiza o redirecionamento à página do usuário
            if (data === true)
                location.href = 'home.html';
        },
        error: function (data) {
            console.log(data);
            alert("Não foi possível conectar-se usando a conta do Facebook");
        }
    });
}