<?php

// Update pantheon.upstream.yml
$pantheonYmlContents = file_get_contents("pantheon.upstream.yml");


print "Update pantheon.upstream.yml php_version to 8.2\n";
$pantheonYmlContents = preg_replace('#^\s*php_version:.*#m', 'php_version: 8.2', $pantheonYmlContents);

file_put_contents("pantheon.upstream.yml", $pantheonYmlContents);


// Update composer.json
$composerJsonContents = file_get_contents("composer.json");
$composerJson = json_decode($composerJsonContents, true);
$originalComposerJson = $composerJson;

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

// leave $composerJson['name'] and description alone - site will switch to drupal-composer-managed

if(serialize($composerJson) == serialize($originalComposerJson)) {
  echo "No changes to composer.json\n";
  return;
}

// Write the updated composer.json file
$prettyJson = json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
$prettyJson = preg_replace('#": \[\s*("[^"]*")\s*\]#m', '": [\1]', $prettyJson);

file_put_contents("composer.json", $prettyJson . PHP_EOL);
