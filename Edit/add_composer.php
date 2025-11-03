<?php
$xmlPath = __DIR__ . '/canon.xml';
$xml = simplexml_load_file($xmlPath);
if (!$xml) {
  die("❌ canon.xml kon niet worden geladen. Controleer het pad en de XML-structuur.");
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <title>➕ Nieuwe componist toevoegen</title>
  <style>
    body { font-family: sans-serif; max-width: 800px; margin: auto; padding: 2rem; }
    form { display: grid; gap: 0.5rem; margin-top: 1rem; }
    input, textarea, button { padding: 0.5rem; font-size: 1rem; }
    textarea { height: 12rem; }
  </style>
</head>
<body>
	<h1>➕ Voeg nieuwe componist toe</h1>

	 <form method="post" action="save_composer_new.php">
		<input type="text" name="ID" placeholder="Unieke ID (bijv. bach1685)" required>
		<input type="text" name="name" placeholder="Voornaam" required>
		<input type="text" name="familyname" placeholder="Achternaam" required>
		<input type="text" name="style" placeholder="Stijl">
		<input type="text" name="placeofbirth" placeholder="Geboorteplaats">
		<input type="number" name="yearofbirth" placeholder="Geboortejaar">
		<input type="text" name="placeofdeath" placeholder="Sterfplaats">
		<input type="number" name="yearofdeath" placeholder="Sterfjaar">
		<div style="margin-bottom: 0.5rem;">
		  <button type="button" onclick="insertTag('<br>')">↩️ Voeg &lt;br&gt; toe</button>
		  <button type="button" onclick="insertTag('<b></b>')">🅱️ Voeg &lt;b&gt;&lt;/b&gt; toe</button>
		  <button type="button" onclick="insertTag('<i></i>')">👁️ Voeg &lt;i&gt;&lt;/i&gt; toe</button>
		</div>
		<textarea name="narrative" placeholder="Narratief"></textarea>
		<button type="submit">➕ Toevoegen</button>
	 </form>

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
