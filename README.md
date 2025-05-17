# **SMarket**

A PHP & MySQL-based web application developed for CTIS256, designed to reduce food and product waste
by facilitating the sale of near-expiry items at discounted prices.

## A) Project Summary :

SMarket allows market users to upload their soon-to-expire inventory with special discounts,
while consumer users can browse these products filtered by city and district.
This promotes both sustainability and cost-efficiency by minimizing waste and supporting smart shopping.

## B) User Roles:

### 1. Market Users

* Register & login
* Add / edit / delete products
* Upload product images
* View products in dashboard
* Update profile details

### 2. Consumer Users (my part)

* Register & login
* Search products by keyword
* View only available & non-expired products from own city
* Add products to cart
* Update quantities / delete items from cart
* Complete purchases (via AJAX)
* Update profile info

## C) Technologies Used

* **Backend:** PHP
* **Database:** MySQL
* **Frontend:** HTML, CSS, JS
* **Security:** Session-based access control, password hashing, input sanitization
* **Functionality Enhancements:**

  * Smart search with partial matches
  * Auto-discount system based on expiration date
  * Expired product filtering
  * Pagination on product list
  * AJAX-based cart and purchase system
