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
  <title>Bewerk componist</title>
  <style>
    body { font-family: sans-serif; max-width: 800px; margin: auto; padding: 2rem; }
    form { display: grid; gap: 0.5rem; margin-top: 1rem; }
    input, textarea, button { padding: 0.5rem; font-size: 1rem; }
    textarea { height: 12rem; }
  </style>
</head>
<body>
  <h1>✏️ Bewerk bestaande componist</h1>

  <!-- Stap 1: Selectie -->
  <form method="get">
    <label for="id">Selecteer componist:</label>
    <select name="id" id="id" onchange="this.form.submit()">
      <option value="">-- Kies een componist --</option>
		<?php foreach ($composerList as $c): ?>
			<option value="<?= $c->ID ?>" <?= ($selectedID === (string)$c->ID) ? 'selected' : '' ?>>
				<?= $c->name ?> <?= $c->familyname ?>
			</option>

		<?php endforeach; ?>

    </select>
  </form>

  <!-- Stap 2: Bewerken -->
  <?php if ($composer): ?>
    <form method="post" action="save_composer_edit.php">
	<p><strong>ID:</strong> <?= $composer->ID ?></p>
		<input type="hidden" name="ID" value="<?= $composer->ID ?>">
      <input type="text" name="name" value="<?= $composer->name ?>" placeholder="Voornaam">
      <input type="text" name="familyname" value="<?= $composer->familyname ?>" placeholder="Achternaam">
      <input type="text" name="style" value="<?= $composer->style ?>" placeholder="Stijl">
      <input type="text" name="placeofbirth" value="<?= $composer->placeofbirth ?>" placeholder="Geboorteplaats">
      <input type="number" name="yearofbirth" value="<?= $composer->yearofbirth ?>" placeholder="Geboortejaar">
      <input type="text" name="placeofdeath" value="<?= $composer->placeofdeath ?>" placeholder="Sterfplaats">
      <input type="number" name="yearofdeath" value="<?= $composer->yearofdeath ?>" placeholder="Sterfjaar">
		<div style="margin-bottom: 0.5rem;">
			<button type="button" onclick="insertTag('<br>')">↩️ Voeg &lt;br&gt; toe</button>
			<button type="button" onclick="insertTag('<b></b>')">🅱️ Voeg &lt;b&gt;&lt;/b&gt; toe</button>
			<button type="button" onclick="insertTag('<i></i>')">👁️ Voeg &lt;i&gt;&lt;/i&gt; toe</button>
		</div>

      <textarea name="narrative" placeholder="Narratief"><?= $composer->narrative ?></textarea>
      <button type="submit">💾 Opslaan</button>
    </form>
  <?php endif; ?>
<script>
function insertTag(tag) {
  const textarea = document.querySelector('textarea[name="narrative"]');
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
