<?php
$xmlPath = __DIR__ . '/canon.xml';
$dom = new DOMDocument();
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;
$dom->load($xmlPath);

// Zoek of <composers> bestaat
$xpath = new DOMXPath($dom);
$composersNode = $xpath->query('//composers')->item(0);
if (!$composersNode) {
  $composersNode = $dom->createElement('composers');
  $dom->documentElement->appendChild($composersNode);
}

// Nieuwe componist
$new = $dom->createElement('composer');
$new->appendChild($dom->createElement('ID', $_POST['ID']));
$new->appendChild($dom->createElement('name', $_POST['name']));
$new->appendChild($dom->createElement('familyname', $_POST['familyname']));
$new->appendChild($dom->createElement('style', $_POST['style']));
$new->appendChild($dom->createElement('placeofbirth', $_POST['placeofbirth']));
$new->appendChild($dom->createElement('yearofbirth', $_POST['yearofbirth']));
$new->appendChild($dom->createElement('placeofdeath', $_POST['placeofdeath']));
$new->appendChild($dom->createElement('yearofdeath', $_POST['yearofdeath']));

// Narrative als CDATA
$narrative = $dom->createElement('narrative');
$cdata = $dom->createCDATASection($_POST['narrative']);
$narrative->appendChild($cdata);
$new->appendChild($narrative);

// Toevoegen aan <composers>
$composersNode->appendChild($new);

// Back-up maken
copy($xmlPath, __DIR__ . '/canon.backup.xml');

// Opslaan
$dom->save($xmlPath);

// Bevestiging
echo "<!DOCTYPE html>
<html lang='nl'>
<head>
  <meta charset='UTF-8'>
  <title>Componist toegevoegd</title>
  <meta http-equiv='refresh' content='2;url=index.html'>
  <style>
    body { font-family: sans-serif; text-align: center; padding: 3rem; }
    .message { font-size: 1.5rem; color: green; }
  </style>
</head>
<body>
  <p class='message'>✅ Componist succesvol toegevoegd. Je wordt teruggestuurd…</p>
</body>
</html>";
?>
