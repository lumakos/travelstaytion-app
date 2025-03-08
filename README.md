# Few words about the application

This is a **RESTful API** built with **Laravel 10**, featuring **Sanctum authentication** and **MongoDB** as the database. It provides **lead management** functionality and integrates with **ActiveCampaign** to synchronize contacts seamlessly.

---

## Features

- **User Authentication** with **Laravel Sanctum** (Register).
- **Lead Management API**: CRUD endpoints for **creating, retrieving, updating, and deleting leads**.
- **Data Validation** and **secure password storage**.
- **Logging & Error Handling** to capture failed operations.

---

## Technologies Used

- **Laravel 10** (Backend Framework)
- **MySQL** (SQL Database)
- **Laravel Sanctum** (Token-based Authentication)
- **Laravel Queues & Horizon** (Job Processing)

---

## Installation & Setup

<u>**Note**</u>: You can find the <u>learnworlds-assignment.postman_collection.json</u> in the project root, which contains all the HTTP requests for this assignment. Download the file, import it into Postman, and run the requests.

## 1. Clone the Repository
```
git clone git@github.com:lumakos/travelstaytion-app.git
cd travelstaytion-app
```

## 2. Install Dependencies
```
composer install
```

## 3. Build and start the app
```
./vendor/bin/sail up -d --build
```

## 4. Copy envs
```
cp .env.example .env
```

## 5. Run migrtions
```
./vendor/bin/sail artisan migrate --seed
```

## 6. Key generations
```
./vendor/bin/sail artisan key:generate
```
#### In case of permission issues, run
```
sudo chmod -R 755 travelstaytion-app
```

## 7. Re-run the app
```
./vendor/bin/sail up -d --build
```

#### Running host
```
http://localhost:100
```

#### Get Access Token
```
email: test@example.com
password: password
```


## 8. Run Horizon
```
./vendor/bin/sail artisan horizon
```

## 9. Run Queue Worker
```
./vendor/bin/sail artisan queue:work
```

## 10. Run Tests
```
./vendor/bin/sail artisan test
```
