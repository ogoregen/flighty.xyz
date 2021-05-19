flighty.xyz is a minimalist, article focused blog. Its design aims for clarity.

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

## Usage

### Logging in to the admin panel

You can use the username `flighty_admin` and pasword `ceit133` on [flighty.xyz/admin](https://flighty.xyz/admin) to log in to the admin panel.

### Interactive Superuser Creation

```console
you@here:~$ php manage.php createuser
```
The script will walk you through the process, prompting for username and password. After user creation, you can log in on `/admin` or `/login`.

## Dependencies

- [Parsedown](https://parsedown.org)

flighty.xyz uses icons from [css.gg](https://css.gg) and the [Inter](https://rsms.me/inter) font.
