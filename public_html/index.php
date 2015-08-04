<?php

$uptime = shell_exec("cut -d. -f1 /proc/uptime");

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$count_24h = $redis->eval('return table.getn(redis.call("keys", "24h_*"))');
$count_ever = $redis->eval('return table.getn(redis.call("keys", "ever_*"))');

?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>REDIRECT.CENTER</title>
  <meta name="description" content="DNS Redirect, Domain redirects with CNAME, how to redirect"/>
  <meta name="author" content="Udlei Nati / udlei@nati.biz">
  <link href='http://fonts.googleapis.com/css?family=Open+Sans:300,400|Inconsolata:400' rel='stylesheet' type='text/css'>
  <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
  <script type="text/javascript">
  function activeEn() {
    $('.change').removeClass('bold');
    $('.change.to-en').addClass('bold');
    $('.en').show();
    $('.pt-br').hide();
    location.hash = 'en';
  }
  function activePtBr() {
    $('.change').removeClass('bold');
    $('.change.to-pt-br').addClass('bold');
    $('.en').hide();
    $('.pt-br').show();
    location.hash = 'pt-br';
  }
  $(document).ready(function() {
      if(location.hash == '#en') {
        activeEn();
      } else if(location.hash == '#pt-br') {
        activePtBr();
      };
      $('.change').on('click',function() {
          if($(this).hasClass('to-en')) {
            activeEn();
          } else {
            activePtBr();
          }
      });
      doUptime();
  });
  var upSeconds=<?php echo $uptime ?>;
  function doUptime() {
      var uptimeString = "Server Uptime: ";
      var secs = parseInt(upSeconds % 60);
      var mins = parseInt(upSeconds / 60 % 60);
      var hours = parseInt(upSeconds / 3600 % 24);
      var days = parseInt(upSeconds / 86400);
      if (days > 0) {
          uptimeString += days;
          uptimeString += ((days == 1) ? " day" : " days");
      }
      if (hours > 0) {
          uptimeString += ((days > 0) ? ", " : "") + hours;
          uptimeString += ((hours == 1) ? " hour" : " hours");
      }
      if (mins > 0) {
          uptimeString += ((days > 0 || hours > 0) ? ", " : "") + mins;
          uptimeString += ((mins == 1) ? " minute" : " minutes");
      }
      if (secs > 0) {
          uptimeString += ((days > 0 || hours > 0 || mins > 0) ? ", " : "") + secs;
          uptimeString += ((secs == 1) ? " second" : " seconds");
      }
      var span_el = document.getElementById("uptime");
      var replaceWith = document.createTextNode(uptimeString);
      span_el.replaceChild(replaceWith, span_el.childNodes[0]);
      upSeconds++;
      setTimeout("doUptime()",1000);
  }
  </script>
  <script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-51158860-1', 'redirect.center');
    ga('send', 'pageview');
  </script>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
    <div id="top-bar">
        <div class="container">
            See too :
            <a href="http://redirect.center" class="redirect-center" title="redirect.center">redirect.center</a> 
            :
            <a href="http://csr.ninja" class="csr-ninja" title="csr.ninja">csr.ninja</a>
            :
            <a href="http://planilha.zone" class="planilha-zone" title="planilha.zone">planilha.zone</a>
        </div>
    </div>
    <div id="header">
        <div class="container">
            <h1><a href="/">REDIRECT.CENTER</a></h1>
            <h5><span id="uptime">...</span></h5>
            <p class="domains">
                 <span class="en">Domains using - last 24h <?php echo $count_24h ?> - ever <?php echo $count_ever ?></span>
                 <span class="pt-br">Dom&iacute;nios usando - &uacute;ltimas 24h <?php echo $count_24h ?> - desde sempre <?php echo $count_ever ?></span>
            </p>
            <div class="language">
                <a href="javascript:;" class="change to-en bold">english</a></span> . <a href="javascript:;" class="change to-pt-br">portugu&ecirc;s</a>
            </div>
        </div>
    </div>
    <div id="intro">
        <div class="container">
            <p class="en">
                Redirect a domain using only a DNS record.
            </p>
            <p class="pt-br">
                Redirecione um dom&iacute;nio usando apenas o DNS.
            </p>
        </div>
    </div>
    <div id="content" class="container">
        <p id="usage" class="title-h1">
            <a href="#usage" class="en title-h1">Usage</a>
            <a href="#usage" class="pt-br title-h1">Uso</a>
        </p>
        <p id="overview">
            <span class="en">
            Point a domain to the <span class="redirect-center">redirect.center</span> server
            and <span class="redirect-center">redirect.center</span> will perform a DNS lookup and
            redirect the user to your specified destination. Throughout this documentation 
            "nati.biz" will be used as a placeholder for your domain, you can visit 
            any nati.biz example to see the redirect in action. 
            </span>
            <span class="pt-br">
            Aponte um dom&iacute;nio para o servidor do <span class="redirect-center">redirect.center</span>
            e o <span class="redirect-center">redirect.center</span> far&aacute; o redirecionamento
            do usu&aacute;rio para o destino especificado. No decorrer desta documentação o dom&iacute;nio 
            "nati.biz" ser&aacute; usado como exemplo, voc&ecirc; pode visitar todos os exemplos para ver o 
            redirecionamento em a&ccedil;&atilde;o.
            </span>
        </p>
        <h2 id="usage:cname"><a href="#usage:cname">CNAME</a></h2>
        <p>
            <span class="en">
            Specify the destination domain as a subdomain of nati.biz. A 
            simple subdomain redirect of <a href="http://google.nati.biz">google.nati.biz</a>
            to github.com:
            </span>
            <span class="pt-br">
            Especifique o dom&iacute;nio destino como um subdom&iacute;nio de nati.biz. Um redirecionamento
            simples de subdom&iacute;nio do <a href="http://google.nati.biz">google.nati.biz</a>
            para github.com:
            </span>
        </p>
        <code>
            google.nati.biz. CNAME google.com.redirect.center.
        </code>

        <h3 id="options"><a href="#options"><span class="en">Options</span><span class="pt-br">Opções</span></a></h3>
        <p>
            <span class="en">
            Options can be specified as part of a cname. 
            For example to redirect www.oldwebsite.com to www.newwebsite.com with 
            a 302 status code:
            </span>
            <span class="pt-br">
            Opções podem ser usadas como parte do cpanem.
            Por exemplo para redirecionar www.oldwebsite.com para www.newwebsite.com com
            status code 302:
            </span>
        </p>
        <code>
            www.oldwebsite.com. CNAME www.newwebsite.com.opts-statuscode-302.redirect.center
        </code>

        <p>
            <span class="en">
            Example to redirect with a path:
            </span>
            <span class="pt-br">
            Exemplo para redirecionar com o path:
            </span>
        </p>
        <code>
            www.oldwebsite.com. CNAME www.newwebsite.com.opts-uri.redirect.center
        </code>

        <p>
            <span class="en">
            Example to redirect for a different path:
            </span>
            <span class="pt-br">
            Exemplo para redirecionar para outro path:
            </span>
        </p>
        <code>
            www.oldwebsite.com. CNAME www.newwebsite.com.slash.contact.redirect.center
        </code>

        <p>
            <span class="en">
            <span class="redirect-center">redirect.center</span> provides options to allow
            for the flexibility most situations will need.
            </span>
            <span class="pt-br">
            <span class="redirect-center">redirect.center</span> tem opções para permitir os tipos
            mais flexíveis de situações que você precisará.
            </span>
        </p>

        <table id="options-table">
            <tr>
                <th><span class="en">Option</span><span class="pt-br">Opções</span></th>
                <th><span class="en">Description</span><span class="pt-br">Descrição</span></th>
            </tr>
            <tr>
                <td>opts-statuscode-{code}</td>
                <td>
                    <span class="en">
                    HTTP Status Code to be used in the redirect. 
                    </span>
                    <span class="pt-br">
                    HTTP Status Code usado para o redirecionamento.
                    </span>
                    <strong>302</strong>, <a href="http://httpstatus.es">HTTP Status Code</a></td>
            </tr>
            <tr>
                <td>opts-uri</td>
                <td>
                <span class="en">
                Append URI (if any) to the target URL
                </span>
                <span class="pt-br">
                Adiciona a URI (se existir) na URL de redirecionamento.
                </span>
                </td>
            </tr>
        </table>
        <h2 id="usage:a"><a href="#usage:a">A Record</a></h2>
        <p>
            <span class="en">
            A root domain (eg: nati.biz) cannot be a CNAME, a workaround for
            this is supported: point the A record for the root domain to the 
            <span class="redirect-center">redirect.center</span> server (54.84.55.102) 
            and then create a CNAME matching the root domain (using CNAME or TXT 
            options as described above). For example to redirect 
            <a href="http://nati.biz">nati.biz</a> to www.nati.biz:
            </span>
            <span class="pt-br">
            O dom&iacute;nio principal (ex: nati.biz) n&atilde;o pode ser do tipo CNAME, a alternativa
            para suportar o redirecionamento: apontar o dom&iacute;nio principal com tipo A para o IP do 
            <span class="redirect-center">redirect.center</span> (54.84.55.102) 
            e criar uma entrada do tipo CNAME apontando para o lugar que deseja redirecionar.
            Por exemplo para redirecionar <a href="http://nati.biz">nati.biz</a> para www.nati.biz:
            </span>
        </p>
        <code>
            <span class="code-sub">A</span> nati.biz. IN A 54.84.55.102            <br/>
            <span class="code-sub">CNAME</span> redirect.nati.biz. CNAME www.nati.biz.redirect.center.        
        </code>
    </div>
    <div id="footer">
        <div class="container">
            <p id="about" class="title-h1">
                <a href="#about" class="en title-h1">About</a>
                <a href="#about" class="pt-br title-h1">Sobre</a>
            </p>
            <p class="en">
                <span class="redirect-center">REDIRECT.CENTER</span> is 
                <a href="https://github.com/unattis/redirect.center">open source</a>, code contributions, 
                general feedback and ideas are greatly appreciated via either 
                the <a href="https://github.com/unattis/redirect.center/issues">GitHub issues</a>, 
                <a href="mailto:udlei@nati.biz">email</a> (udlei@nati.biz). 
            </p>
            <p class="pt-br">
                <span class="redirect-center">REDIRECT.CENTER</span> é 
                <a href="https://github.com/unattis/redirect.center">open source</a>, code contributions, 
                feedback no geral de idéias s&atilde;o muito bem vindas, postar via
                <a href="https://github.com/unattis/redirect.center/issues">GitHub issues</a>, 
                <a href="mailto:udlei@nati.biz">email</a> (udlei@nati.biz).
            </p>
            <ul class="google-keywords">
                <li>Leiten Sie von DNS, Domain-Weiterleitung, Umleitung dns cname</li>
                <li>redirigida por dns, redireccionamiento de dominio, redirigir cname dns</li>
                <li>Перенаправление на DNS, домен перенаправления, перенаправлять DNS CNAME</li>
                <li>إعادة توجيه من قبل نظام أسماء النطاقات، إعادة توجيه المجال توجيه DNS CNAME</li>
                <li>הפניה על ידי DNS, הפניה תחום, להפנות CNAME DNS</li>
                <li>डीएनएस, डोमेन अनुप्रेषित द्वारा पुन: निर्देशित DNS CNAME अनुप्रेषित</li>
                <li>DNS, 도메인 리디렉션으로 리디렉션의 DNS CNAME 리디렉션</li>
                <li>DNSは、ドメインリダイレクトによってリダイレクト、DNSのCNAMEのリダイレクト</li>
                <li>通过DNS，域名重定向重定向，重定向DNS CNAME</li>
                <li>通過DNS，域名重定向重定向，重定向DNS CNAME</li>
            </ul>
        </div>
    </div>
</body>
</html>
