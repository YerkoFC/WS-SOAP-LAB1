<?php 

include_once 'lib/nusoap.php'; // Se incluye la libreria nusoap
$servicio = new soap_server(); // Instancia de un servicio

$ns = "urn:webService";
$servicio->configureWSDL("Web services laboratorio 2", $ns); // Requiere 2 parametros ("nombreWebService", $ns)

$servicio->schemaTargetNamespace = $ns;

/**** DECLARACIÓN DE MÉTODOS DEL WEB SERVICE ****/

$servicio->register("validarRut", array('rut' => 'xsd:string'), array('return' => 'xsd:string'), $ns);// Recibe 4 parametrosa (nombreFuncion, parametros de entrada, valor de retorno, $ns)

$servicio->register("nombrePropio", array('apePaterno' => 'xsd:string', 'apeMaterno' => 'xsd:string', 'nombres' => 'xsd:string', 'sexo' => 'xsd:string'), array('return' => 'xsd:string'));
 

 
/************************************************/

/**** MÉTODO DE VALIDACIÓN DE DÍGITO VERIFICADOR ****/

function validarRut($rut){
	
	$arreglo = 	str_split($rut, 1); // Se convierte el string rutString en un arreglo
	$digVer = end($arreglo); // Se guarda el digito verificador para ser comprobado posteriormente
	$iniSerie = 2; // Valor inicial de la serie 
	$totalSuma = 0; // Suma total para aplicar el modulo K1
	$numerosPrecedentes = 0; // Variable que guardara la cantidad de numeros precedentes al guión del rut

	for ($i=0; $i < strlen($rut); $i++) { // Ciclo que busca contar la cantidad de numeros precedentes al guión
		if($arreglo[$i] == '-'){
			$numerosPrecedentes =  $i;
		}
	}

	$arregloCortado = array_slice($arreglo, 0, $numerosPrecedentes);
	$arregloDadoVuelta = array_reverse($arregloCortado);

	$contador = 0; // Variable contador 

	while ($contador < $numerosPrecedentes) {	
		if($iniSerie < 8){
			$multiplicacion = $iniSerie * intval($arregloDadoVuelta[$contador]);
			$totalSuma = $totalSuma + $multiplicacion;
			$contador++;
			$iniSerie++;
			$multiplicacion = 0;	
		}else{
			$iniSerie = 2;
		}
	}
	$modulo = $totalSuma % 11;;
	$numFinal = 11 - $modulo;

	$numValidado = 'X';

	if($numFinal == 11){
		$numValidado = '0';
	}elseif ($numFinal == 10){
		$numValidado = 'K';
	}else{
		$numValidado = strval($numFinal);
	}

	$respuestaFinal = '0';

	if(strcasecmp($numValidado, $digVer) == 0){
		$respuestaFinal = 'valido';
	}else{
		$respuestaFinal = 'invalido';
	}

	return $respuestaFinal;
}

/**** MÉTODO DE NOMBRE PROPIO ****/

function nombrePropio($apePaterno, $apeMaterno, $nombres, $sexo){

	$nombreCompleto = $nombres . ' ' . $apePaterno . ' ' . $apeMaterno;

	if($sexo === 'h' || $sexo === 'H'){
		return 'Hola Sr. ' . ucwords(strtolower($nombreCompleto));
	}elseif($sexo === 'm' || $sexo === 'M'){
		return 'Hola Sra. ' . ucwords(strtolower($nombreCompleto));
	}else{
		return 'Hola ' . ucwords(strtolower($nombreCompleto));
	}

}



$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';

$servicio->service(file_get_contents("php://input"));



 ?>