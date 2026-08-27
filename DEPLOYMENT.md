# Production Operations

Production uses `/opt/sim-keanggotaan-imm/compose.prod.yaml`. Never use
`docker compose down -v`, `migrate:fresh`, or `db:wipe` on this instance.

## Routine commands

```bash
cd /opt/sim-keanggotaan-imm
sudo docker compose -f compose.prod.yaml ps
sudo docker compose -f compose.prod.yaml logs --tail=200 app
sudo docker compose -f compose.prod.yaml logs --tail=200 queue
sudo docker compose -f compose.prod.yaml restart queue
sudo docker compose -f compose.prod.yaml exec --user appuser app php artisan migrate --force
sudo docker compose -f compose.prod.yaml exec --user appuser app php artisan queue:failed
sudo docker compose -f compose.prod.yaml exec --user appuser app php artisan queue:retry JOB_ID
sudo /usr/local/sbin/backup-sim-keanggotaan-imm
```

## Safe update

1. Run the backup command and record the current commit with `git rev-parse HEAD`.
2. Use `php artisan down` when the change risks incompatible requests.
3. Fetch and checkout the reviewed commit on `features/fedora-workspace`.
4. Run `npm ci && npm run build`.
5. Run Composer in the image with `sudo docker compose -f compose.prod.yaml run --rm --user appuser app composer install --no-dev --prefer-dist --optimize-autoloader`.
6. Run `sudo docker compose -f compose.prod.yaml build && sudo docker compose -f compose.prod.yaml up -d`.
7. Run migration, `php artisan optimize`, and restart the queue.
8. Run `php artisan up`, then verify `/up`, login, uploads, and queue logs.

## Restore

Stop application writes with `php artisan down`, take another backup, and extract
the selected root-only archive. Import `database.sql` with `mariadb` inside the
database container, restore both `storage/app` directories and the certificate
background, restore `.env` with mode `0600`, then run pending migrations,
optimize Laravel, restart the queue, and verify before running `php artisan up`.

## Rollback

Checkout the recorded previous commit, restore its dependencies and assets,
rebuild the images, and recreate `app` and `queue`. Database migrations are not
automatically reversible; restore the pre-deployment backup when schema or data
changes are incompatible.
