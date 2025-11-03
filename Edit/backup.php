<?php
$source = __DIR__ . '/canon.xml';
$backup = __DIR__ . '/canon.backup.xml';

if (!file_exists($source)) {
  echo "❌ canon.xml niet gevonden.";
  exit;
}

if (copy($source, $backup)) {
  echo "✅ canon.xml is geback-upt als canon.backup.xml.";
} else {
  echo "❌ Back-up mislukt. Controleer bestandsrechten.";
}
?>
