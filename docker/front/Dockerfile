FROM node:18.12.1-alpine AS base

RUN npm install -g pnpm

WORKDIR /srv/app
COPY ./docker/front/docker-entrypoint.sh /docker-entrypoint.sh
ENTRYPOINT ["/docker-entrypoint.sh"]

FROM base AS build

COPY ./front /srv/app

RUN pnpm install && pnpm run build

FROM nginx:1.20.2-alpine AS final

COPY --from=build /srv/app/dist /srv/app
COPY conf/nginx/app.conf /etc/nginx/conf.d/default.conf