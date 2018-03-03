<?php

$caslangconstprefix = 'PHPCASSHIB_LANG_';
$caslangprefixlen = strlen('CASSHIB_Languages_');
$CASLANGUAGES = array ();

$consts = get_defined_constants(true);
foreach ($consts['user'] as $key => $value) {
    if (preg_match("/^$caslangconstprefix/", $key)) {
        $CASLANGUAGES[$value] = substr($value, $caslangprefixlen);
    }
}
if (empty($CASLANGUAGES)) {
    $CASLANGUAGES = array (PHPCASSHIB_LANG_ENGLISH => 'English',
                           PHPCASSHIB_LANG_FRENCH => 'French');
}
