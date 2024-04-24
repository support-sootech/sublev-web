<?php
function site_url(){

	if(!empty($_SERVER["HTTPS"])){
		if($_SERVER["HTTPS"]!=="off"){
	    	return 'https://'.$_SERVER['SERVER_NAME'];
		}
 		else {
			return 'http://'.$_SERVER['SERVER_NAME'];
		}
    }
  	else{
		return 'http://'.$_SERVER['SERVER_NAME'];
    }
}

function returnPage(){
    $PAGINA = $_SERVER ['REQUEST_URI'];

    $PAGINA = explode('?', $PAGINA);
    if (is_array($PAGINA)) {
		$PAGINA = $PAGINA[0];
		if ($_SERVER['REQUEST_METHOD']=='GET') {
			$arr = 	explode('/', $PAGINA);
			$PAGINA = is_array($arr) ? '/'.$arr[1] : $arr;
		}
    } else {	
		$PAGINA = $PAGINA;
    }

    return $PAGINA;
}

function salvar_log($string){
  $arquivo    = site_url().'/imagens/log.txt';
  $fg_existe  = '';
  if(file_exists($arquivo)){
    $fg_existe = 'FILE_APPEND';
  }
  $escrita = file_put_contents($arquivo, $string.'\n', $fg_existe);
}

function convertImageBase64($path){
    //$path = 'myfolder/myimage.png';
    $type = pathinfo($path, PATHINFO_EXTENSION);
    $data = file_get_contents($path);
    $base64 = 'data:image/'.$type.';base64,'.base64_encode($data);
    return $base64;
}

function verMatriz($vetor = array()){
	echo '<pre>';
		var_dump($vetor);
	echo '</pre>';
}

function numberformat($valor, $simbolo=true, $decimal=2){
	$valor = ($simbolo?'R$ ':'').number_format($valor, $decimal, ',', '.');
	return $valor;
}

function numberFormatBanco($valor){
	$valor = str_replace('R$ ', '', $valor);
	$valor = str_replace('.', '', $valor);
	$valor = str_replace(',', '.', $valor);
	return $valor;
}

function dt_banco($dt){
	if ($dt) {
		$dt = explode('/', $dt);
		$data = $dt[2].'-'.$dt[1].'-'.$dt[0];
	} else {
		$data = '';
	}
	return $data;
}

function dh_banco($dh){

	$dh = explode(' ', trim($dh));

	if ($dh[0]) {
		$dt = explode('/', $dh[0]);
		$data = $dt[2].'-'.$dt[1].'-'.$dt[0].' '.$dh[1];
	} else {
		$data = '';
	}
	return $data;
}

function dt_br($dt){
	if ($dt) {
		$data = date('d/m/Y', strtotime($dt));
	} else {
		$data = '';
	}
	return $data;
}

function dh_br($dt){
  $dh = '';
  $dt = trim($dt);
  if (!empty($dt)) {
    //$dh = date('d/m/Y H:i:s', strtotime($dt));
    $e = explode(' ', $dt);
    $h = isset($e[1]) ? $e[1] : '00:00:00';
    $d = isset($e[0]) ? explode('-', $e[0]) : '----';
    if (is_array($d)) {
      $dh = $d[2].'/'.$d[1].'/'.$d[0].' '.$h;
    }else{
      $dh = $d.' '.$h;
    }
	}

	return $dh;
}

function dias_semana($numero){
	switch ($numero) {
	    case 0:
	        return 'Domingo';
	        break;
	    case 1:
	        return 'Segunda-Feira';
	        break;
	    case 2:
	        return 'Terça-Feira';
	        break;
		case 3:
	        return 'Quarta-Feira';
	        break;
		case 4:
	        return 'Quinta-Feira';
	        break;
		case 5:
	        return 'Sexta-Feira';
	        break;
		case 6:
	        return 'Sábado';
	        break;
	}
}

