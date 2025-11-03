<?php
//echo "Formulier ontvangen!";
//print_r($_POST);
//exit;
$xmlPath = __DIR__ . '/canon.xml';
$dom = new DOMDocument();
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;
$dom->load($xmlPath);
$playlistIDs = [
  'canon'     => '4DqqTg8GBv4Ea8CzmoKraI',
  'kerstmis'  => '6gDAHEQObs7pH0XR3h5J13',
  'wiegelied' => '2duZlB2x7ybq1OH9PwcXZX'
];

// Zorg dat <music> bestaat
$musicNode = $dom->getElementsByTagName('music')->item(0);
if (!$musicNode) {
  $musicNode = $dom->createElement('music');
  $dom->documentElement->appendChild($musicNode);
}

// Nieuwe <song> node
$song = $dom->createElement('song');
$song->appendChild($dom->createElement('ID', $_POST['ID'] ?? ''));

// Appenddate
$appenddate = $dom->createElement('appenddate');
$appenddate->appendChild($dom->createElement('year', $_POST['year'] ?? ''));
$appenddate->appendChild($dom->createElement('month', $_POST['month'] ?? ''));
$appenddate->appendChild($dom->createElement('day', $_POST['day'] ?? ''));
$song->appendChild($appenddate);

// Overige velden - speciale characters escaped
$playlist  = htmlspecialchars($_POST['playlist'] ?? 'Canon', ENT_XML1 | ENT_QUOTES, 'UTF-8');
$reference = htmlspecialchars($_POST['reference'] ?? '', ENT_XML1 | ENT_QUOTES, 'UTF-8');
$title     = htmlspecialchars($_POST['title'] ?? '', ENT_XML1 | ENT_QUOTES, 'UTF-8');
$artist    = htmlspecialchars($_POST['artist'] ?? '', ENT_XML1 | ENT_QUOTES, 'UTF-8');

$trackURL = trim($_POST['spotify'] ?? '');
$playlistName = trim($_POST['playlist'] ?? 'canon');
$playlistKey = strtolower($playlistNameRaw);
$playlistID = $playlistIDs[$playlistKey] ?? null;
if (!$playlistID) {
  echo "<p style='color:red;'>⚠️ Onbekende playlist: $playlistNameRaw</p>";
}

// Verwijder alles na het eerste vraagteken zoals trackingparameter zoals ?si=...
$cleanTrackURL = explode('?', $trackURL)[0];

if ($trackURL && $playlistID) {
  $deeplink = $cleanTrackURL . '?context=spotify:playlist:' . $playlistID;
} else {
  $deeplink = $cleanTrackURL; // fallback zonder context
}
$safeSpotify = htmlspecialchars($deeplink, ENT_XML1 | ENT_QUOTES, 'UTF-8');

$song->appendChild($dom->createElement('playlist', $playlist));
$song->appendChild($dom->createElement('reference', $reference));
$song->appendChild($dom->createElement('title', $title));
$song->appendChild($dom->createElement('artist', $artist));
$song->appendChild($dom->createElement('spotify', $safeSpotify));


// CDATA-velden
function addCdata($dom, $parent, $tag, $text) {
  $node = $dom->createElement($tag);
  $cdata = $dom->createCDATASection($text);
  $node->appendChild($cdata);
  $parent->appendChild($node);
}

addCdata($dom, $song, 'narrative', $_POST['narrative'] ?? '');
addCdata($dom, $song, 'original', $_POST['original'] ?? '');
addCdata($dom, $song, 'translation', $_POST['translation'] ?? '');

// Toevoegen aan <songs>
$musicNode->appendChild($song);

// Back-up maken
copy($xmlPath, __DIR__ . '/canon.backup.xml');

// Opslaan
$dom->save($xmlPath);

// Bevestiging + redirect
echo '<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <title>Song toegevoegd</title>
  <meta http-equiv="refresh" content="10;url=index.html">
  <style>
    body { font-family: sans-serif; text-align: center; padding: 3rem; }
    .message { font-size: 1.5rem; color: green; }
  </style>
</head>
<body>
  <p class="message">✅ Song succesvol toegevoegd. Je wordt teruggestuurd…</p>
  <p><a href="index.html">🔙 Terug naar Beheerpagina</a></p>
</body>
</html>';

?>
