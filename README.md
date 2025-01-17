
# Project Documentation

## Table of Contents
1. [Introduction](#introduction)
2. [Installation](#installation)
3. [Usage](#usage)

---

## Introduction

---

## Installation
Follow these steps to install the project:
- Necessary to Install Docker and Composer
1. Clone the repository:
   ```bash
   git clone https://github.com/xging/CurrencyAppSymfony.git
   
2. Build and start containers with Docker Compose
   ```bash
   docker-compose up --build
   
3. In project folder "CurrencyAppSymfony" open PHP container and install composer.
   ```bash
   docker exec -it symfony-php-container bash
     composer install
   
4. Change API Key to yours from (https://app.freecurrencyapi.com/dashboard) 
   ```bash
    File to change, in root folder: .env.dev:
    Line to change: CURRENCY_API_KEY=YOUR_KEY
   
5. Run migration
   ```bash
   docker exec -it symfony-php-container bash
     php bin/console doctrine:migrations:diff
     php bin/console doctrine:migrations:migrate

## Usage
### * In order to run the async process, you need to run the consumer (no need to run the consumer for sync messages)
### * Execute commands in Docker PHP container
    docker exec -it symfony-php-container bash
    
0. Run RabbitMQ сonsumer
   ```bash
   php bin/console messenger:consume -vv
1. Add Currency pairs (Async)
   ```bash
   php bin/console app:add-pair "GBP EUR"
2. Remove Currency pairs (Async)
   ```bash
   php bin/console app:remove-pair "GBP EUR"
3. Show Currency rate pair (Sync)
   ```bash
   php bin/console app:show-pair "GBP EUR" 
4. Run&Watch Currency rate pairs (Sync)
   ```bash
   php bin/console app:watch-pair

## HTTP Request
### * Redis is used to cache request output.
0. Check Redis Keys:
   ```bash
   docker exec -it symfony-redis-container redis-cli
     KEYS *
   
1. Find current exchange rate by selected currencies:
   ```bash
   http://localhost:8080/get-currency-rate/GBP/EUR

2. Find history records by selected currencies:
   ```bash
   http://localhost:8080/get-currency-hist/GBP/EUR/

3. Find history records by selected currencies and date (YYYY-MM-DD):
   ```bash
   http://localhost:8080/get-currency-hist/GBP/EUR/2025-01-17/

4. Find history records by selected currencies, date (YYYY-MM-DD), and time (HH:MM:SS):
   ```bash
   http://localhost:8080/get-currency-hist/GBP/EUR/2025-01-17/10:35:05