function hoje(){
	setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
	date_default_timezone_set('America/Sao_Paulo');
	return date('Y-m-d');
}

function somar_dias($dt, $qtd){
	return date('Y-m-d', strtotime('+'.$qtd.' days', strtotime($dt)));
}

function somar_meses($dt, $qtd){
	return date('Y-m-d', strtotime('+'.$qtd.' month', strtotime($dt)));
}

function diferenca_datas($dt_maior, $dt_menor){
	$dt_maior = strtotime($dt_maior);
	$dt_menor = strtotime($dt_menor);
	$diferenca = $dt_maior - $dt_menor;
	$dias = (int)floor($diferenca/(60*60*24));
	return $dias;
}

function diferenca_datas_array($firstDate, $secondDate){
    $firstDate  = new DateTime($firstDate);
	$secondDate = new DateTime($secondDate);
	$intvl = $firstDate->diff($secondDate);

	$return = array();

    $return['year']     = $intvl->y;
    $return['months']   = $intvl->m;
    $return['days']     = $intvl->days;
    $return['hours']    = ($intvl->days)*24;
    $return['minute']   = ($intvl->days)*24*60;
    $return['seconds']  = ($intvl->days)*24*60*60;

    if($return['seconds']<1){
        //12 horas
        $return['seconds'] = 64800;
    }

	return $return;
}

function dia_semana($dt){
	return date('w', strtotime($dt));
}

function proximo_dia_util($dt){
	$dia_semana = dia_semana($dt);

	if($dia_semana==='0' || $dia_semana==='6'){
			$dt = somar_dias($dt, ($dia_semana==='0'?1:2));
	}
	return $dt;
}

function compara_datas($dt1, $dt2){

	$str_dt1 = strtotime($dt1);
	$str_dt2 = strtotime($dt2);

	if (($str_dt1-$str_dt2)>0) {
		$return = '1';
	} else if(($str_dt1-$str_dt2)==0) {
		$return = '0';
	} else {
		$return = '-1';
	}

	return $return;

}

function mes_abreviado($mes){
	switch ($mes) {
    	case '01':
    		$mes = 'Jan';
    		break;
    	case '02':
    		$mes = 'Fev';
    		break;
    	case '03':
    		$mes = 'Mar';
    		break;
		case '04':
    		$mes = 'Abr';
    		break;
		case '05':
    		$mes = 'Mai';
    		break;
		case '06':
    		$mes = 'Jun';
    		break;
		case '07':
    		$mes = 'Jul';
    		break;
		case '08':
    		$mes = 'Ago';
    		break;
		case '09':
    		$mes = 'Set';
    		break;
		case '10':
    		$mes = 'Out';
    		break;
		case '11':
    		$mes = 'Nov';
    		break;
		case '12':
    		$mes = 'Dez';
    		break;
    	}
    return $mes;
}

function mes_extenso($mes){
	switch ($mes) {
    	case '01':
    		$mes = 'Janeiro';
    		break;
    	case '02':
    		$mes = 'Fevereiro';
    		break;
    	case '03':
    		$mes = 'Março';
    		break;
		case '04':
    		$mes = 'Abril';
    		break;
		case '05':
    		$mes = 'Maio';
    		break;
		case '06':
    		$mes = 'Junho';
    		break;
		case '07':
    		$mes = 'Julho';
    		break;
		case '08':
    		$mes = 'Agosto';
    		break;
		case '09':
    		$mes = 'Setembro';
    		break;
		case '10':
    		$mes = 'Outubro';
    		break;
		case '11':
    		$mes = 'Novembro';
    		break;
		case '12':
    		$mes = 'Dezembro';
    		break;
    	}
    return $mes;
}

function retorna_idade($dt){
	$data = dt_br($dt);
    // Separa em dia, mês e ano
    list($dia, $mes, $ano) = explode('/', $data);
    // Descobre que dia é hoje e retorna a unix timestamp
    $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
    // Descobre a unix timestamp da data de nascimento do fulano
    $nascimento = mktime( 0, 0, 0, $mes, $dia, $ano);
    // Depois apenas fazemos o cálculo já citado :)
    $idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25);
    return $idade;
}

