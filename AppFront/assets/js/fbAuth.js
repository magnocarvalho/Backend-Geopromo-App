/*
 * @author Vinicius Baroni Soares
 */

urlRaiz = 'http://localhost/geopromo/ServerBack';

window.fbAsyncInit = function() {
    // FB JavaScript SDK configuration and setup
    FB.init({
        appId      : '1728558777419207', // FB App ID
        cookie     : true,  // enable cookies to allow the server to access the session
        xfbml      : true,  // parse social plugins on this page
        version    : 'v2.9' // use graph api version 2.9
    });

    // Check whether the user already logged in
    FB.getLoginStatus(function(response) {
        if (response.status === 'connected') {
            //display user data
            getFbUserData();
        }
    });
};
// Load the JavaScript SDK asynchronously
(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/pt_BR/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

// Facebook login with JavaScript SDK
function fbLogin() {
    FB.login(function (response) {
        if (response.authResponse) {
            // Get and display the user profile data
            getFbUserData();
        } else {
            document.getElementById('status').innerHTML = 'User cancelled login or did not fully authorize.';
        }
    }, {scope: 'email'});
}

function getFbUserData(){
    FB.api('/me', {locale: 'pt_BR', fields: 'id,first_name,last_name,email,link,gender,locale,picture'},
    function (response) {
        // Save user data
        saveFBUserData(response);
    });
}

// Salva os dados no BD
function saveFBUserData (data) {
    $.ajax({
        url: urlRaiz + '/api/user/fblogin',
        dataType: 'json',
        data: {provider: 'facebook', userData: JSON.stringify(data)},
        method: 'post',
        success: function (data) {
            console.log(data);
            if(data === true)
                location.href='home.html';
        },
        error: function (data) {
            console.log(data);
            alert("Não foi possível conectar-se usando a conta do Facebook");
        }
    });
}