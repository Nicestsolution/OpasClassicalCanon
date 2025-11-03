<?php
$xmlPath = __DIR__ . '/canon.xml';
$dom = new DOMDocument();
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;
$dom->load($xmlPath);
$xml = simplexml_import_dom($dom);

if (!$xml) {
  die("❌ canon.xml kon niet worden geladen.");
}

// Stap 1: Playlist-ID's voor deeplink
$playlistIDs = [
  'canon'     => '4DqqTg8GBv4Ea8CzmoKraI',
  'kerstmis'  => '6gDAHEQObs7pH0XR3h5J13',
  'wiegelied' => '2duZlB2x7ybq1OH9PwcXZX'
];

// Stap 2: Gegevens uit POST
$id         = $_POST['ID'] ?? '';
$playlist   = $_POST['playlist'] ?? 'Canon';
$reference  = $_POST['reference'] ?? '';
$title      = $_POST['title'] ?? '';
$artist     = $_POST['artist'] ?? '';
$year       = $_POST['year'] ?? '';
$month      = $_POST['month'] ?? '';
$day        = $_POST['day'] ?? '';
$spotifyRaw = $_POST['spotify'] ?? '';
$narrative  = $_POST['narrative'] ?? '';
$original   = $_POST['original'] ?? '';
$translation= $_POST['translation'] ?? '';

// Stap 3: Deeplink genereren
$playlistKey = strtolower($playlist);
$playlistID = $playlistIDs[$playlistKey] ?? null;
$cleanSpotify = explode('?', trim($spotifyRaw))[0];
$deeplink = ($cleanSpotify && $playlistID)
  ? $cleanSpotify . '?context=spotify:playlist:' . $playlistID
  : $cleanSpotify;

// Stap 4: Song zoeken en overschrijven
$song = null;
foreach ($xml->music->song as $s) {
  if ((string)$s->ID === $id) {
    $song = $s;
    break;
  }
}

if (!$song) {
  die("❌ Song-ID '$id' niet gevonden.");
}
$songDom = dom_import_simplexml($song);

// Stap 5: Velden bijwerken (escape waar nodig)
$song->playlist    = htmlspecialchars($playlist, ENT_XML1 | ENT_QUOTES, 'UTF-8');
$song->reference   = htmlspecialchars($reference, ENT_XML1 | ENT_QUOTES, 'UTF-8');
$song->title       = htmlspecialchars($title, ENT_XML1 | ENT_QUOTES, 'UTF-8');
$song->artist      = htmlspecialchars($artist, ENT_XML1 | ENT_QUOTES, 'UTF-8');
$song->spotify     = htmlspecialchars($deeplink, ENT_XML1 | ENT_QUOTES, 'UTF-8');

// Verwijder bestaande appenddate en CDATA-nodes
unset($song->year, $song->month, $song->day, $song->appenddate);
foreach (['narrative', 'original', 'translation'] as $tag) {
  $existing = $songDom->getElementsByTagName($tag);
  if ($existing->length > 0) {
    $songDom->removeChild($existing->item(0));
  }
}

// Voeg nieuwe appenddate toe
$appenddate = $song->appenddate ?: $song->addChild('appenddate');
$appenddate->addChild('year', htmlspecialchars($year, ENT_XML1 | ENT_QUOTES, 'UTF-8'));
$appenddate->addChild('month', htmlspecialchars($month, ENT_XML1 | ENT_QUOTES, 'UTF-8'));
$appenddate->addChild('day', htmlspecialchars($day, ENT_XML1 | ENT_QUOTES, 'UTF-8'));

// CDATA-velden mogen ongeëscaped blijven
function addCdata($dom, $parent, $tag, $text) {
  $node = $dom->createElement($tag);
  $cdata = $dom->createCDATASection($text);
  $node->appendChild($cdata);
  $parent->appendChild($node);
}

addCdata($dom, $songDom, 'narrative', $_POST['narrative'] ?? '');
addCdata($dom, $songDom, 'original', $_POST['original'] ?? '');
addCdata($dom, $songDom, 'translation', $_POST['translation'] ?? '');


// Stap 6: Opslaan
$xml->asXML($xmlPath);

// Stap 7: Bevestiging
echo "<!DOCTYPE html>
<html lang='nl'>
<head>
  <meta charset='UTF-8'>
  <title>Song bewerkt</title>
  <meta http-equiv='refresh' content='5;url=edit_song.php'>
  <style>
    body { font-family: sans-serif; text-align: center; padding: 3rem; }
    .message { font-size: 1.5rem; color: green; }
  </style>
</head>
<body>
  <p class='message'>✅ Song succesvol bewerkt. Je wordt teruggestuurd…</p>
  <p><a href='index.html'>🔙 Terug naar Beheerpagina</a></p>
</body>
</html>";
?>
