function preloaderStart(){
    $.preloader.start({
        modal: true,
        src : '/layout/img/ajax-loader.gif'
    });
}
  
function preloaderStop(){
    $.preloader.stop();
}

function gerarAlerta(msg, cabecalho='', tipo='success'){
	$.jGrowl(msg, {
        header: cabecalho,
        theme: 'bg-'+tipo+''
    });

}

async function gerarAlertaConfirmacao(msg, textConfirmButton='Sim', textCancelButton='Não'){
	await Swal.fire({
	    text: msg,
	    confirmButtonColor: '#3085d6',
        cancelButtonColor: '#dc3545',
	    confirmButtonText: textConfirmButton,
        cancelButtonText: textCancelButton,
	    showCancelButton: true,
        icon: "question",
        background: "#fff",
	}).then((result) => {
        
        if (result.isConfirmed) {
            console.log('A');
	        return true;
	    }else{
            console.log('B');
			return false;
		}
        
	});

}

function isFormValidate(elementoForm = null) {
    let isValidate = true;

    if (elementoForm.hasClass('formValidate')) {
        let error = 0;
        elementoForm.find('.requered').each(function(){
            if($(this).val()=='') {
                error++;
            }
        });

        isValidate = error > 0 ? false : true;
    }

    $('form.formValidate').find('.requered').each(function(){
        if ($(this).val()=='') {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });

    return isValidate;
}

function formFieldsRequered() {
    $('form.formValidate').find('input, select, textarea').each(function(){
        let txt = $('label[for='+$(this).attr('id')+']').text().replace('*','');
        if ($(this).hasClass('requered')) {
            txt = txt+'*';
        }
        $('label[for='+$(this).attr('id')+']').text(txt);
    });
}

function soNums(e){
    //teclas adicionais permitidas (tab,delete,backspace,setas direita e esquerda)
    keyCodesPermitidos = new Array(8,9,37,39,46);
    //numeros e 0 a 9 do teclado alfanumerico
    for(x=48;x<=57;x++){
        keyCodesPermitidos.push(x);
    }
    //numeros e 0 a 9 do teclado numerico
    for(x=96;x<=105;x++){
        keyCodesPermitidos.push(x);
    }
    //Pega a tecla digitada
    keyCode = e.which;
    //Verifica se a tecla digitada é permitida
    if ($.inArray(keyCode,keyCodesPermitidos) != -1){
        return true;
    }
    return false;
}

async function buscaCep(numeroCep=''){
	var obj = {};

	if (numeroCep!='') {
		var cep = numeroCep.replace(/\D/g, '');
		//Verifica se campo cep possui valor informado.
		if (cep != "") {
            //Expressão regular para validar o CEP.
            var validacep = /^[0-9]{8}$/;
            //Valida o formato do CEP.
            if(validacep.test(cep)) {
                //Consulta o webservice viacep.com.br/
                $.getJSON("//viacep.com.br/ws/"+ cep +"/json/?callback=?", function(dados) {
                    if (!("erro" in dados)) {
                        obj = dados;
                        return obj;
                    }
                });
            }
		}
	} else{
        return obj;
    }
}

async function consultaViaCep(numeroCep=''){
	
    var cep = numeroCep.replace(/\D/g, '');
    let result;
	try {
        result = await $.ajax({
            url:"//viacep.com.br/ws/"+cep+"/json/?callback=?",
            type:'get',
            dataType:'json'            
        });
        return result;
    } catch (error) {
        console.error('ERROR CONSULTA CEP:', error);
    }
}


$(document).ready(function(){

    $('.somente_numeros').bind('keydown',soNums);
    //$(".moeda_real").maskMoney({symbol:'R$ ', showSymbol:true, thousands:'.', decimal:',', symbolStay: true, allowZero: true, allowEmpty: true});
	//$(".valor-decimal").maskMoney({thousands:'.', decimal:',', allowZero: true, allowEmpty: true});

    //MASCARAS
    $("#date").mask("99/99/9999",{completed:function(){alert("completed!");}});
    $(".phoneExt").mask("(99) 999-9999? x99999");
    $("#iphone").mask("+33 999 999 999");
    $("#tin").mask("99-9999999");
    $("#ssn").mask("999-99-9999");
    $("#product").mask("a*-999-a999", { placeholder: " " });
    $("#eyescript").mask("~9.99 ~9.99 999");
    $("#po").mask("PO: aaa-999-***");
    $("#pct").mask("99%");
    /*
    $("input").blur(function() {
        $("#info").html("Unmasked value: " + $(this).mask());
    }).dblclick(function() {
        $(this).unmask();
    });
    */
    //Mask

    //$(".mask-cep").inputmask("99999-999");
    //$(".mask-data").inputmask("99/99/9999");
    //$(".mask-cnpj").inputmask("99.999.999/9999-99");
    //$(".mask-cpf").inputmask("999.999.999-99");
    //$(".mask-telefone").inputmask("(99) 9999-9999");



    $(".mask-linha-digitavel").mask("99999.99999 99999.999999 99999.999999 9 99999999999999");
    $(".mask-periodo").mask("99/9999");
    $(".mask-data").mask("99/99/9999");
    $(".mask-hora").mask("99:99:99");
    $(".mask-data-hora").mask("99/99/9999 99:99:99");
    $(".mask-placa").mask("aaa-9999");
    $(".mask-cep").mask("99999-999");
    $(".mask-cartao").mask("9999.9999.9999.9999");
    $(".mask-venc-cartao").mask("99/9999");
    $(".mask-venc-cartao-1").mask("99/99");
    $('.valor-decimal').mask('#.##0,00', {reverse: true});

    $(".mask-cnpj").mask("99.999.999/9999-99");
    $(".mask-cpf").mask("999.999.999-99");

    $(".mask-telefone").mask("(99) 9999-9999");
    $(".mask-celular")
            .mask("(99) 9999-9999?9")
            .focusout(function (event) {
                var target, phone, element;
                target = (event.currentTarget) ? event.currentTarget : event.srcElement;
                phone = target.value.replace(/\D/g, '');
                element = $(target);
                element.unmask();
                if(phone.length > 10) {
                    element.mask("(99) 99999-999?9");
                } else {
                    element.mask("(99) 9999-9999?9");
                }
            });
    
    $(".mask-cpf-cnpj")
        .focusout(function (event) {
            var target, val, element;
            target = (event.currentTarget) ? event.currentTarget : event.srcElement;
            val = target.value.replace(/\D/g, '');
            element = $(target);
            element.unmask();
            
            if(val.length <= 2) {
                console.log('VAL', val.length);
                element.unmask();
            } else if(val.length > 11) {
                element.mask("99.999.999/9999-99");
            } else {
                element.mask("999.999.999-99");
            }
    });
    

});