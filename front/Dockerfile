FROM node:24-alpine AS base
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
RUN pnpm build

FROM node:24-alpine AS prod
WORKDIR /srv/app
COPY --from=build /srv/app/.output /srv/app
ENV PORT=80
ENV HOST=0.0.0.0
EXPOSE 80
CMD ["node", "/srv/app/server/index.mjs"]