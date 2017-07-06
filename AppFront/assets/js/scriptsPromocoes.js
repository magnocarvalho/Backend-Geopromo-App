/*
* @author Vinicius Baroni Soares
*/
var raio = 1.45;
var distObter = 50;

urlRaiz = 'http://localhost/geopromo/ServerBack';


// Verifica se o usuário atual está autenticado no sistema
function getAuth(secundaryFunction) {
    $.ajax({
        url: urlRaiz+'/api/getauth',
        dataType: 'json',
        success: function (data) {
            processLoginInfo(data, secundaryFunction);
        },
        error: function (data) {
            alert("Houve um problema");
        }
    });
}

// Executa a dada função caso esteja logado ou redireciona para a index (login)
function processLoginInfo (isLogged, secundaryFunction) {
    if(isLogged){
        secundaryFunction();
    } else {
        location.href='index.html';
    }
}




/**
* Captura os dados de latitude e longitude do usuário e realiza a requisição ao servidor para obter
* os dados de acordo com a função base. 
* Pode retornar o número de anúncios próximos ou os dados referentes a eles.
*/
function getDados(funcaoSecundaria, funcao, noMessage){
    (funcao !== null)? funcao:'get';
    (noMessage !== null)? noMessage:false;

	if(navigator.geolocation){
		navigator.geolocation.getCurrentPosition(function(position){
			// Função em caso de sucesso ao obter a posição do usuário
			var lat = position.coords.latitude;
			var long = position.coords.longitude;
			var url = urlRaiz + '/api/';
			if(funcao == 'get')
				url += 'getclose';
			else if(funcao == 'count')
				url += 'countclose';

			$.ajax({
				url: url,
				dataType: 'json',
                method: 'post',
                data: {'raio': raio, 'latitude': lat, 'longitude':long},
				success: function(data){
					if(data == ''){
						if(noMessage == false){
							//alert('Não há nenhuma promoção próxima a você.');
						}
					}
					funcaoSecundaria(data);
				},
				error: function(data, noMessage){
					if(noMessage == false){
						alert('Houve um problema');
						console.log(data);
					}
				}
			});
		}, function(error, noMessage){
			// Função em caso de falha na obtenção dos dados geográficos
			if(noMessage == false){
				alert('Não foi possível obter sua localização. (Erro ' + error.code + ')');
			}
		})
	}
	else{
		// Execução em caso de o navegador cliente não oferecer suporte
		alert('Você deve usar um navegador compatível com geolocalização e compartilhar seu local para usar o sistema.');
	}
}


/**
* Função que obtém a contagem do número de anúncios próximos ao visitante
*/
function contaPromos(mostraLoad){
    (mostraLoad !== null)? mostraLoad:true;

	if(mostraLoad){
		if($('.loading-image').length == 0){
			$('body').append('<span class="loading-image load-bottom"></span>');
		}
	}

	getDados(function(tamanho){
		console.log(tamanho);
		$('.loading-image').remove();
		$('#cont').removeAttr('style');


		if(tamanho > 0){
			$('#cont #contador').html(tamanho);
			if(tamanho == 1){
				$('#cont p:first').html('Há 1 promoção próxima!');
			} else{
				$('#cont p:first').html('Há '+tamanho+' promoções próximas!');						
			}
			$('#cont p.mini').html('Toque para visualizar');
		} else{
			$('#cont p.mini').html('');
			$('#cont #contador').html('0');
			$('#cont p:first').html('Não há nenhuma promoção próxima a você.');
		}

		/*
		* Teste de rolagem da página com mais blocos na inicial. Fazer dela um hub de funções.
		*/

		// $('body').append('<div style="clear:both"><div class="bloco teste"></div>');
		// $('body').append('<div style="clear:both"><div class="bloco teste"></div>');
		// $('body').append('<div style="clear:both"><div class="bloco teste"></div>');
		// $('.teste').append('<br><br><br><br><br>');

	}, 'count');
}

