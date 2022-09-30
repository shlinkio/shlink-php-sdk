#!/usr/bin/env sh

if [ "$#" -lt 1 ]; then
  echo "Usage:" >&2
  echo "   $0 {version}" >&2
  exit 1
fi

VERSION=$1
CONTAINER=$(docker run --rm -d -p 8080:8080 -e DEFAULT_DOMAIN=localhost:8080 -e IS_HTTPS_ENABLED=false ghcr.io/shlinkio/shlink:${VERSION})

vendor/bin/phpunit --order-by=random -c phpunit-integration.xml --testdox --colors=always

docker stop ${CONTAINER}
