#! /bin/bash

# Record the current date. This will be used to create release folders for this
# particular release.
datetimenow=$(date +%s)
basedir="/var/www/homi"
currentdir="$basedir/current"
releasedir="$basedir/releases"

echo "Releasing $datetimenow ..."

# Make a directory for this new release
mkdir -p "$releasedir/$datetimenow"

# Extract release into newly created directory
tar -xzf "$releasedir/release.tar.gz" -C "$releasedir/$datetimenow"

rm "$currentdir"

# Create a symlink between the newly created release and the "current" release
# directory.
ln -sf "$releasedir/$datetimenow" "$basedir/current"

# Copy master ENV file
cp "$basedir/.env" "$basedir/current/"

# Copy the swarm file from recent release to basedir
cp "$currentdir/docker-compose-release.yml" "$basedir"
cp "$currentdir/docker-compose-swarm.yml" "$basedir"

# give random label to swarm services to make sure they re-release
randomStr=`openssl rand -hex 20`; sed -i "s/RANDOM_LABEL/$randomStr/g" $basedir/docker-compose-swarm.yml

# add version number to the .env file
tagValue=`cat "$currentdir/HOMI_VERSION"`
echo "HOMI_VERSION=\"$tagValue\"">> "$currentdir/.env"

# Start the release container for migrations, etc.
docker-compose -f "$basedir/docker-compose-release.yml" up -d

# Fix permissions for Laravel
chmod -R 777 "$currentdir/storage"

docker exec $(docker ps -q -f name=php_release) php artisan migrate --force
docker exec $(docker ps -q -f name=php_release) php artisan config:cache && php artisan route:cache && php artisan view:cache
docker exec $(docker ps -q -f name=php_release) php artisan lighthouse:print-schema -W && php artisan horizon:publish

# Add symlink for the public dir
ln -sf "$basedir/storage/app/public" "$basedir/current/public/storage"

# Destroy temporary container for migrations, etc.
docker-compose -f "$basedir/docker-compose-release.yml" down

# deploy the swarm changes
docker stack deploy homi --compose-file "$basedir/docker-compose-swarm.yml"

# Maybe future terraform workers
#if [[ -z "${DO_PAT}" ]]; then
#  cd ~/hermes/terraform
#  WORKER_COUNT=`cat worker_count`
#  STATUS_COUNT=`cat status_count`
#  DO_PAT=`cat do_pat`
#  terraform apply -auto-approve  -var "do_token=${DO_PAT}"   -var "pvt_key=$HOME/.ssh/terraform" -var "workerCount=${WORKER_COUNT}" -var "envFile=$HOME/hermes/.env" -var "statusWorkerCount=${STATUS_COUNT}"
#fi

# Remove release archive
rm "$releasedir/release.tar.gz"

echo "Cleaning up old releases"

# Minimum number of folders  before we begin to remove old ones
minfolders=4

# Date two weeks ago
twoweeksago=$(date --date="2 weeks ago" +"%s")

cd $releasedir

# Number of releases we could detect
numfolders=$(find ./* -maxdepth 0 -type d | wc -l)

if [ "$numfolders" -gt "$minfolders" ]
then
    for rel in $(find ./* -maxdepth 0 -type d)
    do
        # Trim the './' that find returns with all directories
        trimmed="${rel:2}"
        # Make sure directory name is integer
        if [ "$trimmed" -eq "$trimmed" ] 2> /dev/null
        then
            # If more than two weeks ago, remove.
            if [ "$trimmed" -lt "$twoweeksago" ] && [ "$numfolders" -gt "$minfolders" ]
            then
                sudo rm -rf "$trimmed"
                let numfolders=$numfolders-1
            fi
        fi
    done
fi

# Self destruct
rm "$releasedir/release.sh"

echo "Done releasing."
