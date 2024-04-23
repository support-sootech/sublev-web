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

$(document).ready(function(){

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