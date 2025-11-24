# check the ip with ipconfig

# vit.config.js
server: {
	host: '192.168.99.80', // // Listen on all network interfaces
	port: 5173,      // Or whatever port you want
},

# .env
APP_URL=http://192.168.99.80:8000

### php artisan serve --host=192.168.99.80 --port=8000
### npm run dev
### Browser -> http://192.168.99.80:8000/


# Login
# theikdimaung@mail.com
# password


🧭 3. Clear and Rebuild Config Cache
After editing .env, run:
# php artisan config:clear
# php artisan config:cache
php artisan migrate
php artisan migrate:fresh

# jobportal
