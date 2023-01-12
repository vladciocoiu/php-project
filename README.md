# vlad-national-library

## About
This is my project for the back-end development in PHP course at University. It is a library management app.

## Links
* [Hosted Website](https://vlad-national-library.000webhostapp.com/project/)
* [Presentation](https://prezentare-proiect-php.netlify.app/)

## Features
 - User authentication using session + validation by accessing a link sent through email
 - CAPTCHA boxes for login and register forms
 - Regular users can borrow books, see their borrowings and all available items
 - Admin users can add new items, delete existing items, update quantity of items, extend borrowings' due dates, mark borrowings as returned
 - Admins can see the total number of visits on the home page, and the number of unique visitors
 - Contact form where users can send emails
 - Users can generate PDF entry permits
 - Users can see Goodreads ratings and links for all books

## Stack
* ```PHP```
* ```Twig``` templating engine 
* ```MySQL``` database
* PHP frameworks such as:
    * ```FPDF``` for generating PDFs
    * ```PHPMailer``` for sending emails
    * ```PHPDotEnv``` for using environment variables