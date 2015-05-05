#!/bin/bash
while [[ $# > 1 ]]
do
key="$1"

case $key in
    -p|--post)
    POST_SLUG="$2"
    shift
    ;;
    -n|--name)
    NAME="$2"
    shift
    ;;
    -e|--email)
    EMAIL="$2"
    shift
    ;;
    -h|--hash)
    EMAIL_HASH="$2"
    shift
    ;;
    -u|--url)
    URL="$2"
    shift
    ;;        
    -m|--message)
    MESSAGE="$2"
    shift
    ;;
    -r|--repo)
    REPO="$2"
    shift
    ;;
    --default)
    DEFAULT=YES
    shift
    ;;
    *)
    ;;
esac
shift
done

# Read Git token
TOKEN=`cat .gittoken`

# Read config file
source config

FILE="name: ${NAME}\nhash: ${EMAIL_HASH}\n"

if [ ! -z "$URL" ]; then
    FILE=${FILE}"url: ${URL}\n"
fi

FILE=${FILE}"message: \"${MESSAGE}\"\n"

# Change directory to repo
cd ${GIT_REPO}

# Form comment file directory
COMMENTS_DIR=${COMMENTS_DIR_FORMAT//@post-slug/$POST_SLUG}

# Create directory if does not exist
if [ ! -d "$COMMENTS_DIR" ]; then
  mkdir ${COMMENTS_DIR}
fi

COMMENT_TIMESTAMP=`date +%Y%m%d%H%M%S`
COMMENT_FILE=${COMMENTS_FILE_FORMAT//@timestamp/$COMMENT_TIMESTAMP}
COMMENT_FILE=${COMMENTS_DIR}/${COMMENT_FILE//@hash/$EMAIL_HASH}

# Abort if file already exists
if [ -f $COMMENT_FILE ]; then
    exit 0
fi

# Create file
printf "$FILE" > $COMMENT_FILE

# Prepare Git and commit file
git config user.name ${GIT_USER}
git config user.email ${GIT_EMAIL}
git remote rm origin
git remote add origin https://${GIT_USERNAME}:${TOKEN}@github.com/${GIT_REPO_REMOTE}
git status
git pull origin master
git add ${COMMENT_FILE}
git commit -m "Automatic upload of comment"
git push --quiet origin master > /dev/null 2>&1