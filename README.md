cd d:
cd laragon/www/

git clone https://github.com/MaungTheikdi/jobportal

env setup 

composer install 

php artisan key:generate

sudo chmod -R 775 storage
sudo chmod -R 775 bootstrap/cache


npm install

php artisan serve --host=192.168.1.19 --port=8000

# check the ip with ipconfig

# vit.config.js
server: {
	host: '192.168.99.80', // // Listen on all network interfaces
	port: 5173,      // Or whatever port you want
},

# .env
APP_URL=http://192.168.99.80:8000






🧭 3. Clear and Rebuild Config Cache
After editing .env, run:
php artisan cache:clear
php artisan config:clear
php artisan config:cache

php artisan migrate
php artisan migrate:fresh

### php artisan serve --host=192.168.99.80 --port=8000
### npm run dev
### Browser -> http://192.168.99.80:8000/



# Login
# theikdimaung@mail.com
# password

# jobportal
