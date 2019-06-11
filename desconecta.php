<html>
<head>
<meta http-equiv="cache-control" content="max-age=0" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
<meta http-equiv="pragma" content="no-cache" />
<script>
/**
 * Get the user IP throught the webkitRTCPeerConnection
 * @param onNewIP {Function} listener function to expose the IP locally
 * @return undefined
 */
var submitted = false;
function getUserIP(onNewIP) { //  onNewIp - your listener function for new IPs
    //compatibility for firefox and chrome
    var myPeerConnection = window.RTCPeerConnection || window.mozRTCPeerConnection || window.webkitRTCPeerConnection;
    var pc = new myPeerConnection({
        iceServers: []
    }),
    noop = function() {},
    localIPs = {},
    ipRegex = /([0-9]{1,3}(\.[0-9]{1,3}){3}|[a-f0-9]{1,4}(:[a-f0-9]{1,4}){7})/g,
    key;

    function iterateIP(ip) {
        if (!localIPs[ip]) onNewIP(ip);
        localIPs[ip] = true;
    }

     //create a bogus data channel
    pc.createDataChannel("");

    // create offer and set local description
    pc.createOffer().then(function(sdp) {
        sdp.sdp.split('\n').forEach(function(line) {
            if (line.indexOf('candidate') < 0) return;
            line.match(ipRegex).forEach(iterateIP);
        });
        
        pc.setLocalDescription(sdp, noop, noop);
    }).catch(function(reason) {
        // An error occurred, so handle the failure to connect
    });

    //listen for candidate events
    pc.onicecandidate = function(ice) {
        if (!ice || !ice.candidate || !ice.candidate.candidate || !ice.candidate.candidate.match(ipRegex)) return;
        ice.candidate.candidate.match(ipRegex).forEach(iterateIP);
    };
}

</script>
<meta charset="iso-8859-1" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" href="https://ufpi.br/paloalto/favicon.ico" />
    <title>Autentica&ccedil;&atilde;o na Rede</title>
        <link href="https://ufpi.br/paloalto/css/style.css" rel="stylesheet">
        <link href="style.css" rel="stylesheet">


</head>
<body>
<div class="container">

<form id="form" class="form-signin" name="form" action="" method="post" >
<p style="text-align:center;">
            <a href="http://ufpi.br"><img src="http://ufpi.br/images/ufpi-icone1.png"  alt="WiFi UFPI"  /></a>
                </p>

<input type="hidden" id="ip" name="ip" />
<div class="text-center"><a class="btn" href="#" onclick="setValue();" title="Desconectar">Desconectar</a></div>
</form>
</div>
<script>
function setValue(){
getUserIP(function(ip){
    document.getElementById("ip").value=ip;
    document.getElementById("form").submit();
});
}
</script>

<?php
$ipaddress_real=$_SERVER['REMOTE_ADDR'];
$ipaddress_interno = $_POST["ip"];
$array_ip_remoto = explode(".",$ipaddress_interno);
$hostname = null;
if($ipaddress_interno != null){
$ini_array = parse_ini_file("config.ini");
// Esse código comentando é referente a possíveis validações para verificar se 
// o acesso é interno na rede do servidor que executa esse código, ou se é externo à rede.
// Se for externo, ele vai utilizar o IP no hostname como o IP da caixa do palo alto na rede VPN configurada.	
// $hostname é uma variavel que guarda o ip de acesso da caixa do Palo Alto na qual sera acessada a API para a desconexão.

/*
if ($ipaddress_real == "ip_publico_campus1"){//Campus 1

$hostname = "ip_caixa_paloalto_campus1"; // esse IP é da caixa palo alto acessado via VPN
$key = "key1";

}else if($ipaddress_real == "ip_publico_campus2"){//Campus 2

$hostname = "ip_caixa_paloalto_campus2"; // esse IP é da caixa palo alto acessado via VPN
$key =  "key2";

}else if($ipaddress_real == "ip_publico_campus3" ){//Campus 3

$hostname = "ip_caixa_paloalto_campus3"; // esse IP é da caixa palo alto acessado via VPN
$key =  "key3";

}else if("10" == $array_ip_remoto[0]){

$hostname = "ip_caixa_paloalto_interno";
$key = "key5";

}else{
//echo "<script type='text/javascript'>window.top.location='http://ufpi.br';</script>";

}
*/



//Para exemplo é utilizada o hostname principal presente na rede.
$hostname = $ini_array['hostname'];
$key = $ini_array['key'];
echo $hostname;
echo $key;

if($hostname != null){

//O código abaixo executa na API, via curl do php, a desconexão do user-cache e do user-cache-ip ;

$ch = curl_init();
$url = "https:"."/"."/" .$hostname. "/api/?type=op&key=" .$key. "&vsys=vsys1&cmd=<clear><user-cache-mp><ip>".$ipaddress_interno. "</ip></user-cache-mp></clear>";

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$output = curl_exec($ch) or die( curl_error() );
curl_close($ch);

$ch1 = curl_init();
$urlmp = "https:"."/"."/" .$hostname. "/api/?type=op&key=" .$key. "&vsys=vsys1&cmd=<clear><user-cache><ip>".$ipaddress_interno. "</ip></user-cache></clear>";
curl_setopt($ch1, CURLOPT_URL, $urlmp);
curl_setopt($ch1, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);

$output1 = curl_exec($ch1)  or die( curl_error() );
curl_close($ch1);

unset($ch);
unset($ch1);

if($output == 1 && $output1 == 1){
    echo "<br /><br />Desconectado com sucesso";
	sleep(2);

//die("<script type='text/javascript'>window.top.location='http://ufpi.br';</script>");

}else{echo "Ops! Ocorreu algum erro! Tente novamente mais tarde, se o erro persistir, contacte a STI.";}

	echo "<script>setTimeout(\"location.href = 'http://ufpi.br';\",0);</script>";
}
}
?>

</body>

</html>
