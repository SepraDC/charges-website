FROM node:22-alpine AS base
RUN npm install -g pnpm
WORKDIR /srv/app
COPY . .
RUN pnpm install

FROM base AS dev
ENV HOST=0.0.0.0
ENV PORT=3000

EXPOSE 3000

CMD ["pnpm", "run", "dev"]

FROM base AS build
COPY . /srv/app
RUN pnpm install && pnpm build

FROM node:22-alpine AS final
COPY --from=build /srv/app/.output /srv/app
ENV PORT=80
EXPOSE 80
ENTRYPOINT [""]
WORKDIR /srv/app/server
CMD ["node", "index.mjs"]