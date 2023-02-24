#!/bin/bash

# This script is pretty tailored to assuming it's running a fresh git clone.

# Check github authentication; ignore status code 1 returned from this command
ssh -T git@github.com

# Fail fast on any future errors.
set -euo pipefail

## I don't think we need to check out $CIRCLE_BRANCH ?
# git checkout "${CIRCLE_BRANCH}"

# copy our script so it's available to run after we change branches
cp devops/scripts/apply_drupal10_composer_changes.php /tmp

# add the upstream repos as remotes - we need the 'public' upstream because d10 is based on it
git remote add public "$UPSTREAM_REPO_REMOTE_URL"
git fetch public

git remote add drupal-10-start "$DRUPAL_10_REPO_REMOTE_URL"
git fetch drupal-10-start

# the D10 'start state' upstream uses pantheon-upstreams/drupal-composer-managed as a starting point
git checkout -b public --track public/main

# but let's create a new branch to work on
git checkout -b drupal-10

# run a php script to update to the drupal 10 start state
# put ^10 in the relevant places in composer.json
php /tmp/apply_drupal10_composer_changes.php

# apply transformations coded into our pre-update-cmd script
composer run-script pre-update-cmd

# commit the changes
git commit -am "Create new sites with Drupal 10" --author='Pantheon Automation <bot@getpantheon.com>'

# We need to rewrite history on the D10 upstream to keep the commit SHAs the same,
# so that newly created sites don't see the diverged commits from the D9 upstream as
# updates it needs to apply
git push --force drupal-10-start public:main
