#! /bin/bash

function _up() {
  docker-compose -f docker-compose.yml up -d
}

function _stop() {
  docker-compose -f docker-compose.yml stop
}

function _rebuild() {
  docker-compose -f docker-compose.yml up -d --build --force-recreate --remove-orphans
}

function _ssh() {
  docker-compose -f docker-compose.yml exec app bash
}

function _migrate() {
  docker-compose -f docker-compose.yml exec app php database/migration.php
}

function _seed() {
  docker-compose -f docker-compose.yml exec app php database/seed.php
}

case $1 in
"start") _up ;;
"stop") _stop ;;
"rebuild") _rebuild ;;
"ssh") _ssh ;;
"migrate") _migrate ;;
"seed") _seed ;;
esac