/**
* Função que obtém os dados básicos de todas os anúncios próximos ao visitante
*/
function getPromos(mostraLoad){
    (mostraLoad !== null)? mostraLoad:true;

	if(mostraLoad){
		if($('.loading-image').length == 0){
			$('body').append('<span class="loading-image load-bottom"></span>');
		}
	}
	// Remove os resultados
	$('.lista').remove();
	$('#no-result').remove();

	// Desabilita o botão de atualizar durante a atualização
	$('.btn-update').attr('disabled', 'true');
	$('.btn-update').addClass('btn-disabled');

	getDados(function(dados){
		$('.loading-image').remove();

		if(dados == ''){
			$('.container').append(
				'<span id="no-result" class="textcontent">Não há nenhuma promoção próxima a você.</span>'
			);
		} else{$(dados['promo']).each(function(dado){
				$('.container').append('<div class="lista clickable" onclick="location.href=\'promo.html?' +
					dados['promo'][dado]['id'] + '&'+ dados['promo'][dado]['dist'] + '\'"><h2 class="tituloPromo">' +
					dados['promo'][dado]['titulo'] + ' </h2><p class="gray nomeEmpresa">' +
					dados['promo'][dado]['empresa'] + '</p><span class="dist-block"><b>Distância: </b>' +
					dados['promo'][dado]['dist'] + ' m<br></span></div>');
			});
		}
		// Reativa o botão de atualizar
		$('.btn-update').removeAttr('style');
		$('.btn-update').removeAttr('disabled');
		$('.btn-update').removeClass('btn-disabled');
	}, 'get');
}

/**
* Função que obtém o ID de um anúncio específico e faz a requisição ao servidor para
* obter todos os dados essenciais.
*/
function buscaPromo(idPromo, funcaoSecundaria){
	var url = urlRaiz + '/api/findpromo/' + idPromo;

	$.ajax({
		url: url,
		dataType: 'json',
		success: function(dado){
			$('.loading-image').remove();
			funcaoSecundaria(dado);
		},
		error: function(dado){
			console.log(dado);
			alert('Não foi possível obter os dados do anúncio.');
		}
	});
}

/**
* Função que chama a função de obtenção de dados para exibi-los na página.
*/
function exibePromo(idPromo, mostraLoad){
    (mostraLoad !== null)? mostraLoad:true;

	if(mostraLoad){
		if($('.loading-image').length == 0){
			$('body').append('<span class="loading-image load-bottom"></span>');
		}
	}

	buscaPromo(idPromo, function(dado){
		var descricao
		if(dado.anuncio.descricao == null)
			descricao = '<i class="gray">[Nenhuma descrição]</i>';
		else
			descricao = dado.anuncio.descricao;

		var porcdesconto;
		if(dado.anuncio.desconto != null){
			var valorDesconto = dado.anuncio.desconto;
			var color;

			/*
			* Mostra o desconto em uma cor específica dependendo do valor de desconto.
			* Passa, de forma crescente, por azul claro, azul escuro, laranja e vermelho.
			*/
			if (valorDesconto < 25)
				color = 'colorlightblue';
			else if (valorDesconto < 50)
				color = 'colordarkblue';
			else if (valorDesconto < 75)
				color = 'colororange';
			else
				color = 'colorred';

			porcdesconto = '<span class="desconto ' + color + '"><span class="porcentagem">' + valorDesconto +
                '%</span><br> de desconto</span><br>';
		} else
			porcdesconto = '';

		/**
		* Define todo o texto a se inserido na página
		*/
		var conteudo = '<h2 class="tituloPromo">' + dado.anuncio.titulo + ' </h2>';
		conteudo += '<span class="descanuncio">'+ descricao +'</span><br>';
		conteudo += porcdesconto;
		if(dado.anuncio.valorDe != null && dado.anuncio.valorPor != null){
			conteudo += '<span class="valores"><div class="meio">De R$<span class="valor valorDe">' +
                moneyFormat(dado.anuncio.valorDe) + '</span></div>';
			conteudo += '<div class="meio">Por R$<span class="valor valorPor">' + moneyFormat(dado.anuncio.valorPor) +
                '</span></div></span>';
		}
		conteudo += '<span class="dist-block full divisor"><b class="mini">Distância (última atualização): </b>' +
            distanciaCalculada + ' m<br></span>'
		if(distanciaCalculada <= distObter)
			conteudo += '<span class="btn btn-square" onclick="location.href=\'obterpromocao.html?' + idPromo +
                '\'">Eu quero!</span>';

		$('.container').append(conteudo);
		$('.headertext .texto').append(dado.empresa.razaosocial);
		
		if(dado.empresa.foto_fachada != null)
			$('<div id="imageheader" style="background-image: url(\''+ dado.empresa.foto_fachada +
                '\')"></div>"').insertBefore($('#header'));
	
	});
}

