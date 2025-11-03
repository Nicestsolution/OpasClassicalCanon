<?php
// Pad naar de bron en doel
$source = __DIR__ . '/canon.xml';
$destination = dirname(__DIR__) . '/canon.xml';

// Back-up maken van bestaande root-versie (optioneel)
if (file_exists($destination)) {
  $backupName = dirname(__DIR__) . '/canon_backup_' . date('Ymd_His') . '.xml';
  copy($destination, $backupName);
}

// Kopieer de nieuwe versie
if (copy($source, $destination)) {
  echo "✅ canon.xml is gepubliceerd naar de rootmap.";
} else {
  echo "❌ Publicatie mislukt. Controleer bestandsrechten.";
}
?>
