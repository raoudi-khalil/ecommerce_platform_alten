# 🛒 ALTEN E-commerce Platform — Symfony 7 REST API

Cette plateforme e-commerce est une application back-end RESTful développée avec **Symfony 7.2**. Elle intègre un système d’authentification sécurisé via JWT, un module complet de gestion de panier et de favoris, ainsi qu’un back-office simplifié pour la gestion des produits. La documentation des endpoints est générée automatiquement à l’aide de **NelmioApiDocBundle (Swagger UI)**.

---

## 📌 Objectifs du projet

- Fournir une API modulaire, extensible et sécurisée.
- Gérer l'intégralité du cycle utilisateur : inscription, connexion, panier, favoris.
- Permettre l’administration des produits via des endpoints sécurisés.
- Documenter automatiquement les routes pour permettre une intégration rapide.

---

## 🧰 Stack technique

| Élément                 | Version / Outil                     |
|------------------------|-------------------------------------|
| Langage                | PHP 8.2+                            |
| Framework              | Symfony 7.2                         |
| ORM                    | Doctrine ORM                        |
| Authentification       | JWT (`firebase/php-jwt`)            |
| Documentation API      | NelmioApiDocBundle (Swagger)        |
| Base de données        | MySQL (ou autre via Doctrine)       |
| Sérialisation          | Symfony Serializer + `@Groups`     |
| Architecture           | MVC + Service Layer (managers)      |

---

## 📁 Structure du projet (backend Symfony uniquement)

src/ ├── Controller/ # Contrôleurs REST (Auth, Cart, Wishlist, Product)
 ├── Entity/ # Entités Doctrine : User, Product, Cart, wishlist.
 ├── Enum/ # Enumération : InventoryStatus
 ├── Model/ # DTO pour les entrées utilisateurs
 ├── Repository/ # Requêtes personnalisées
 ├── Security/ # Authentificateur JWT personnalisé 
 ├── Service/ # Couche métier : managers spécialisés 
 └── Kernel.php # Fichier d'entrée Symfony


---

## 🔐 Authentification JWT

### 🔑 Endpoints disponibles

| Méthode | URL        | Description                                 |
|---------|------------|---------------------------------------------|
| POST    | `/account` | Création de compte utilisateur              |
| POST    | `/token`   | Connexion utilisateur (génère un JWT Token) |

> ✅ Une fois connecté, les requêtes protégées nécessitent un header :
>
> ```
> Authorization: Bearer <token>
> ```

---

## 🔄 Endpoints principaux (Swagger `/api/doc`)

### 📦 Produits

| Méthode | Endpoint            | Accès       | Description                     |
|---------|---------------------|-------------|---------------------------------|
| GET     | `/products`         | Public      | Liste des produits              |
| GET     | `/products/{id}`    | Public      | Détails d’un produit            |
| POST    | `/products`         | Admin only  | Création d’un produit           |
| PATCH   | `/products/{id}`    | Admin only  | Modification d’un produit       |
| DELETE  | `/products/{id}`    | Admin only  | Suppression d’un produit        |

### 🛒 Panier

| Méthode | Endpoint                       | Description                              |
|---------|--------------------------------|------------------------------------------|
| GET     | `/cart`                        | Récupérer le panier actuel               |
| POST    | `/cart/add/{productId}`        | Ajouter un produit au panier             |
| DELETE  | `/cart/items/{cartItemId}`     | Supprimer un article du panier           |
| PATCH   | `/cart/items/{cartItemId}`     | Modifier la quantité d’un article        |
| DELETE  | `/cart`                        | Vider entièrement le panier              |

### ❤️ Wishlist

| Méthode | Endpoint                       | Description                              |
|---------|--------------------------------|------------------------------------------|
| GET     | `/wishlist`                    | Récupérer tous les articles favoris      |
| POST    | `/wishlist/products/{id}`      | Ajouter un produit à la wishlist         |
| DELETE  | `/wishlist/products/{id}`      | Retirer un produit de la wishlist        |

---

## 📄 Documentation API

La documentation Swagger est accessible à l’adresse :

> 🔗 [http://localhost:8000/api/doc](http://localhost:8000/api/doc)

---

## ⚙️ Démarrage rapide

```bash
git clone https://github.com/raoudi-khalil/ecommerce_platform_alten.git
cd ecommerce_platform_alten
composer install
cp .env .env.local
# Configurez la base de données dans .env.local
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console server:run

