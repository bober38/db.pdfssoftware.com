<?php

if (empty($lang)) $lang='EN';
if (empty($btn)) $btn="button_download.png";
if (preg_match('/^(.+)\.(.+)$/', $btn, $ar)) {
    $btn=$ar[1];
    $ext=$ar[2];
}
else {
    $ext='jpg';
}
switch ($lang) {
    case 'FR':
        $btn_getstarted="/download/images/FR-$btn.$ext";
        $btn_getstarted_hover="/download/images/FR-{$btn}_on.$ext";
        break;
    case 'PT':
        $btn_getstarted="/download/images/PT-$btn.$ext";
        $btn_getstarted_hover="/download/images/PT-{$btn}_on.$ext";
        break;
    case 'ES':
        $btn_getstarted="/images/ES-$btn.$ext";
        $btn_getstarted_hover="/images/ES-{$btn}_on.$ext";
        break;
    case 'IT':
        $btn_getstarted="/images/IT-$btn.$ext";
        $btn_getstarted_hover="/images/IT-{$btn}_on.$ext";
        break;
    case 'NL':
        $btn_getstarted="/images/NL-$btn.$ext";
        $btn_getstarted_hover="/images/NL-{$btn}_on.$ext";
        break;
    case 'SV':
        $btn_getstarted="/images/SV-$btn.$ext";
        $btn_getstarted_hover="/images/SV-{$btn}_on.$ext";
        break;
    case 'DE':
        $btn_getstarted="/images/DE-$btn.$ext";
        $btn_getstarted_hover="/images/DE-{$btn}_on.$ext";
        break;
    case 'NO':
        $btn_getstarted="/images/NO-$btn.$ext";
        $btn_getstarted_hover="/images/NO-{$btn}_on.$ext";
        break;
    case 'EN':
    default:
        $btn_getstarted="/images/$btn.$ext";
        $btn_getstarted_hover="/images/{$btn}_on.$ext";
        break;
}
  echo '
// PRELOADING IMAGES
if (document.images) {
 img_on =new Image();  img_on.src ="'.$btn_getstarted_hover.'";
 img_off=new Image();  img_off.src="'.$btn_getstarted.'";
}

function handleOver() {
 if (document.images) document.download.src=img_on.src;
}

function handleOut() {
 if (document.images) document.download.src=img_off.src;
}';
?>
