# Containerized PHP Blog

## Introduction

Simple dockerized Blog framework. The blog features:

- A connection to a MySQL database. For simplicity the database directory is included in the repository but it should be considered a separate service and has therefore its own dedicated README page.
- Internationalization for english and french.
- Admin role to create and manage posts.
- Custom routage system.

Only users with admin role can create, edit and delete posts. Users can comment posts. Please read the database instructions on how to add a admin user.

## Requirements

You'll need to install docker in order to launch the docker container: https://docs.docker.com/engine/install/

## 🚚 Building the image

```sh
docker build -t <IMAGE_NAME> .
```

## 🏃‍♂️ Running the container

```sh
docker run --restart=always -d -p <PORT> --name <CONTAINER_NAME> <IMAGE_NAME>
```

To learn about the `PORT` command: https://docs.docker.com/config/containers/container-networking/

## 📶 Connecting to a database

To connect the server to the database start by creating a network:

```sh
docker network create <NETWORK>
```

You can then connect your blog container and your database container using the connect command:

```sh
docker network connect <NETWORK> <CONTAINER>
```

## Development

For a development environment you'll need to initialize a volume to avoid rebuilding your image everytime you change the code. The easiest way to do this is by adding the `-v <SOURCE PATH:DESTINATION PATH>` option when running the container. The paths needs to be absolute. This will overwrite the destination directory and bind it to your source directory.

🦄 For UNIX users the following example will do the trick:

```sh
docker run --restart=always -d -p <PORT> --name <CONTAINER_NAME> -v ${PWD}:/var/www <IMAGE_NAME>
```

Where `PWD` is a UNIX variable containing the current directory path.

### Environment variables

```
APP_ENV - specifies the app environment which changes server behaviour. Defaults to **production**.
DB_DRIVER - defaults to **mysql**.
DB_HOST - address to your database. Depending on your network settings you can use the database container name. Defaults to **db**.
DB_DATABASE - Name of the database you want to use. By default **blog**.
DB_USERNAME - Root username. Defaults to **root**.
DB_PASSWORD - Root password, default is an empty string.
```

### Composer

As you overwrite the code in the container you will have to install composer to generate the dependencies: https://getcomposer.org/download/

In the same directory as the composer.json file, execute the following command:

```sh
composer install
```

## TL;DR

😩 In the repository, launch the following commands (UNIX users only):

```sh
# Rename env examples
mv ./database/.env.example ./database/.env && \
mv .env.example .env
```

```sh
# Build images
docker build -t db-blog ./database && \
docker build -t blog . && \
# Run containers
docker run --restart=always -d  --env-file=./database/.env -v ${PWD}/database/data:/var/lib/mysql --name db-container db-blog && \
docker run --restart=always -d -p 8080:80 --name blog blog && \
# Create network and connect containers
docker network create blog-net && \
docker network connect blog-net db-container && \
docker network connect blog-net blog
```

Once the database has started, run these commands to add admin user:

```sh
docker exec -it db-container mysql -u root -psecret
```

```sql
INSERT INTO blog.users(name, email, password, role)
VALUES('admin', 'admin@admin.com', '$2y$10$HnvxsRPGwW3ewY2j79tHkeJUGXK/J5Mot.FFtd3otZS299HwhbTme', 'admin');
```

You will find the blog on `localhost:8080` in your browser. Login as admin to explore the site to its fullest:

- username: `admin@admin.com`
- password: `password`
