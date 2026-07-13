# Fedora Docker development

This project runs with PHP 8.4 and a separate MariaDB container. It does not use Fedora's PHP 8.5 or host MariaDB.

## First run

```bash
sudo usermod -aG docker $USER
# Log out and log back in (or reboot) so the new Docker group takes effect.

cd ~/Developments/sim-Keanggotaan-imm-docker
cp .env.example .env
cat .env.docker.example >> .env

docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
npm install
npm run dev
```

Open <http://localhost:8000>.

## Useful commands

```bash
docker compose up -d
docker compose down
docker compose logs -f app
docker compose exec app php artisan migrate
docker compose exec app composer install
docker compose exec app bash
```

The database is stored in Docker's `mariadb-data` volume. `docker compose down` keeps it; `docker compose down -v` permanently deletes it.
