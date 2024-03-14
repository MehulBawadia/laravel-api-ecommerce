@servers(['web' => 'username@ip_address -p <port-number>'])

@task('deploy')
    {{-- Replace username and domain-name with the respective values --}}
    cd /home/username/domains/domain-name
    echo "Inside domain-name directory..."

    rm -rf vendor/
    echo "Removed vendor/ directory"

    git reset --hard origin/main
    echo "Removed any untracked files and/or folders"

    git pull origin main

    composer2 install --optimize-autoloader

    php artisan migrate --force

    php artisan config:clear
    php artisan route:clear
    php artisan view:clear

    php artisan config:cache
    php artisan route:cache
    php artisan view:cache

    php artisan scribe:generate

    echo "Check https://domain-name"
@endtask
