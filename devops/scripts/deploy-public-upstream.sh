#!/bin/bash
# This script is pretty tailored to assuming it's running in the CircleCI environment / a fresh git clone.
# It mirrors most commits from `pantheon-systems/drupal-composer-managed:release` to `pantheon-upstreams/drupal-composer-managed`.

# Check github authentication; ignore status code 1 returned from this command
ssh -T git@github.com

# Fail fast on any future errors.
set -euo pipefail

. devops/scripts/commit-type.sh

git remote add public "$UPSTREAM_REPO_REMOTE_URL"
git fetch public

git checkout "${CIRCLE_BRANCH}"

echo
echo "-----------------------------------------------------------------------"
echo "Preparing to release to upstream org"
echo "-----------------------------------------------------------------------"
echo

# List commits between release-pointer and HEAD, in reverse
newcommits=$(git log release-pointer..HEAD --reverse --pretty=format:"%h")
commits=()

# Identify commits that should be released
for commit in $newcommits; do
  commit_type=$(identify_commit_type "$commit")
  if [[ $commit_type == "normal" ]] ; then
    commits+=($commit)
  fi

  if [[ $commit_type == "mixed" ]] ; then
    2>&1 echo "Commit ${commit} contains both release and nonrelease changes. Cannot proceed."
    exit 1
  fi
done

# If nothing found to release, bail without doing anything.
if [[ ${#commits[@]} -eq 0 ]] ; then
  echo "No new commits found to release"
  echo "https://i.kym-cdn.com/photos/images/newsfeed/001/240/075/90f.png"
  exit 1
fi

# Cherry-pick commits not modifying circle config onto the release branch
git checkout -b public --track public/main
git pull

set +e
if [[ "$CIRCLECI" != "" ]]; then
  git config --global user.email "bot@getpantheon.com"
  git config --global user.name "Pantheon Automation"
fi
set -e

for commit in "${commits[@]}"; do
  if [[ -z "$commit" ]] ; then
    continue
  fi
  echo "Adding $commit:"
  git --no-pager log --format=%B -n 1 "$commit"
  git cherry-pick -rn "$commit" 2>&1
  # Product request - single commit per release
  # The commit message from the last commit will be used.
  git log --format=%B -n 1 "$commit" > /tmp/commit_message
  # git commit --amend --no-edit --author='Pantheon Automation <bot@getpantheon.com>'
done

echo "Committing changes"
git commit -F /tmp/commit_message --author='Pantheon Automation <bot@getpantheon.com>'

echo
echo "Releasing to upstream org"
echo

# Push to the public (pantheon-upstreams/drupal-composer-managed) repository
git push public public:main

# release-pointer needs to be moved to the end of the branch we started on ($CIRCLE_BRANCH)

git checkout $CIRCLE_BRANCH

# update the release-pointer
git tag -f -m 'Last commit set on upstream repo' release-pointer HEAD

# Push release-pointer
git push -f origin release-pointer
