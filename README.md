# Jekyll Discuss

## Installation

**1.** Install dependencies with Composer

`composer install`

**2.** Clone your GitHub repository to a sub-directory of `jekyll-discuss`

**3.** Give permissions to the directory so that your web server's group can write to it

e.g. if your user is `ec2-user` and your web server is on the group `apache`

`sudo chown -R ec2-user:apache jekyll-discuss`

**4.** Edit the `config` file with the following parameters:

| variable               | role                                            | variables
|------------------------|-------------------------------------------------|-----------------------|
| GIT_USERNAME           |  Your GitHub username                           |                       |
| GIT_REPO               |  The relative path to the repository folder     |                       |
| GIT_REPO_REMOTE        |  The GitHub path to the repository              |                       |
| GIT_USER               |  The name to appear in the commits              |                       |
| GIT_EMAIL              |  The email address to appear in the commits     |                       |
| COMMENTS_DIR_FORMAT    |  Path and format for the comments directory     | `@post-slug`          |
| COMMENTS_FILE_FORMAT   |  Path and format for the comments files         | `@timestamp`, `@hash` | 

**5.** Create a personal access token on GitHub and write it to `.gittoken`

`echo {THE-TOKEN} > .gittoken`
