# Wordpress
https://wordpress.org | https://codex.wordpress.org

## Best Practices

### Coding standard
https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/  
https://github.com/WordPress/WordPress-Coding-Standards

- ***Install PHP Code Snifer***  
```composer create-project wp-coding-standards/wpcs --no-dev```

- ***Install Wordpress Coding Standard***  
```git clone -b master https://github.com/WordPress/WordPress-Coding-Standards.git wpcs```

- *** Code sniffer settings  
```phpcs --config-set installed_paths /path/to/wpcs```

***Usage***  
phpcs filepath  
phpcbf filepath/direcory 
                           
                           

