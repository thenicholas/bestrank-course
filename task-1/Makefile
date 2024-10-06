.PHONY: all run install clean

# Default target
all: install run

# Install dependencies if vendor directory doesn't exist
install:
	@if [ ! -d "vendor" ]; then \
		echo "Vendor directory not found. Running composer install..."; \
		docker compose run --rm php composer install; \
	else \
		echo "Vendor directory already exists. Skipping composer install."; \
	fi

# Run the PHP script
run:
	docker compose run --rm php sh -c "cd bitrix24-php-sdk && php sdk-api-request.php"

# Clean up
clean:
	docker compose down -v
	rm -rf vendor
