<?php
$xmlPath = __DIR__ . '/canon.xml';
$xml = simplexml_load_file($xmlPath);
if (!$xml) {
  die("❌ canon.xml kon niet worden geladen. Controleer het pad en de XML-structuur.");
}

// Stap 1: Componisten alfabetisch sorteren
$composerList = [];
if (isset($xml->composers->composer)) {
  foreach ($xml->composers->composer as $c) {
    $composerList[] = $c;
  }
  usort($composerList, function($a, $b) {
    return strcmp((string)$a->familyname, (string)$b->familyname);
  });
}

// Stap 2: Componist geselecteerd?
$selectedID = $_GET['id'] ?? null;
$selectedSongID = $_GET['songID'] ?? null;
$composer = null;
$songsByComposer = [];

if ($selectedID) {
  foreach ($xml->composers->composer as $c) {
    if ((string)$c->ID === $selectedID) {
      $composer = $c;
      break;
    }
  }

  // Stap 3: Songs van deze componist verzamelen
  foreach ($xml->music->song as $s) {
    if ((string)$s->reference === $selectedID) {
      $songsByComposer[] = $s;
    }
  }
}

echo "<h1>🎼 Bewerk bestaand muzieknummer</h1>";
echo "<form method='get' action='edit_song.php'>";

// Dropdown: componist
echo "<label for='id'>Selecteer componist:</label>";
echo "<select name='id' onchange='this.form.submit()'>";
echo "<option value=''>-- Kies een componist --</option>";
foreach ($composerList as $c) {
  $id = (string)$c->ID;
  $name = (string)$c->name . ' ' . (string)$c->familyname;
  $selected = ($selectedID === $id) ? 'selected' : '';
  echo "<option value='$id' $selected>$name</option>";
}
echo "</select>";

// Dropdown: song (indien componist gekozen)
if ($composer && count($songsByComposer) > 0) {
  echo "<br><label for='songID'>Selecteer muzieknummer:</label>";
  echo "<select name='songID' onchange='this.form.submit()'>";
  echo "<option value=''>-- Kies een muzieknummer --</option>";
  foreach ($songsByComposer as $s) {
    $id = (string)$s->ID;
    $title = (string)$s->title;
    $append = $s->appenddate ?? null;
	$year  = $append ? (string)$append->year : '';
	$month = $append ? (string)$append->month : '';
	$day   = $append ? (string)$append->day : '';
	$date = "{$year}/{$month}/{$day}";
    $selected = ($selectedSongID === $id) ? 'selected' : '';
    echo "<option value='$id' $selected>$title ($date)</option>";
  }
  echo "</select>";
}
echo "</form>";
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <title>Bewerk muzieknummer</title>
  <style>
    body { font-family: sans-serif; max-width: 800px; margin: auto; padding: 2rem; }
    form { display: grid; gap: 0.5rem; margin-top: 1rem; }
    input, textarea, button { padding: 0.5rem; font-size: 1rem; }
    textarea { height: 12rem; }
  </style>
</head>
<body>

	<?php
	if ($selectedSongID) {
	  // Zoek de song in XML
	  $song = null;
	  foreach ($xml->music->song as $s) {
		if ((string)$s->ID === $selectedSongID) {
		  $song = $s;
		  break;
		}
	  }

	  if ($song):
	?>
	<form method="post" action="save_song_edit.php">
	  <input type="hidden" name="ID" value="<?= htmlspecialchars($song->ID) ?>">

	  <label for="playlist">Playlist:</label>
	  <select name="playlist" id="playlist" required>
		<?php
		$playlists = ['Canon', 'Kerstmis', 'Wiegelied'];
		foreach ($playlists as $p):
		  $selected = ((string)$song->playlist === $p) ? 'selected' : '';
		?>
		  <option value="<?= $p ?>" <?= $selected ?>><?= $p ?></option>
		<?php endforeach; ?>
	  </select>

	  <label for="title">Titel:</label>
	  <input type="text" name="title" value="<?= htmlspecialchars($song->title) ?>" required>

	  <label for="artist">Artiest:</label>
	  <input type="text" name="artist" value="<?= htmlspecialchars($song->artist) ?>">

		<?php
		$append = $song->appenddate ?? null;
		$year  = $append ? (string)$append->year : '';
		$month = $append ? (string)$append->month : '';
		$day   = $append ? (string)$append->day : '';
		?>

		<label for="year">Jaar:</label>
		<input type="text" name="year" value="<?= htmlspecialchars($year) ?>" required>

		<label for="month">Maand:</label>
		<input type="text" name="month" value="<?= htmlspecialchars($month) ?>" required>

		<label for="day">Dag:</label>
		<input type="text" name="day" value="<?= htmlspecialchars($day) ?>" required>

	  <label for="reference">Componist-ID:</label>
	  <input type="text" name="reference" value="<?= htmlspecialchars($song->reference) ?>" required>

	  <label for="spotify">
		<img src="https://upload.wikimedia.org/wikipedia/commons/1/19/Spotify_logo_without_text.svg" alt="Spotify" style="height: 24px; vertical-align: middle; margin-right: 0.5rem;">
		Spotify-link:
	  </label>
	  <input type="text" name="spotify" value="<?= htmlspecialchars($song->spotify) ?>" placeholder="Spotify track-URL">
		
		<div style="margin-bottom: 0.5rem;">
		  <button type="button" onclick="insertTag('<br>')">↩️ Voeg &lt;br&gt; toe</button>
		  <button type="button" onclick="insertTag('<b></b>')">🅱️ Voeg &lt;b&gt;&lt;/b&gt; toe</button>
		  <button type="button" onclick="insertTag('<i></i>')">👁️ Voeg &lt;i&gt;&lt;/i&gt; toe</button>
		</div>
		
	  <label for="narrative">Narratief:</label>
	  <textarea name="narrative"><?= htmlspecialchars($song->narrative) ?></textarea>

	  <label for="original">Originele tekst:</label>
	  <textarea name="original"><?= htmlspecialchars($song->original) ?></textarea>

	  <label for="translation">Vertaling:</label>
	  <textarea name="translation"><?= htmlspecialchars($song->translation) ?></textarea>

	  <button type="submit">💾 Bewaar wijzigingen</button>
	</form>
	<?php
	  else:
		echo "<p style='color:red;'>❌ Song-ID niet gevonden.</p>";
	  endif;
	}
	?>

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

