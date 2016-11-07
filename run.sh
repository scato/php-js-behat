#!/usr/bin/env bash

composer install
npm install --legacy-bundling

vendor/bin/behat
