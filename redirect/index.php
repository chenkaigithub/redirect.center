<?php

$counter_visible = getenv("COUNTER_VISIBLE") ? getenv("COUNTER_VISIBLE") : 'true';
$counter_redis_host = getenv("COUNTER_REDIS_HOST") ? getenv("COUNTER_REDIS_HOST") : '127.0.0.1';
$counter_redis_port = getenv("COUNTER_REDIS_PORT") ? getenv("COUNTER_REDIS_PORT") : '6379';

if ($counter_visible == "true") {
    $redis = new Redis();
    $redis->connect($counter_redis_host, $counter_redis_port);
    $redis->set('ever_'.strtolower($_SERVER['HTTP_HOST']), '1');
    $redis->setex('24h_'.strtolower($_SERVER['HTTP_HOST']), 86400, '1');
}

$redirect_domain = getenv("SITE_DOMAIN") ? getenv("SITE_DOMAIN") : 'redirect.center';
$GLOBALS['redirect_domain'] = $redirect_domain;

$r = dns_get_record($_SERVER['HTTP_HOST'],DNS_A + DNS_CNAME);
$found = $r[0];

foreach ($r as $x) {

    if (strtolower($x['host']) == strtolower($_SERVER['HTTP_HOST'])) {
        $found = $x;
    }

}

if ($found['type'] == "A") {

	# Verifica se existe a entrada redirect.center.HTTP_HOST
	$record = "redirect.".$_SERVER['HTTP_HOST'];
	$rr = dns_get_record($record,DNS_CNAME);

	redirect($rr[0]['type'],$record,$rr[0]['target']);

}

elseif ($found['type'] == "CNAME") {

	redirect($found['type'],$_SERVER['HTTP_HOST'],$found['target']);

}

function redirect ($type,$record,$target) {

    $record = strtolower($record);
    $target = strtolower($target);

	if ($type == "CNAME") {

        $code = 301;

        $target = str_replace(".".$GLOBALS['redirect_domain'],"",$target);

        # Verifica redirecionamento por URI
        if (strstr($target,".opts-uri")) {
            $target = str_replace(".opts-uri","",$target);
            $target .= $_SERVER['REQUEST_URI'];
        }

        # Redirect to specific path when there are slashes
        $target = str_replace(".slash.","/",$target);
        $target = str_replace(".opts-slash.","/",$target);

        # Muda codigo de redirect
        if (strstr($target,".opts-statuscode-")) {
            $code = strstr($target,".opts-statuscode-");
            $code = str_replace(".opts-statuscode-","",$code);
            $target = str_replace(".opts-statuscode-".$code,"",$target);
            $code = filter_var($code, FILTER_SANITIZE_NUMBER_INT);
        }

        Header('location: http://' . $target , true, $code);
        return;

	}

	// ERRO INDICANDO QUE DEVERIA SER DO TIPO CNAME
    print "<html><head><title>error</title></head><body><pre>\n";
    print "I can't resolve record: ".$record.".\n\n";
    print "Add in your dns server this entry:\n";
    print "redirect.".$_SERVER['HTTP_HOST']." CNAME your_redirect.".$GLOBALS['redirect_domain'].".\n\n";
    print "If it is already done, may you need wait to try again.\n\n";
    print "<a href='http://".$GLOBALS['redirect_domain']."'>".$GLOBALS['redirect_domain']."</a>";
    print "</pre></body></html>";	

}
?>
