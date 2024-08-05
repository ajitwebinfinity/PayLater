#!make
include .env
export

all:
	curl -X POST \
		-H "content-type:application/json" \
		"https://packagist.org/api/update-package?username=george.nuclearo&apiToken=$$PACKAGIST_TOKEN" \
		-d '{"repository":{"url":"https://packagist.org/packages/spotii/spotiipay"}}'
