<?php

if (isset($_POST['submit'])) {
    $start = $_POST["start"];
    $find = $_POST["find"];

    web_crawling($start);
}

//$links_visitados = array();
$visitando = array();

$cont = 0;


function web_crawling($url)
{
    $links_visitados = array();
    //global $links_visitados;
    global $visitando;
    global $find;
    global $cont;

    $options = array('http' => array('method' => "GET", 'headers' => "User-Agent: MiranhaBot/1.0\n"));
    $context = stream_context_create($options);
    $doc = new DOMDocument();
    @$doc->loadHTML(@file_get_contents($url, false, $context));

    $array_de_links = $doc->getElementsByTagName("a");

    foreach ($array_de_links as $link) {
        $l = $link->getAttribute("href");

        if (substr($l, 0, 1) == "/" && substr($l, 0, 2) != "//") {
            $l = parse_url($url)["scheme"] . "://" . parse_url($url)["host"] . $l;
        } else if (substr($l, 0, 2) == "//") {
            $l = parse_url($url)["scheme"] . ":" . $l;
        } else if (substr($l, 0, 2) == "./") {
            $l = parse_url($url)["scheme"] . "://" . parse_url($url)["host"] . dirname(parse_url($url)["path"]) . substr($l, 1);
        } else if (substr($l, 0, 1) == "#") {
            $l = parse_url($url)["scheme"] . "://" . parse_url($url)["host"] . parse_url($url)["path"] . $l;
        } else if (substr($l, 0, 3) == "../") {
            $l = parse_url($url)["scheme"] . "://" . parse_url($url)["host"] . "/" . $l;
        } else if (substr($l, 0, 11) == "javascript:") {
            continue;
        } else if (substr($l, 0, 5) != "https" && substr($l, 0, 4) != "http") {
            $l = parse_url($url)["scheme"] . "://" . parse_url($url)["host"] . "/" . $l;
        }

        // Colocar os links que não foram visitados no array de links a serem visitados
        if (!in_array($l, $links_visitados)) {
            $links_visitados[] = $l;
            $visitando[] = $l;

            echo "==> " . $l . "\n";

            if (strcasecmp($l, $find) == 0) {
                echo "Encontrei <$find> na $cont ª iteração!" . "\n";
                exit(0);
            } else {
                $cont++;

                // Depois de 1000 links, sai do programa
                if ($cont == 1000) {
                    exit(0);
                }
            }
        }
    }

    // Retirar links repetidos  
    array_shift($visitando);

    // Entrar nos links dos links
    foreach ($visitando as $site) {
        web_crawling($site);
    }
}

//web_crawling($start);
