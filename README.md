# SRL Dockerised Order Form

SRL is a small pet project built to test a Dockerised environment with multiple containers. It uses one container for the PHP application and another for the MySQL database, then saves order form submissions into the database.

## Project Overview

This project demonstrates how a simple PHP app can run in a containerised setup while persisting data in a separate MySQL container. The app presents an order form, collects customer and order details, and stores the submitted data in MySQL.

## Features

- PHP-based web application.
- Separate MySQL container for data storage.
- Order form to collect customer details.
- Saves submitted form data into MySQL.
- Good starting point for testing Docker networking, persistence, and service separation.

## Tech Stack

- PHP
- MySQL
- Docker
- Docker Compose

## How It Works

1. The user opens the order form in the PHP container.
2. The form collects customer and order information.
3. On submit, the PHP app sends the data to the MySQL container.
4. The database stores the submitted record for later use.

## Container Setup

The project uses two containers:

- `app`: runs the main PHP application.
- `db`: runs the MySQL server and stores the form data.

These containers communicate over a shared Docker network, allowing the PHP app to connect to MySQL using the database service name.

## Purpose of the Project

This project is mainly for learning and testing Kubernetes deployment which will be done in next step:

- Docker container communication.
- Multi-container application setup.
- PHP and MySQL integration.
- Form handling and database persistence.
- Basic project structure for future expansion.

## Running the Project

1. Build the containers.
2. Start the services with Docker Compose.
3. Open the app in a browser.
4. Fill in the order form and submit it.
5. Check the MySQL database to confirm the data was saved.

## Future Improvements

- Add form validation.
- Add edit and delete actions.
- Show saved orders on a dashboard.
- Add logging and error handling.
- Use environment variables for all database settings.
- Use Kubernetes to Orchestrate containers