function retornaIdadeArray($d1='', $d2=''){
  $idade = array();
  if (!empty(limpa_numero($d1)) && !empty(limpa_numero($d2))) {
    $d1 = new DateTime($d1);
  	$d2 = new DateTime($d2);
  	$idade = $d1->diff($d2);
  }
  return $idade;
}

function validar_data($data){
	$arr_data 	= explode('/', $data);
	$validacao 	= checkdate(($arr_data[1]*1), ($arr_data[0]*1), $arr_data[2]);
	if ($validacao) {
		return TRUE;
	} else {
		return FALSE;
	}
}

function retorna_dt_vencimento_pagto_boleto($str) {
  $dt_base = '1997-10-07';
	$dt_vencimento 	= somar_dias($dt_base, $str);
	$vencido = compara_datas($dt_vencimento, hoje());
	return array('dt_vencimento'=>dt_br($dt_vencimento), 'fg_vencido'=>($vencido<0?TRUE:FALSE));
}

function mover_arquivo($arquivo, $dir, $nome){
	if (file_exists($dir)) {
		$mover = move_uploaded_file($arquivo, $dir.'/'.$nome);
		if($mover){
			return TRUE;
		}else{
			return $mover;
		}

	}else{
		return FALSE;
	}
}

function somar_datas($numero, $tipo){
	switch ($tipo) {
    	case 'd':
    		$tipo = ' day';
    		break;
    	case 'm':
    		$tipo = ' month';
    		break;
    	case 'y':
    		$tipo = ' year';
    		break;
    	}
    return "+".$numero.$tipo;
}

function valida_logado($fg_valida_page=false){

	if (isset($_SESSION['usuario']['hash_login']) && !empty($_SESSION['usuario']['hash_login'])) {

		if ($fg_valida_page) {
			if (isset($_SESSION['usuario']['endpoints'])) {
				if (array_key_exists(returnPage(), $_SESSION['usuario']['endpoints'])) {
					return true;
				} else {
					return false;
				}
			}
		} else {
			return true;
		}
	} else {
		return false;
	}

	/*
	$page = returnPage();
	$menu = menuSistema();
	if (isset($menu[$page]) && isset($_SESSION['usuario'])) {
		return (in_array($page, $_SESSION['usuario']['menus']));
	} else {
		return false;
	}
	*/	
	
}

function limpa_numero($str) {
    return preg_replace("/[^0-9]/", "", $str);
}

function preencheZero($nro, $qtde, $value='0'){
	return str_pad($nro, $qtde, $value, STR_PAD_LEFT);
}

function mascara($val='', $mask=''){
    $maskared = '';
    $k = 0;
    for($i = 0; $i<=strlen($mask)-1; $i++) {
        if($mask[$i] == '#') {
            if(isset($val[$k])) $maskared .= $val[$k++];
        }else {
            if(isset($mask[$i])) $maskared .= $mask[$i];
        }
    }
 return $maskared;
}

function mascaraTelefone($telefone){
  $num = '';
  $telefone = $telefone.'';

  if(isset($telefone) && !empty($telefone) && strlen($telefone) >= 10){
    if(strlen($telefone)>10){
      $num = mascara($telefone,'(##) #####-####');
    }else{
      $num = mascara($telefone,'(##) ####-####');
    }
  }

  return $num;
}

function mascaraCpfCnpj($tipo, $num){

  if($tipo=='J'){
    $num = mascara(preencheZero($num, 14),'##.###.###/####-##');
  }else{
    $num = mascara(preencheZero($num, 11),'###.###.###-##');
  }

  return $num;
}

function mascaraCartao($num=''){

  if(!empty($num)){
    $num = mascara($num,'####.####.####.####');
  }

  return $num;
}

