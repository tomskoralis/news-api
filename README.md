# news-api

### Instructions to run the News API website:

1. Make a copy of the `.env.example` and rename it to `.env`.
2. Enter your newsapi.org API key in the `.env` file.
3. Create a new mySQL 8.0 database that has users table with `id`, `name`, `email`, and `password` columns.
    - `id` must auto-increment and `email` must be unique to the table.
4. Enter your database credentials in the `.env`
   file.
    - `DATABASE_NAME`, `DATABASE_USER`, `DATABASE_PASSWORD` are required.
    - `DATABASE_HOST` is localhost by default and `DATABASE_DRIVER` is pdo_mysql by default.
5. Test the website by running it from the `public` directory using the command: 
```
php -S localhost:8000