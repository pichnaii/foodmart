It is a free HTML CSS template by https://templatesjungle.com/
You can use this template as a starter template and start building as you require.

The code is consistent and can be easily maintained as we have followed a good coding standard. We want everyone to easily understand it and modify it according to their requirement. As the main goal of providing these templates is to give you something to work on before even starting.

Preview URL: 
https://demo.templatesjungle.com/foodmart/

Get the Full Version here: 
https://templatesjungle.gumroad.com/l/foodmart-bootstrap-html-template


FREE FOR BOTH PERSONAL AND COMMERCIAL USE

This HTML Template is provided by TemplatesJungle.com and is free to use in both personal and commercial projects as long as you don't remove our credit link in the footer.

However, you can remove the credit link by paying for No Attribution version of the template.


RIGHTS

You are allowed to use it in your personal projects and commercial projects.

You can modify and sell it to your clients.


PROHIBITIONS

You cannot remove the credit link which links back to templatesjungle.com.

You are not permitted to resell or redistribute (paid or free) as it is. 

You cannot use it to build premium templates, themes or any other goods to be sold on marketplaces.

If you want to share the free resource in your blog, you must point it to original TemplatesJungle.com resource page. 

You cannot host the download file in your website.


SUPPORT

You can contact us to report any bugs and errors in the template. We will try and fix them immediately although it's a free resource.

Feel free to let us know about what you want to see in the future downloads. We will definitely give it a thought while creating our next freebie.


CREDITS & REFERENCES

https://getbootstrap.com/

Stock Photos
https://unsplash.com/
https://www.freepik.com/
https://www.pexels.com/

Fonts
Google fonts
https://fonts.google.com/

Icons
https://icon-sets.iconify.design/

Bootstrap Framework
https://getbootstrap.com/

JQuery Plugins

Swiper Slider - https://swiperjs.com/
Chocolat.js – a Free Lightbox Plugin -http://chocolat.insipi.de/
Magnific Lightbox - https://github.com/dimsemenov/Magnific-Popup

Thanks for downloading from TemplatesJungle.com !



how to push code to github
1 git status 
2 git add . or git add path\to\file.php (add specific files)
3 git commit -m "Describe what you changed"
4 git push origin main OR git push origin master (if our brand is master)


Quick Tips
1 Always pull first (if other people also work on the repo) git pull origin main
2 Check which branch you are on: git branch 
3 Add a .gitignore if you haven’t yet, so you don’t push things like vendor/, .env, or node_modules/.



// database create table cutomer 
CREATE TABLE `customer` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(50) NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `company` VARCHAR(100),
    `phone` VARCHAR(30),
    `email` VARCHAR(60),
    `address` VARCHAR(255),
    `description` VARCHAR(255),
    `created_date` DATE
);

// database create table supplier 
CREATE TABLE `supplier` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(50) NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `company` VARCHAR(100),
    `phone` VARCHAR(30),
    `email` VARCHAR(60),
    `address` VARCHAR(255),
    `description` VARCHAR(255),
    `created_date` DATE
);

// database create table currency 
CREATE TABLE `currency` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `base_currency` VARCHAR(50) NOT NULL,
    `currency_code` VARCHAR(100) NOT NULL,
    `currency_name` VARCHAR(100),
    `currency_symbol` VARCHAR(30),
    `exchange_rate` VARCHAR(60),
    `created_date` DATE
);

// database create table warehouse 
CREATE TABLE `warehouse` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(50) NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `local_name` VARCHAR(100),
    `address` LONGTEXT NOT NULL,
    `note` LONGTEXT NOT NULL,
    `created_date` DATE
);

// database create table company 
CREATE TABLE `company` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(50) NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `local_name` VARCHAR(100),
    `phone` VARCHAR(100),
    `email` VARCHAR(100),
    `vat` VARCHAR(100),
    `logo` VARCHAR(100),
    `local_address` LONGTEXT NOT NULL,
    `address` LONGTEXT NOT NULL,
    `note` LONGTEXT NOT NULL,
    `created_date` DATE
);

-- Permissions table (defines all available permissions)
CREATE TABLE permissions (
    id   INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,  -- e.g. 'warehouse_edit'
    label VARCHAR(150)                  -- e.g. 'Can Edit Warehouse'
);

CREATE TABLE user_permissions (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    user_id       INT NOT NULL,
    permission_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_perm (user_id, permission_id)
);

CREATE TABLE user_roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL,
    name VARCHAR(255) NOT NULL UNIQUE,
    updated_date DATE,
    created_date DATE,
    description TEXT NULL,
    status INT(11)
)