function mascaraCep($num=''){
  $num = $num.'';
  if(!empty($num)){
    $num = mascara($num,'#####-###');
  }

  return $num;
}

function formato_moeda($valor){
	return number_format((isset($valor)?$valor:0.00),'2',',','.');
}

function extensoes_permitidos(){
	return array('pdf','jpg','png');
}

function imagens_permitidas(){
	return array('bmp','jpg','jpeg','png');
}

function By2M($size){
    $filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
    return $size ? round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i] : '0 Bytes';
}

function removeAcentosString($string){
  $string = preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim($string)));
  return $string;
}

function removeAcentos($Msg)
{
	/*
	$a = array(
		"/[ÂÀÁÄÃ]/" =>"A",
		"/[âãàáä]/" =>"a",
		"/[ÊÈÉË]/"  =>"E",
		"/[êèéë]/"  =>"e",
		"/[ÎÍÌÏ]/"  =>"I",
		"/[îíìï]/"  =>"i",
		"/[ÔÕÒÓÖ]/" =>"O",
		"/[ôõòóö]/" =>"o",
		"/[ÛÙÚÜ]/"  =>"U",
		"/[ûúùü]/"  =>"u",
		"/ç/"       =>"c",
		"/Ç/"       => "C",
		"/ñ/"       => "n",
		"/Ñ/"       => "N",
		"/&/"       => "e");
	return preg_replace(array_keys($a), array_values($a), $Msg);
	*/

	$comAcentos = array('à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'O', 'Ù', 'Ü', 'Ú');
	$semAcentos = array('a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U');

	return str_replace($comAcentos, $semAcentos, $Msg);

}

function remover_caracter($string) {
    $string = preg_replace("/[áàâãä]/", "a", $string);
    $string = preg_replace("/[ÁÀÂÃÄ]/", "A", $string);
    $string = preg_replace("/[éèê]/", "e", $string);
    $string = preg_replace("/[ÉÈÊ]/", "E", $string);
    $string = preg_replace("/[íì]/", "i", $string);
    $string = preg_replace("/[ÍÌ]/", "I", $string);
    $string = preg_replace("/[óòôõö]/", "o", $string);
    $string = preg_replace("/[ÓÒÔÕÖ]/", "O", $string);
    $string = preg_replace("/[úùü]/", "u", $string);
    $string = preg_replace("/[ÚÙÜ]/", "U", $string);
    $string = preg_replace("/ç/", "c", $string);
    $string = preg_replace("/Ç/", "C", $string);
    $string = preg_replace("/[][><}{)(:;,!?*%~^`@]/", "", $string);
    //$string = preg_replace("/ /", "_", $string);
    return $string;
}

function siteHost(){
  $txt      = '';
  $host     = $_SERVER['HTTP_HOST'];
  $arr_host = explode('.',$host);
  return $arr_host[0];
}

function redirect($url){
    echo '<script type="text/javascript">window.location=\''.$url.'\';</script‌​>';
}

function senhaValida($senha) {
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[\w$@]{6,}$/', $senha);
}

function messagesDefault($idx) {
	$message['OK'] = 'OK';
	$message['register'] = 'Cadastro efetuado com sucesso!';
	$message['update'] = 'Alteração efetuada com sucesso!';
	$message['delete'] = 'Registro deletado com sucesso!';
	$message['fields_requered'] = 'Existem campos obrigatórios para serem preenchidos!';
	$message['invalid_credentials'] = 'Credenciais inválidas!';
	$message['register_not_found'] = 'Registro não localizado!';
	$message['incorrect_method'] = 'Método incorreto!';
	$message[10] = 'CPF / CNPJ já cadastrado no sistema!';
	$message['register_password'] = 'Senha definida com sucesso!';
	$message['register_password_send'] = 'E-mail de redefinição de senha enviado com sucesso!';

	return isset($message[$idx]) ? $message[$idx] : '';
}

?>
