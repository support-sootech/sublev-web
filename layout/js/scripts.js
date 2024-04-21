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

$(document).on(function(){

});