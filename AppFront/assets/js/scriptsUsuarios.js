/*
* @author Vinicius Baroni Soares
*/

urlRaiz = 'http://localhost/geopromo/ServerBack';

// Verifica se o usuário atual está autenticado no sistema
function getAuth() {
    $.ajax({
        url: urlRaiz+'/api/getauth',
        dataType: 'json',
        success: function (data) {
            console.log(data);
            showLogin1Content(data);
        },
        error: function (data) {
            alert("Houve um problema");
        }
    });
}


// Exibe os dados da página inicial com base na autenticação do usuário
function showLogin1Content (isLogged) {
    $('.loading-image').remove();

    $('.container').empty();
    $('.container').append('<span class="textcontent"></span>');

    if(isLogged) {
        // TODO: caso já esteja logado - redirecionar para a home

    } else {
        var conteudo = '<h2 class="colororange">Conecte-se para encontrar promoções próximas</h2>';
        conteudo += '<p>Conecte-se à sua conta ou crie uma nova.</p><br>';
        conteudo += '<input type="email" class="input input-full textcenter" id="emailLogin" ' +
            'placeholder="Informe seu email"><br>';
        conteudo += '<span class="btn btn-square" id="btnContinuar" onclick="loginEmail($(\'#emailLogin\').val())">' +
            'Continuar</span>';

        $('.textcontent').append(conteudo);
    }
}


// Envia o email ao servidor para verificar se já está registrado
function loginEmail(email) {
    $('body').append('<span class="loading-image load-bottom"></span>');
    if(email == '') {
        $('.load-bottom').remove();
        $('.alert-erro').remove();
        $('<span class="alert alert-erro">Insira seu email</span>').insertAfter('#emailLogin');
    } else {
        $.ajax({
            url: urlRaiz+'/api/user/checkemail/'+email,
            dataType: 'json',
            success: function (data) {
                $('.load-bottom').remove();
                showLogin2Content(data);
            },
            error: function (data) {
                $('.load-bottom').remove();
                alert("Houve um erro");
            }
        });
    }
}

// Mostra o conteúdo na página com base na existência ou não do login no BD
function showLogin2Content (emailExistente) {
    if(emailExistente){
        // TODO: caso o email esteja no BD
        alert(emailExistente);

    } else {
        $('.btn').remove();
        $('h2').html('Este email não está cadastrado');
        $('p').html('Você pode mudá-lo e tentar se conectar novamente ou pode se cadastrar usando este email.');

        var conteudo = '<span class="btn btn-square btn-secundary" id="btnContinuar" ' +
            'onclick="loginEmail($(\'#emailLogin\').val())" ' +
            'style="width:35%; margin-right:10px">Conectar</span>';

        conteudo += ' <span class="btn btn-square" id="btnCadastrar" onclick="cadastroEmail($(\'#emailLogin\').val())" ' +
            'style="width:35%; margin-left:10px">' +
            'Cadastrar-se</span>';

        $('.textcontent').append(conteudo);
    }
}




// Envia o email ao servidor para iniciar o cadastro
function cadastroEmail(email) {
    $('body').append('<span class="loading-image load-bottom"></span>');
    if(email == '') {
        $('.load-bottom').remove();
        $('.alert-erro').remove();
        $('<span class="alert alert-erro">Insira seu email</span>').insertAfter('#emailLogin');
    } else {
        $.ajax({
            url: urlRaiz+'/api/user/register/email/'+email,
            dataType: 'json',
            success: function (data) {
                $('.load-bottom').remove();
                showRegister1Content();
            },
            error: function (data) {
                $('.load-bottom').remove();
                alert("Houve um erro");
            }
        });
    }
}

// Exibe os dados da página de registro (etapa 1) - TODO: semelhante ao login
function showRegister1Content (isLogged) {
    $('.loading-image').remove();

    $('.textcontent').empty();


        var conteudo = '<h2 class="colororange">Crie sua conta, é rapidinho</h2>';
        conteudo += '<p>Defina agora sua senha de acesso à conta.</p><br>';
        conteudo += '<input type="password" class="input input-full textcenter" id="senharegistro" ' +
            'placeholder="Defina a senha"><br>';
        conteudo += '<span class="btn btn-square" id="btnContinuar" onclick="cadastroSenha($(\'#senharegistro\').val())">' +
            'Continuar</span>';

        $('.textcontent').append(conteudo);
}


// Envia o email ao servidor para armazenar a senha no objeto
function cadastroSenha(senha) {
    $('body').append('<span class="loading-image load-bottom"></span>');
    if(senha == '') {
        $('.load-bottom').remove();
        $('.alert-erro').remove();
        $('<span class="alert alert-erro">Defina uma senha para a conta</span>').insertAfter('#senharegistro');
    } else {
        $.ajax({
            url: urlRaiz+'/api/user/register/senha/'+senha,
            dataType: 'json',
            success: function (data) {
                $('.load-bottom').remove();
                alert(senha);
                //showRegister1Content(data);
            },
            error: function (data) {
                $('.load-bottom').remove();
                alert("Houve um erro");
            }
        });
    }
}