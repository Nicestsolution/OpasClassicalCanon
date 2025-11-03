<?php
$xmlPath = __DIR__ . '/canon.xml';
$dom = new DOMDocument();
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;
$dom->load($xmlPath);

$id = $_POST['ID'] ?? null;
if (!$id) {
  die("❌ Geen ID opgegeven.");
}

// Zoek de juiste <composer>
$xpath = new DOMXPath($dom);
$composerNodes = $xpath->query("//composer[ID='$id']");

if ($composerNodes->length === 0) {
  die("❌ Componist met ID '$id' niet gevonden.");
}

$composer = $composerNodes->item(0);

// Velden bijwerken
function updateNode($parent, $tag, $value) {
  $node = $parent->getElementsByTagName($tag)->item(0);
  if ($node) {
    $node->nodeValue = $value;
  }
}

updateNode($composer, 'name', $_POST['name'] ?? '');
updateNode($composer, 'familyname', $_POST['familyname'] ?? '');
updateNode($composer, 'style', $_POST['style'] ?? '');
updateNode($composer, 'placeofbirth', $_POST['placeofbirth'] ?? '');
updateNode($composer, 'yearofbirth', $_POST['yearofbirth'] ?? '');
updateNode($composer, 'placeofdeath', $_POST['placeofdeath'] ?? '');
updateNode($composer, 'yearofdeath', $_POST['yearofdeath'] ?? '');

// Narrative vervangen met CDATA
$narrativeNode = $composer->getElementsByTagName('narrative')->item(0);
if ($narrativeNode) {
  $newCdata = $dom->createCDATASection($_POST['narrative'] ?? '');
  while ($narrativeNode->firstChild) {
    $narrativeNode->removeChild($narrativeNode->firstChild);
  }
  $narrativeNode->appendChild($newCdata);
}

// Back-up maken
copy($xmlPath, __DIR__ . '/canon.backup.xml');

// Opslaan
$dom->save($xmlPath);
echo "<!DOCTYPE html>
<html lang='nl'>
<head>
  <meta charset='UTF-8'>
  <title>Opslaan voltooid</title>
  <meta http-equiv='refresh' content='2;url=index.html'>
  <style>
    body { font-family: sans-serif; text-align: center; padding: 3rem; }
    .message { font-size: 1.5rem; color: green; }
  </style>
</head>
<body>
  <p class='message'>✅ Wijzigingen opgeslagen. Je wordt teruggestuurd…</p>
</body>
</html>";

?>

