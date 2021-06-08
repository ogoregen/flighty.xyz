flighty.xyz is a minimalist, article focused blog.

## Features

* Fully responsive, mobile friendly, and accesible design
  * Dedicated mobile navigation menu
* Dark theme
* Admin panel
  * Focused view
  * Entry creation - articles and pages
  * Editable and deletable entries
  * Markdown text formatting
  * Customizable menu order of pages
* Command line interface superuser creation

## Interactive Superuser Creation

```console
you@here:~$ php manage.php createuser
```
The script will walk you through the process, prompting for username and password. After user creation, you can log in on `/admin` or `/login`.

## Deployment

Required MySQL code for the database is provided under `data/`. Database credentials must be put into `database.php`. The website will be now ready to be served with Apache. The folder `public/` should be served.

## Dependencies

- [Parsedown](https://parsedown.org)

flighty.xyz uses icons from [css.gg](https://css.gg) and the [Inter](https://rsms.me/inter) font.
