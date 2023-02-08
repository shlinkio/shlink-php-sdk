#!/usr/bin/env sh

if [ "$#" -lt 1 ]; then
  echo "Usage:" >&2
  echo "   $0 {shlink_version} [{api_version}]" >&2
  exit 1
fi

export SHLINK_VERSION=$1
SECOND_PARAM=$2
export SHLINK_API_VERSION=${SECOND_PARAM:='2'}
PORT="8765"
export SHLINK_BASE_URL="http://localhost:${PORT}"
CONTAINER=$(docker run --rm -d -p ${PORT}:8080 -e DEFAULT_DOMAIN=localhost:${PORT} -e IS_HTTPS_ENABLED=false ghcr.io/shlinkio/shlink:${SHLINK_VERSION})
sleep 2 # Let's give the server a couple of seconds to start
export SHLINK_API_KEY=$(docker exec ${CONTAINER} shlink api-key:generate | grep "Generated API key" | sed 's/.*Generated\ API\ key\:\ \"\(.*\)\".*/\1/')

vendor/bin/phpunit --order-by=random -c phpunit-integration.xml --testdox --colors=always

docker stop ${CONTAINER}
