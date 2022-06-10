# happyday-gallery container

## сборка

### добавление хостов

- в linux/mac `/etc/hosts`
- в windows `c:\windows\system32\drivers\etc\hosts`

```
127.0.0.1 gallery.happyday.loc # frontend
127.0.0.1 api.gallery.happyday.loc # backend
```

### https!

- идем в `docker/nginx/ssl`
- принимаем сертификата (файлы `.crt`)
    - в винде
      по [доке](https://track.webregul.ru/youtrack/articles/WRG-A-29/%D0%BB%D0%BE%D0%BA%D0%B0%D0%BB%D1%8C%D0%BD%D1%8B%D0%B9-ssl#%D1%83%D1%81%D1%82%D0%B0%D0%BD%D0%BE%D0%B2%D0%BA%D0%B0-%D1%81%D0%B5%D1%80%D1%82%D0%B8%D1%84%D0%B8%D0%BA%D0%B0%D1%82%D0%BE%D0%B2-%D0%BD%D0%B0-windows)
    - в макоси
      по [доке](https://track.webregul.ru/youtrack/articles/WRG-A-29/%D0%BB%D0%BE%D0%BA%D0%B0%D0%BB%D1%8C%D0%BD%D1%8B%D0%B9-ssl#%D1%83%D1%81%D1%82%D0%B0%D0%BD%D0%BE%D0%B2%D0%BA%D0%B0-%D1%81%D0%B5%D1%80%D1%82%D0%B8%D1%84%D0%B8%D0%BA%D0%B0%D1%82%D0%BE%D0%B2-%D0%BD%D0%B0-macos)
    - в линуксе есть пара доков: [раз](https://zalinux.ru/?p=4166)
      и [два](https://itsecforu.ru/2019/06/04/%F0%9F%94%91-%D0%BA%D0%B0%D0%BA-%D1%83%D1%81%D1%82%D0%B0%D0%BD%D0%BE%D0%B2%D0%B8%D1%82%D1%8C-%D1%81%D0%B5%D1%80%D1%82%D0%B8%D1%84%D0%B8%D0%BA%D0%B0%D1%82%D1%8B-ca-%D0%BD%D0%B0-%D1%81%D0%B5%D1%80%D0%B2/)

для хрома есть хак, которой точно работает на линуксе:

- введите в строку адреса `chrome://flags/#allow-insecure-localhost`
- установите `enabled`
- проверяйте на нужном хосте

### сборка контейнера

```
cd backend
cp .env.example .env
cd ../frontend
cp .env.example .env && npm install
cd ../docker
cp .env.example .env
docker-compose up -d
```

#### сайты

по дефолту и если вы добавили их в `hosts` в блоке "добавление хостов" выше

- https://gallery.happyday.loc:8090 - фронт (надо сначала пройти этап "сборка фронтенда" и, если нужен локальный бек, "
  сетап бекенда" ниже)
- https://api.gallery.happyday.loc:8090 - бек (надо сначала пройти этап "сетап бекенда" ниже)
- https://api.gallery.happyday.loc:8090/api/docs - документация по апи (надо сначала пройти этап "сетап бекенда" ниже)

##### сборка фронтенда

в контейнере установлен `nuxt`, он работает по команде `nuxt --config-file web/nuxt.config.js`. поэтому запускать
отдельно нукст через `npm run dev:web` не надо – билд доступен на домене фронта.

команда запуска `nuxt` определена в `docker/.env` - `NUXT_RUN_COMMAND`, поэтому ее можно легко поменять.

например, если вам нужен продбилд, то:

- делаем в папке `frontend` `npm run build:web`
- меняем команду запуска `NUXT_RUN_COMMAND="nuxt start --config-file web/nuxt.config.js"`
- делаем `docker-compose up -d --no-deps --force-recreate --build nuxt`
- ждем и получаем продбилд

надо понимать, что для обновления билда надо каждый раз делать `npm run build:web`
и на всякий случай делать рестарт контейнера `docker-compose restart nuxt` (но это не факт)

##### сетап бекенда

если вам нужен локальный бек, то:

```
cd docker
docker-compose exec app /usr/local/docker-entrypoint/init.sh
```

##### сетап регистрации/авторизации через happyday

регистрация/авторизация идет через dev.happyday.ru - по умолчанию это настроено в `backend/.env`. если настройки домена
апи тоже по умолчанию (`https://api.gallery.happyday.loc:8090`), то все будет работать из коробки.

если у вас другой домен или вам надо включить авторизацию через препрод или прод, нужно обратиться к девопсу.

на локалке в контейнере настроить межпроектную авторизацию пока невозможно.

## если что-то сломалось в базе или на беке

эти команды ставят свежий композер и применяет миграции

```
cd docker
docker-compose exec app sh -c 'cd backend && composer install'
docker-compose exec app php backend/artisan migrate
```

## если что-то сломалось на фронте

эти команды ставят свежую ноду, делают билд и делают рестарт нукста в контейнере

```
cd docker
sh -c 'cd ../frontend && rm -rf node_modules && npm install && npm run build:web' && docker-compose restart nuxt
```

## команды

- остановка `docker-compose stop`
- запуск после остановки `docker-compose start`
- перезапуск `docker-compose restart # опционально конкретный контейнер nginx|mysql|app|redis|mailhog`
- залезть в конкретный контейнер `docker-compose exec nginx|mysql|app|redis|mailhog sh`
- глянуть логи `docker-compose logs nginx|mysql|app|redis|mailhog`
- ребилд контейнера `docker-compose up -d --no-deps --force-recreate --build nginx|mysql|app|redis|mailhog`

## билд фронта внутри контейнера (если нет npm на компе)

! внимание !: обязательно после сборки откатите изменения в `package-lock.json`

```
docker-compose exec app bash -c 'cd frontend && npm install' # или любая другая команда сборки
docker-compose exec app git checkout -- package-lock.json # удаляем изменения, который вызвал npm из контейнера
```

## смена портов - если надо (лучше не надо)

- настраиваем внешние порты в `docker/.env`
- ребилд контейнера

## удаление контейнера

```
docker-compose stop
docker-compose rm -f
docker volume rm {APP_NAME from docker/.env}_{APP_ENV from docker/.env}_app-db
docker network rm {APP_NAME from docker/.env}_APP_ENV from docker/.env_app-network
```
