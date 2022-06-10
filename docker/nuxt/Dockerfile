FROM node:14-alpine3.14

RUN apk add tzdata
RUN npm install -g nuxt@2.x
RUN nuxt -v

WORKDIR /var/www

ARG NUXT_RUN_COMMAND
ENV NUXT_RUN_COMMAND ${NUXT_RUN_COMMAND}
CMD ${NUXT_RUN_COMMAND}
