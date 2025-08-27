# multivendor-ecommerce
A complete solution for multivendor eCommerce business!

## Installation and Run with Docker

Clone the repo and run docker compose:

```bash
git clone https://github.com/arm-technologies/multivendor-ecommerce.git
cd multivendor-ecommerce
sudo docker compose up
```


## Installation and Run (xampp/lampp must be installed on your device)

Clone the repo into htdocs or var/www/html:

```bash
git clone https://github.com/arm-technologies/multivendor-ecommerce.git
cd multivendor-ecommerce
```

Copy .env.example file and change database configurations:

```bash
cp .env.example .env
```

Run migration and seeder:

```bash
php artisan migrate --seed
```
