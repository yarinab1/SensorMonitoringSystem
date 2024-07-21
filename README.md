# A-Tower Sensor Monitoring System

Welcome to the A-Tower Sensor Monitoring System! This project helps monitor temperature sensors installed on the glass windows of the A-Tower. It provides functionalities for tracking sensor malfunctions and viewing aggregated temperature data.

## Getting Started

Follow these steps to get the system up and running:

### 1. Install Docker

- Download and install Docker from the [official Docker website](https://www.docker.com/get-started).

### 2. Clone the Repository

- Clone the repository using Git:
  ```sh
  git clone https://github.com/yarinab1/SensoMonitoringSystem.git

### 3. Build and Start the Docker Containers

- Navigate to the root folder of the cloned repository:
  ```sh 
   cd SensoMonitoringSystem
  
- Build the Docker images:
  ```sh 
  docker-compose build

- Start the Docker containers:
  ```sh 
  docker-compose up
  
### 4. Verify Service Status
- Wait until Docker completes the startup process. You should see that the service sensomonitoringsystem-set-data-2-1 is running. This service executes the script for setting data in the database.

### 5. Access the Application
- **Backend**: The backend service is accessible at http://localhost:3030.
- **Frontend**: The frontend application is available at http://localhost.

## Notes
Ensure Docker is running properly on your system before starting the containers.
The system uses Docker Compose to manage the services, including a web application, a frontend, and a MySQL database.
If you encounter issues with Docker or the services, refer to Docker documentation or seek help in the repository’s issue tracker.
Thank you for using the A-Tower Sensor Monitoring System. We hope you find it both useful and engaging!