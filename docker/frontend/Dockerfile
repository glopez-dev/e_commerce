FROM node:18-alpine AS build

WORKDIR /app

COPY frontend/package.json ./
RUN npm install

COPY frontend/ ./
RUN npm run build

FROM alpine:3.19

COPY --from=build /app/build /build
CMD ["cp", "-r", "/build/.", "/var/www/e_commerce/"]
