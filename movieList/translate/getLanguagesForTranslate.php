<?php
include 'translatorObjects.php';

$translator = new Translator();
echo json_encode($translator->getLanguagesForTranslate());

?>