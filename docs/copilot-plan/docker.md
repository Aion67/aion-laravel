# Docker Notes

## Local

- Use `compose.yaml` for the local multi-service stack.
- Start the app with `docker compose -f compose.yaml up -d --build`.
- Run app setup commands inside the app container after startup.

## Production

- Use `docker-compose.prod.yml` for the remote deployment stack.
- Build and start it with `docker compose -f docker-compose.prod.yml up -d --build`.
- Run migrations and cache warmup commands inside the production app container after deploy.

## Seeded Demo Data

- The default seeder now creates realistic users, customers, medications, prescriptions, sales, and stock movements.
- It is safe for local demos and report validation.