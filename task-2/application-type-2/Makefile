# This file is part of the B24PhpSdk package.
#
#  For the full copyright and license information, please view the MIT-LICENSE.txt
#  file that was distributed with this source code.

PHP = php
PORT = 8080
HOST = localhost
DOCUMENT_ROOT = public
XDEBUG_MODE = debug
XDEBUG_START = yes
XDEBUG_HOST = host.docker.internal
PHP_IDE_CONFIG = serverName=WSL

.PHONY: serve-debug

default:
	@echo "make needs target:"
	@egrep -e '^\S+' ./Makefile | grep -v default | sed -r 's/://' | sed -r 's/^/ - /'

serve-debug:
	PHP_IDE_CONFIG=$(PHP_IDE_CONFIG) \
	$(PHP) -S $(HOST):$(PORT) -t $(DOCUMENT_ROOT) \
	-d xdebug.mode=$(XDEBUG_MODE) \
	-d xdebug.start_with_request=$(XDEBUG_START) \
	-d xdebug.client_host=$(XDEBUG_HOST)

# linters
lint-phpstan:
	vendor/bin/phpstan --memory-limit=1G analyse
lint-rector:
	vendor/bin/rector process --dry-run
lint-rector-fix:
	vendor/bin/rector process