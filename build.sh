echo "Настройка .env"
cp .env-example .env
echo REAL_UID=$(id -u) >> .env

echo "Начинаю сборку контейнера..."
docker-compose build

echo "Используйте 'docker-compose up -d', чтобы запустить"