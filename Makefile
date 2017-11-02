SSH_KEY_NAME=dev-trtdashboard
WEB_DIR=/home/trtscraper/dashboard

rsync:
	rsync -az --no-perms --delete -i --omit-dir-times . ${SSH_KEY_NAME}:${WEB_DIR} --exclude-from 'exclude-list.txt'

deploy: rsync