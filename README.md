# news-api

### Instructions to run the News API website:

1. Copy the `.env.example` and rename it to `.env`.
2. Put your newsapi.org API key between the `""` in the same line as `NEWS_API_KEY`.
3. Create a new database that has users table with `id`, `name`, `email`, and `password` columns.
    - `id` must auto-increment and `email` must be unique to the table.
4. Put your `DATABASE_NAME`, `DATABASE_USER`, `DATABASE_PASSWORD`, `DATABASE_HOST`, `DATABASE_DRIVER` in the `.env`
   file.
    - `DATABASE_NAME`, `DATABASE_USER`, `DATABASE_PASSWORD` are all required.
    - `DATABASE_HOST` is localhost by default and `DATABASE_DRIVER` is pdo_mysql by default.
5. Run the website from the `public` directory.