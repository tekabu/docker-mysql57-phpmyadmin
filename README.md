# MySQL 5.7 + phpMyAdmin - Server Migration Setup

## Quick Start

1. Copy the example environment file:
   ```bash
   cp .env.example .env
   ```

2. Edit `.env` with strong passwords:
   ```bash
   nano .env
   ```

3. Start MySQL:
   ```bash
   docker compose up -d
   ```

4. Start phpMyAdmin only when needed:
   ```bash
   docker compose --profile tools up -d phpmyadmin
   ```

## Access

- **MySQL**: `127.0.0.1:3306` (default bind)
- **phpMyAdmin**: `http://127.0.0.1:8080` (only when `tools` profile is started)

## Management Commands

- Start MySQL: `docker compose up -d`
- Start phpMyAdmin: `docker compose --profile tools up -d phpmyadmin`
- Stop: `docker compose down`
- Restart: `docker compose restart`
- View logs: `docker compose logs -f`
- Stop and remove volumes: `docker compose down -v`

## Configuration

- MySQL config: `my.cnf`
- Environment variables: `.env`
- Data persistence: Docker volume `mysql_data`
- Pinned MySQL image: `mysql:5.7.44`

## Backup and Restore

```bash
# Backup database
docker compose exec mysql sh -c 'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" mysqldump -u root --single-transaction --quick --routines --events --triggers --set-gtid-purged=OFF app_production' > backup.sql

# Restore database
docker compose exec -T mysql sh -c 'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" mysql -u root app_production' < backup.sql
```

## Migration Runbook (MySQL 5.7 to MySQL 5.7)

1. Prepare destination server
   ```bash
   docker compose up -d
   ```

2. Create full logical backup on source server
   ```bash
   mysqldump -u root -p --single-transaction --quick --routines --events --triggers --set-gtid-purged=OFF --all-databases > full_backup.sql
   ```

3. Transfer backup to destination server
   ```bash
   scp full_backup.sql user@new-server:/tmp/full_backup.sql
   ```

4. Restore on destination server
   ```bash
   cat /tmp/full_backup.sql | docker compose exec -T mysql sh -c 'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" mysql -u root'
   ```

5. Validate critical data
   ```bash
   docker compose exec mysql sh -c 'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" mysql -u root -e "SHOW DATABASES;"'
   ```

6. Cutover with minimal downtime
   - Put app in maintenance mode.
   - Run one final dump from source and restore to destination.
   - Point app/database DNS to destination.
   - Remove maintenance mode.

## Security Notes (while staying on 5.7)

- Keep port binds to `127.0.0.1` unless external access is strictly required.
- Start phpMyAdmin only when needed (`--profile tools`), then stop it.
- Change all passwords in `.env` before first start.
- Restrict host-level firewall rules to app/IP allowlists only.
