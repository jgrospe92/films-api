# Slim-Based Template

A template to be used to create REST-based Web services using the latest version of **Slim** PHP micro framework.

## Deployment Instructions

1. Copy the `slim-template` folder into `htdocs`
2. Rename the `slim-template` folder to `[project_name]-api`. For example, `films-api`
3. In the `.htaccess` file, change the specified subdirectory that is assigned to the `RewriteBase` directive.  The name of the subdirectory must be the same as the one you chose in Step 2). For example, change from `RewriteBase /slim-template` to  `RewriteBase /films-api`
4. In `index.php`, change the applications's base path directory from  `$app->setBasePath("/slim-template");` to `$app->setBasePath("/films-api");`

## Importing and Populating the Database

1. Use phpMhyAdmin to import the provided .sql file.
2. Before importing the database schema, disable the `Enable foreign key checks` option that can be found under the `Import` tab
3. You can consult the structure of the Sakila database at [Mysql Doc](https://dev.mysql.com/doc/sakila/en/sakila-structure.html)
4. In `app_config.php`, change the value of DB_NAME constant to reflect the name of the database you want to connect to.

## Dependencies

---

Use `composer` to either update the referenced or add additional dependencies.

1. `composer require slim/slim:"4.*"`
2. `composer require guzzlehttp/guzzle`
3. `composer require vlucas/phpdotenv`
4. `composer require firebase/php-jwt`
5. `composer require monolog/monolog`

composer remove tuupola/slim-jwt-auth --update-with-dependencies


## VS Code Extensions for Web Development

---

1. [Thunder Client](https://marketplace.visualstudio.com/items?itemName=rangav.vscode-thunder-client)
2. [Path Intellisense](https://marketplace.visualstudio.com/items?itemName=christian-kohler.path-intellisense)
3. [GitLens — Git supercharged](https://marketplace.visualstudio.com/items?itemName=eamodio.gitlens)
4. [PHP IntelliSense](https://marketplace.visualstudio.com/items?itemName=zobo.php-intellisense)
5. [PHP Intelephense](https://marketplace.visualstudio.com/items?itemName=bmewburn.vscode-intelephense-client)
6. [PHP Debug](https://marketplace.visualstudio.com/items?itemName=xdebug.php-debug)
7. [PHP Extension Pack](https://marketplace.visualstudio.com/items?itemName=xdebug.php-pack)
8. [ESLint](https://marketplace.visualstudio.com/items?itemName=dbaeumer.vscode-eslint)
9. [JavaScript (ES6) code snippets](https://marketplace.visualstudio.com/items?itemName=xabikos.JavaScriptSnippets)
10. [HTML Snippets](https://marketplace.visualstudio.com/items?itemName=abusaidm.html-snippets)

### Markdown

1. [Markdown All in One](https://marketplace.visualstudio.com/items?itemName=yzhang.markdown-all-in-one)
2. [markdownlint](https://marketplace.visualstudio.com/items?itemName=DavidAnson.vscode-markdownlint)
3. [Markdown Preview Github Styling](https://marketplace.visualstudio.com/items?itemName=bierner.markdown-preview-github-styles)
4. [Markdown PDF](https://marketplace.visualstudio.com/items?itemName=yzane.markdown-pdf)

### Productivity

1. [Code Spell Checker](https://marketplace.visualstudio.com/items?itemName=streetsidesoftware.code-spell-checker)
2. [Bookmarks](https://marketplace.visualstudio.com/items?itemName=alefragnani.Bookmarks)

### Appearance

1. [Material Icon Theme](https://marketplace.visualstudio.com/items?itemName=PKief.material-icon-theme)

#### Dark Themes

1. [Evondev Dracula](https://marketplace.visualstudio.com/items?itemName=evondev.dracula-high-contrast)
2. [Tokyo Night](https://marketplace.visualstudio.com/items?itemName=enkia.tokyo-night)
3. [Dracula Official](https://marketplace.visualstudio.com/items?itemName=dracula-theme.theme-dracula)



### Reference
- Sleiman Rabah
- Vanier College Teacher