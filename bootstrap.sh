#!/bin/sh

#docker builder prune --all

if [[ "$1" == "clear" ]]; then
  rm -rf ./docker/db/master/data/*
  rm -rf ./docker/db/rep/data/*
  rm -rf ./app/log/*
  echo "removed!"
fi

docker-compose up --build --force-recreate
