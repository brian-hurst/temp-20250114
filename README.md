# Temp-20250114 Deployment:
1) Clone Project
   1) ``git@github.com:brian-hurst/temp-20250114.git && cd temp-20250114``
2) Build the default NGINX PHP FPM Docker Image.
   1) ``docker build -t temp-20250114 . -f Dockerfile --no-cache``
3) Run the Container's.
   1) ``docker compose -f compose.yaml up -d``
4) Install the app.
   1) ``docker exec -it temp-20250114-app-1 composer install --no-interaction``
5) App should be browsable at.
   1) ``http://localhost:8001/``
6) To test:
   1) ``docker exec -it temp-20250114-app-1 php bin/phpunit``

## Troubleshooting
If the container doesn't start, make sure the entrypoint is executable and that it has LF line endings, not CRLF:

```chmod +x docker/php-fpm-nginx/script.sh```

Check that the port defined in compose.yaml is available.
