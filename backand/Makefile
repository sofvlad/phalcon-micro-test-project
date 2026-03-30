init:
	docker exec -it market-backend composer install
	docker exec -it market-backend php vendor/bin/phinx migrate

init-seeds:
	docker exec -it market-backend php vendor/bin/phinx seed:run
