#!/bin/bash

set -o errexit

source "$( dirname "${BASH_SOURCE[0]}" )/travis_retry.sh"

echo 'Installing Yarn'
travis_retry npm install yarn -g

echo 'Configuring Yarn'
travis_retry yarn global add grunt

echo 'Installing Node packages'
cd "$TRAVIS_BUILD_DIR/build"
travis_retry yarn
