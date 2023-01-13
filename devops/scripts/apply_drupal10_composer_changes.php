<?php

// Update pantheon.upstream.yml
$pantheonYmlContents = file_get_contents("pantheon.upstream.yml");
$pantheonYml = yaml_parse($pantheonYmlContents);
$originalPantheonYml = $pantheonYml;

print_r($pantheonYml);

if($pantheonYml['web_docroot'] != true) {
  print "Update pantheon.upstream.yml web_docroot to true\n";
  $pantheonYml['web_docroot'] = true;
} else {
  print "pantheon.upstream.yml web_docroot is already true\n";
}

if($pantheonYml['php_version'] != '8.2') {
  print "Update pantheon.upstream.yml php_version to 8.2\n";
  $pantheonYml['php_version'] = '8.2';
} else {
  print "pantheon.upstream.yml php_version is already 8.2\n";
}

if(serialize($pantheonYml) == serialize($originalPantheonYml)) {
  echo "No changes to pantheon.upstream.yml\n";
  return;
}

$prettyYml = yaml_emit($array, YAML_UTF8_ENCODING, YAML_LN_BREAK);
echo "== YML ==\n";
echo $prettyYml;
echo "== End YML ==\n";

$prettyYml = preg_replace('#^(\s+)(\w+):#m', '$1$2:', $prettyYml);

echo "== Pretty YML ==\n";
echo $prettyYml;
echo "== End Pretty YML ==\n";



exit;

// Update composer.json
$composerJsonContents = file_get_contents("composer.json");
$composerJson = json_decode($composerJsonContents, true);


// D10 versions
if($composerJson["require"]["drupal/core-composer-scaffold"] != "^10") {
  print "Update drupal/core-composer-scaffold to ^10\n";
  $composerJson["require"]["drupal/core-composer-scaffold"] = "^10";
}

if($composerJson["require"]["drupal/core-recommended"] != "^10") {
  print "Update drupal/core-recommended to ^10\n";
  $composerJson["require"]["drupal/core-recommended"] = "^10";
}

if($composerJson["require"]["pantheon-systems/drupal-integrations"] != "^10") {
  print "Update pantheon-systems/drupal-integrations to ^10\n";
  $composerJson["require"]["pantheon-systems/drupal-integrations"] = "^10";
}

if($composerJson["require-dev"]["drupal/core-dev"] != "^10") {
  print "Update drupal/core-dev to ^10\n";
  $composerJson["require-dev"]["drupal/core-dev"] = "^10";
}

if(! isset($composerJson['config']['allow-plugins']['phpstan/extension-installer'])) {
  print "Allow phpstan/extension-installer in preparation for Drupal 10\n";
  $composerJson['config']['allow-plugins']['phpstan/extension-installer'] = true;
}

if($composerJson['config']['platform']['php'] != '8.2.0') {
  print "Update PHP platform to 8.2.0\n";
  $composerJson['config']['platform']['php'] = '8.2.0';
}

// leave $composerJson['name'] and description alone - site will switch to drupal-composer-managed


if(serialize($composerJson) == serialize($originalComposerJson)) {
  echo "No changes to composer.json\n";
  return;
}

// Write the updated composer.json file
$prettyJson = json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
$prettyJson = preg_replace('#": \[\s*("[^"]*")\s*\]#m', '": [\1]', $prettyJson);

file_put_contents("composer.json", $prettyJson . PHP_EOL);




