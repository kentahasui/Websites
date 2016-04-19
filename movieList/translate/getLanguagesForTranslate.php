<?php
include("translatorObjects.php");
echo "hello";
$translator = new Translator();
echo "starting get languages";
echo $translator->getLanguagesForTranslate();

?>