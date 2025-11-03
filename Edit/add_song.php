<?php
$xmlPath = __DIR__ . '/canon.xml';
$xml = simplexml_load_file($xmlPath);
if (!$xml) {
  die("❌ canon.xml kon niet worden geladen. Controleer het pad en de XML-structuur.");
}

// Stap 1: Componist geselecteerd?
$selectedID = $_GET['id'] ?? null;
$composer = null;
if ($selectedID) {
  foreach ($xml->composers->composer as $c) {
    if ((string)$c->ID === $selectedID) {
      $composer = $c;
      break;
    }
  }
}
//echo "<pre>";
//print_r($xml);
//echo "</pre>";
$composerList = [];
if (isset($xml->composers->composer)) {
	$composerList = [];
	foreach ($xml->composers->composer as $c) {
		$composerList[] = $c;
	}
	usort($composerList, function($a, $b) {
		return strcmp((string)$a->familyname, (string)$b->familyname);
	});
}
echo "Aantal componisten: " . count($composerList);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <title>Toevoegen muziek</title>
  <style>
    body { font-family: sans-serif; max-width: 800px; margin: auto; padding: 2rem; }
    form { display: grid; gap: 0.5rem; margin-top: 1rem; }
    input, textarea, button { padding: 0.5rem; font-size: 1rem; }
    textarea { height: 12rem; }
  </style>
</head>
<body>
  <h1>✏➕ Voeg nieuw muzieknummer toe</h1>

  <!-- Stap 1: Selectie -->
  <form method="post" action="save_new_song.php">
    <label for="id">Selecteer componist:</label>
	<select name="reference" required>
      <option value="">-- Kies een componist --</option>
		<?php foreach ($composerList as $c): ?>
			<option value="<?= $c->ID ?>"><?= $c->name ?> <?= $c->familyname ?></option>
		<?php endforeach; ?>
    </select>

	<h3>Nieuw muzieknummer toevoegen</h3>
	<input type="text" name="ID" placeholder="Song-ID (bv. 20251017)" required>
	<input type="text" name="year" placeholder="Jaar" required>
	<input type="text" name="month" placeholder="Maand" required>
	<input type="text" name="day" placeholder="Dag" required>
	<label for="playlist">Playlist:</label>
	<select name="playlist" id="playlist" required>
	  <option value="Canon">Canon</option>
	  <option value="Kerstmis">Kerstmis</option>
	  <option value="Wiegelied">Wiegelied</option>
	</select>
	<input type="text" name="title" placeholder="Titel" required>
	<input type="text" name="artist" placeholder="Artiest">
	<label for="spotify">
	  <img src="https://upload.wikimedia.org/wikipedia/commons/1/19/Spotify_logo_without_text.svg" alt="Spotify" style="height: 24px; vertical-align: middle; margin-right: 0.5rem;">
	  Spotify-link:
	</label>
	<input type="text" name="spotify" id="spotify" placeholder="Spotify track-URL" style="padding-left: 0.5rem; border: 1px solid #1DB954;">
	
	<div style="margin-bottom: 0.5rem;">
	  <button type="button" onclick="insertTag('<br>')">↩️ Voeg &lt;br&gt; toe</button>
	  <button type="button" onclick="insertTag('<b></b>')">🅱️ Voeg &lt;b&gt;&lt;/b&gt; toe</button>
	  <button type="button" onclick="insertTag('<i></i>')">👁️ Voeg &lt;i&gt;&lt;/i&gt; toe</button>
	</div>

	<textarea name="narrative" placeholder="Narratief"></textarea>
	<textarea name="original" placeholder="Originele tekst"></textarea>
	<textarea name="translation" placeholder="Vertaling"></textarea>

  <button type="submit">➕ Voeg song toe</button>
  </form>
<script>
	let lastFocusedTextarea = null;

	// Onthoud welke textarea laatst is aangeklikt
	document.querySelectorAll('textarea').forEach(textarea => {
	  textarea.addEventListener('focus', () => {
		lastFocusedTextarea = textarea;
		document.querySelectorAll('textarea').forEach(t => t.style.borderColor = '');
		textarea.style.borderColor = 'dodgerblue';
	  });
	});

	function insertTag(tag) {
	  if (!lastFocusedTextarea) {
		alert("Klik eerst in een tekstvak.");
		return;
	  }
	  const textarea = lastFocusedTextarea;
	  const start = textarea.selectionStart;
	  const end = textarea.selectionEnd;
	  const text = textarea.value;
	  textarea.value = text.slice(0, start) + tag + text.slice(end);
	  textarea.focus();
	  textarea.selectionStart = textarea.selectionEnd = start + tag.length;
	}
</script>

</body>
</html>

