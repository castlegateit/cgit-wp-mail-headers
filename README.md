# Castlegate IT WP Mail Headers

Replace the `From` header and set a default `Reply-To` header on all mail sent by the default `wp_mail` function. The default values are set by two required constants:

``` php
define('CGIT_WP_MAIL_HEADERS_FROM', 'Example Name <example@example.com>');
define('CGIT_WP_MAIL_HEADERS_REPLY_TO', 'Example Name <example@example.com>');
```

These constants should be set in `wp-config.php`. The `From` header will always be replaced with the default value. The `Reply-To` header will only be added if the email does not already have a `Reply-To` header.

## License

Released under the [MIT License](https://opensource.org/licenses/MIT). See [LICENSE](LICENSE) for details.
