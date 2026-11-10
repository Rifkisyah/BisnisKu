<?php

$files = glob(__DIR__ . '/tests/Browser/EP/*.php');
$search = "->click('form:not([action*=\"locale\"]):not([action$=\"logout\"]) button[type=\"submit\"]')";
$replace = "->script(\"const btn = document.querySelector('form:not([action*=\\\"locale\\\"]):not([action$=\\\"logout\\\"]) button[type=\\\"submit\\\"]'); if (btn) btn.click();\")";

foreach ($files as $file) {
    $content = file_get_contents($file);
    if (strpos($content, $search) !== false) {
        $content = str_replace($search, $replace, $content);
        file_put_contents($file, $content);
        echo "Replaced in $file\n";
    }
}
