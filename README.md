# Jekyll Discuss
> A commenting system for Jekyll

## Introduction

All the information about this project is described in [this blog post](https://eduardoboucas.com/blog/2015/05/11/rethinking-the-commenting-system-for-my-jekyll-site.html).

## Installation

> Before you start the installation, you need to know the primary group of your web server's user. If you're using Apache, try `groups apache` our `groups www`. For these instructions, we'll use `apache` as the web server's group and `ec2-user` as the regular user.

1. Install dependencies with Composer

`composer install`

2. Clone your GitHub repository and give your web server write permissions

`sudo chgrp -R apache YOUR-REPO`
`sudo chmod -R 774 YOUR-REPO`

3. Add your user to the group used by the web server so you can read and access the files

`sudo usermod -a -G apache ec2-user`

4. Edit the `config` file with the following parameters:

| variable                 | role                                            | variables             |
|--------------------------|-------------------------------------------------|-----------------------|
| `GIT_USERNAME`           |  Your GitHub username                           |                       |
| `GIT_REPO`               |  The relative path to the repository folder     |                       |
| `GIT_REPO_REMOTE`        |  The GitHub path to the repository              |                       |
| `GIT_USER`               |  The name to appear in the commits              |                       |
| `GIT_EMAIL`              |  The email address to appear in the commits     |                       |
| `COMMENTS_DIR_FORMAT`    |  Path and format for the comments directory     | `@post-slug`          |
| `COMMENTS_FILE_FORMAT`   |  Path and format for the comments files         | `@timestamp`, `@hash` | 

5. Create a personal access token on GitHub and write it to `.gittoken`

`echo {THE-TOKEN} > .gittoken`

6. Edit the fields and the validation in `index.php` to fit your needs

7. POST your form data to `index.php`

You should be good to go! Ping me on [Twitter](https://twitter.com/eduardoboucas) if you have any questions.

## Contributing

I need help! If you think this approach to a commenting system for Jekyll is interesting, share your ideas and pull requests and let's make it better.
