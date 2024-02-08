
# Symfony Movies

Multilangual personal movies library project made in Symfony and TailwindCSS. Contains login system with role management and list of movies with genres, actors and reviews.



## Features

- Login system
- Multilangual (en, pl)
- User roles management (admin, editor, user)
- Dashboard to manage app content and user profile
- List of movies with pagination
- Actors, Genres and Reviews


## Environment Variables

To run this project, you will need to add the following environment variables to your .env file

`APP_ENV`

`APP_SECRET`

`DATABASE_URL`




## Run Locally

Clone the project

```bash
  git clone https://github.com/kbookowsky/symfony-movies
```

Go to the project directory

```bash
  cd symfony-movies
```

Install dependencies

```bash
  npm install
  composer install
```

Build assets

```bash
  npm run build
```

Run migrations and fixtures

```bash
  symfony console doctrine:migrations:migrate
  symfony console doctrine:fixtures:load
```

Start the server

```bash
  symfony server:start
```
