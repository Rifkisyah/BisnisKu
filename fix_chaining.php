<?php

$files = glob(__DIR__ . '/tests/Browser/EP/*.php');
$search = "->script(\"const btn = document.querySelector('form:not([action*=\\\"locale\\\"]):not([action$=\\\"logout\\\"]) button[type=\\\"submit\\\"]'); if (btn) btn.click();\")";
$replace = "; \$browser->script(\"const btn = document.querySelector('form:not([action*=\\\"locale\\\"]):not([action$=\\\"logout\\\"]) button[type=\\\"submit\\\"]'); if (btn) btn.click();\"); \$browser";

foreach ($files as $file) {
    $content = file_get_contents($file);
    if (strpos($content, $search) !== false) {
        $content = str_replace($search, $replace, $content);
        file_put_contents($file, $content);
        echo "Fixed chaining in $file\n";
    }
}
