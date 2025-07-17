prepush:
	@echo "CMD: make prepush"
	@echo "USAGE: Runs bun prepush and composer prepush"
	docker compose exec -it php composer prepush && bun prepush