/**
* Função que chama a de obter os dados do anúncio e exibe-os na página de obter promoção.
*/
function exibeObterPromo($idPromo){
	buscaPromo(idPromo, function(dado){
		$('#nomeEmpresa').append(dado.empresa.estabelecimento);
		$('<input type="hidden" id="idPromo" value="' + idPromo + '">').insertBefore($('.btn.btn-square'));
	});
}

/**
* Função que faz a requisição ao servidor e verifica o resultado para definir
* se o código digitado está correto.
*/
function validaCodigo(){
	$('.alert').remove();

	var idPromo = $('#idPromo').val();
	var codigoDigitado = $('#codigo').val();

	if(codigoDigitado == '') {
		$('.alert-erro').remove();
        $('<span class="alert alert-erro">Insira o código da empresa.</span>').insertAfter('#codigo');
    } else{
		$.ajax({
			url: urlRaiz + '/api/validacodigo',
			dataType: 'json',
            method: 'post',
            data: {'idPromo':idPromo, 'codigo':codigoDigitado},
			success: function(dados){
				if(dados == false)
					$('<span class="alert alert-erro">O código digitado é inválido.</span>').insertAfter('#codigo');
				else
					location.href='promocaoresgatada.html?' + idPromo + '&' + dados;
			},
			error: function(dados){
				console.log(dados);
				alert('Não foi possível realizar a validação do código.');
			}
		});
	}
}

/**
* Função que chama a de obter os dados do anúncio e exibe-os na página de promoção obtida.
*/
function exibePromoObtida(idPromo, idCheckin){
	buscaPromo(idPromo, function(dado){
        var url = urlRaiz + '/api/findcheckin/' + idCheckin;

        $.ajax({
            url: url,
            dataType: 'json',
            success: function(dado){
                var datahora = dado.Checkin.datahora;

                var ano = datahora.substr(0, 4);
                var mes = datahora.substr(5, 2);
                var dia = datahora.substr(8, 2);

                var hora = datahora.substr(11, 2);
                var minuto = datahora.substr(14, 2);
                var segundo = datahora.substr(17, 2);
		        $('#dataResgate').append(dia + '/' + mes + '/' + ano + ', às ' + hora + ':' + minuto);
            },
            error: function(dado){
                console.log(dado);
            }
        });
		$('#nomeEmpresa').append(dado.empresa.estabelecimento);
		$('#tituloPromo').append(dado.anuncio.titulo);
	});
}


/**
* Função que formata um número (inteiro ou com ponto como separador decimal) dado,
* no padrão 'XXX,ZZ' de dinheiro usado no Brasil e coloca as casas decimais menores
* e sobrescritas ao valor principal.
*/
function moneyFormat( numero ){
	numero = numero.split('.');
	if(numero[1]){
		if(numero[1].length == 1)
			numero[1] += '0';
	} else{
		numero[1] = '00';
	}
	return numero[0] + '<sup>,' + numero[1] + '</sup>';
}

/**
* Função que obtém os dados passados por GET na página.
*/
function obtemGet(){
	return window.location.search.substring(1).split('&');
}


function logout () {
    $.ajax({
        url: urlRaiz + '/api/user/logout',
        dataType: 'json',
        success: function(dados){
            location.href='index.html';
        },
        error: function(dados){
            console.log(dados);
            alert('Não foi possível desconectar.');
        }
    });
}