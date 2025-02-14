FROM node:20.9.0-alpine AS base
RUN npm install -g pnpm
WORKDIR /srv/app
COPY ./.docker/front/docker-entrypoint.sh /docker-entrypoint.sh
ENTRYPOINT ["/docker-entrypoint.sh"]

FROM base AS build
COPY ./front /var/www
RUN pnpm install && pnpm build

FROM node:20.9.0-alpine AS final
COPY --from=build /var/www/.output /var/www
ENV PORT=80
EXPOSE 80
ENTRYPOINT [""]
WORKDIR /var/www/server
CMD ["node", "index.mjs"]