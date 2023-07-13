FROM node:18.12.1-alpine AS base

RUN npm install -g pnpm

WORKDIR /srv/app
COPY ./.docker/front/docker-entrypoint.sh /docker-entrypoint.sh
ENTRYPOINT ["/docker-entrypoint.sh"]

FROM base AS final

COPY ./front /srv/app
RUN chown -R "1000:1000" /srv/app
COPY ./.docker/front/docker-entrypoint-prod.sh /docker-entrypoint.sh
ENTRYPOINT ["/docker-entrypoint.sh"]