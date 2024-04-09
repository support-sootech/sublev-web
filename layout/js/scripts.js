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
