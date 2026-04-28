# 🛒 kubernetes-workloads-lab

A progressive hands-on lab that takes a PHP order form application from a **Dockerised multi-container setup** all the way to a full **Kubernetes deployment via Helm**.

---

## 📋 Table of Contents

- [Overview](#overview)
- [Tech Stack](#tech-stack)
- [Project Structure](#project-structure)
- [How It Works](#how-it-works)
- [Getting Started](#getting-started)
  - [Prerequisites](#prerequisites)
  - [Running with Docker Compose](#running-with-docker-compose)
  - [Deploying with Helm](#deploying-with-helm)
- [Helm Chart — `Chart/`](#helm-chart--chart)
  - [Chart Overview](#chart-overview)
  - [Kubernetes Resources](#kubernetes-resources)
  - [Configuration Reference](#configuration-reference)
- [Future Improvements](#future-improvements)

---

## Overview

**kubernetes-workloads-lab** is a pet project built to practice containerisation and Kubernetes orchestration hands-on. It starts with a simple PHP order form backed by a MySQL database, each running in its own Docker container, and then progresses to a fully templated Helm chart for deploying the same stack on Kubernetes.

**What the app does:**
- Presents an order form to the user
- Collects customer and order details (via `Customer.php` and `OrderItem.php` domain classes)
- Persists submitted data to a MySQL database using PDO

---

## Tech Stack

| Layer | Technology |
|---|---|
| Application | PHP 7.4 (Apache) |
| Database | MySQL 8.0 |
| Containerisation | Docker |
| Local Orchestration | Docker Compose |
| Kubernetes Packaging | Helm v3 |
| Dependency Management | Composer |
| Testing | PHPUnit |

---

## Project Structure

```
kubernetes-workloads-lab/
├── Chart/                          # Helm chart for Kubernetes deployment
│   ├── Chart.yaml                  # Chart metadata (name: php-app, v0.1.0)
│   ├── values.yaml                 # Default configuration values
│   └── templates/
│       ├── deployments.yaml        # PHP app Deployment (2 replicas)
│       ├── mysql-statefulset.yaml  # MySQL StatefulSet + headless Service
│       ├── service.yaml            # ClusterIP Service for the PHP app
│       ├── ingress.yaml            # Ingress (togglable via values)
│       ├── configmap.yaml          # Non-sensitive env vars (host, db name, user)
│       └── secret.yaml             # Sensitive env vars (MySQL password)
├── config/
│   └── db.php                      # PDO database connection (reads from env vars)
├── src/
│   └── Order/
│       ├── Customer.php            # Customer domain class
│       └── OrderItem.php           # Order item domain class
├── index.php                       # Application entry point
├── Dockerfile                      # PHP 7.4 Apache image definition
├── compose.yaml                    # Docker Compose service definitions
├── composer.json                   # PHP dependencies
└── phpunit.xml                     # PHPUnit test configuration
```

---

## How It Works

```
[Browser]
    │
    ▼
[Ingress]  ──→  [php-app Service (ClusterIP :8500)]
                        │
                        ▼
              [PHP Deployment — 2 replicas]
                        │  (env from ConfigMap + Secret)
                        ▼
              [MySQL Headless Service]
                        │
                        ▼
              [MySQL StatefulSet — 1 replica]
                        │
                        ▼
              [PersistentVolumeClaim — 1Gi]
```

The PHP app reads its database connection settings from environment variables injected by a **ConfigMap** (host, user, db name) and a **Secret** (password), keeping sensitive data separate from the application image.

---

## Getting Started

### Prerequisites

- [Docker](https://docs.docker.com/get-docker/) with Docker Compose v2
- [kubectl](https://kubernetes.io/docs/tasks/tools/) configured against a cluster (e.g. Minikube, Kind, or a cloud cluster)
- [Helm v3](https://helm.sh/docs/intro/install/)

---

### Running with Docker Compose

The quickest way to run the app locally:

```bash
# Clone the repository
git clone https://github.com/preetcoder/kubernetes-workloads-lab.git
cd kubernetes-workloads-lab

# Build and start all services
docker compose up --build

# Open in browser
open http://localhost:8080
```

To stop:

```bash
docker compose down          # stop containers
docker compose down -v       # stop and wipe the database volume
```

**Services started:**

| Service | Image | Port |
|---|---|---|
| `app` | Built from `Dockerfile` (PHP 7.4 Apache) | `8080` → `80` |
| `db` | `mysql:8.0` | Internal only |

Live file sync is enabled via Docker Compose Watch — changes to source files sync into the container without a full rebuild.

---

### Deploying with Helm

#### 1. Add the local hostname (for Ingress)

The chart's default Ingress host is `localk8slab.com`. Add it to your `/etc/hosts`:

```bash
echo "127.0.0.1 localk8slab.com" | sudo tee -a /etc/hosts
```

#### 2. Install the chart

```bash
helm install php-app ./Chart
```

#### 3. Verify the deployment

```bash
kubectl get pods
kubectl get svc
kubectl get ingress
```

#### 4. Upgrade after changes

```bash
helm upgrade php-app ./Chart
```

#### 5. Uninstall

```bash
helm uninstall php-app
```

> ⚠️ **Note on the Secret:** The default `values.yaml` contains a plaintext password for local development. In production, override this with a proper secrets manager or use `--set appSecret.values[0].value=<your-password>` at install time. Never commit real credentials to source control.

---

## Helm Chart — `Chart/`

### Chart Overview

| Field | Value |
|---|---|
| Chart name | `php-app` |
| Chart version | `0.1.0` |
| App version | `1.0.0` |
| Type | `application` |

### Kubernetes Resources

| Template | Kind | Purpose |
|---|---|---|
| `deployments.yaml` | `Deployment` | Runs 2 replicas of the PHP/Apache app |
| `mysql-statefulset.yaml` | `StatefulSet` + `Service` | Runs MySQL with a 1Gi persistent volume; headless Service enables stable DNS |
| `service.yaml` | `Service` (ClusterIP) | Exposes the PHP app internally on port `8500` |
| `ingress.yaml` | `Ingress` | Routes external traffic to the app (toggleable) |
| `configmap.yaml` | `ConfigMap` | Injects `MYSQL_HOST`, `MYSQL_USER`, `MYSQL_DB` into the app |
| `secret.yaml` | `Secret` (Opaque) | Injects `MYSQL_PASSWORD` into both the app and the MySQL StatefulSet |

### Configuration Reference

All values live in `Chart/values.yaml` and can be overridden at install time with `--set` or a custom values file.

```yaml
# Deployment
appDeployment:
  replicas: 2
  image: "codepreet/docker_php_multi_containers"
  imageTag: "03"
  pullPolicy: IfNotPresent

# App Service
appService:
  name: php-app-service
  port: 8500
  targetPort: 80
  serviceType: ClusterIP

# Ingress
ingress:
  enabled: true
  hosts:
    - host: localk8slab.com   # ← add to /etc/hosts for local use
      paths:
        - path: /
          pathType: Prefix

# MySQL StatefulSet
appStateFullSet:
  name: mysql-statefulset
  serviceName: mysql-service
  image: mysql
  imagTag: "8.0"
  volumeMountPath: "/var/lib/mysql"
  volumeName: "mysql-storage"   # PVC size: 1Gi

# ConfigMap (non-sensitive)
appConfigMaps:
  - key: MYSQL_HOST
    value: mysql-service
  - key: MYSQL_USER
    value: root
  - key: MYSQL_DB
    value: mobile

# Secret (sensitive)
appSecret:
  type: Opaque
  values:
    - key: MYSQL_PASSWORD
      value: secret  # ← override in production!
```

**Override example — scale up and use a custom image tag:**

```bash
helm upgrade php-app ./Chart \
  --set appDeployment.replicas=3 \
  --set appDeployment.imageTag=04
```

---

## Future Improvements

- [ ] **Replace plaintext Secret with external secrets management** (e.g. HashiCorp Vault, AWS Secrets Manager, or the External Secrets Operator)
- [ ] **Add Horizontal Pod Autoscaler (HPA)** to scale the PHP Deployment automatically based on CPU/memory usage
- [ ] **Add liveness and readiness probes** to the PHP container so Kubernetes can self-heal unhealthy pods
- [ ] **Add resource limits to MySQL StatefulSet** — currently only the PHP Deployment has CPU/memory limits defined
- [ ] **Add a Helm `_helpers.tpl`** to centralise label and name generation following Helm best practices
- [ ] **Publish the chart to a Helm repository** (e.g. GitHub Pages + `cr` or OCI registry) for easy sharing and versioning
- [ ] **Add a CI/CD pipeline** (GitHub Actions) to lint the Helm chart (`helm lint`), run PHPUnit tests, build and push the Docker image, and auto-deploy on merge
- [ ] **Add Ingress TLS support** — the `ingress.tls` block is already present in `values.yaml` but currently empty
- [ ] **Add an order dashboard** to list, edit, and delete submitted orders in the PHP UI
- [ ] **Add form validation** on both client and server side
- [ ] **Add application logging and structured error handling** in the PHP layer
- [ ] **Support multiple environments** (dev / staging / prod) via separate Helm values files (e.g. `values.prod.yaml`)

---

## Author

**Harpreet Singh** — [@preetcoder](https://github.com/preetcoder)

---

*Built for learning. Contributions and feedback welcome.*
